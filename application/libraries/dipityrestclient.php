<?php
/*
 * Copyright (c) 2008, Underlying, Inc.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 * + Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 *
 * + Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in
 *   the documentation and/or other materials provided with the
 *   distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

//require_once('PEAR/Exception.php');

/**
 * PHP client for the Dipity REST API.  Provides methods for managing
 * timeline and events objects in the Dipity service.  Methods may
 * throw PEAR_Exception when things go wrong.
 *
 * @version 0.4
 */
class DipityRestClient {
  private $key_;
  private $secret_;
  private $server_;
  private $payload_;
  private $location_;
  private $uploadFile_ = '';

  /**
   * Construct an object to wrap the Dipity REST API.
   *
   * @param   string            $key           API key from the developer registration page
   * @param   string            $secret        secret from the developer registration page
   */
  public function DipityRestClient() {
    $this->key_ = "b14dc89273c65d0a7ff122d0d4d62a59";
    $this->secret_ = "81c315631c60296f0d8dccd22430583e";
	$server = '';
    $this->server_ = (strlen($server) ? $server : "api.dipity.com");
  }

  /**
   * Creates a timeline in the Dipity service.
   *
   * @param   DipityTimeline    $timeline      timeline object to create in Dipity
   */
  public function createTimeline(DipityTimeline &$timeline) {
    $this->validate($timeline->data_, array('title', 'public'));
    $this->request(array('timelines'), $timeline->data_, "POST");
    $timeline->setTid($this->getLocationId());
  }

  /**
   * Retrives the Dipity timelines of the Developer.
   *
   * @return  array                            array of DipityTimeline objects
   */
  public function getTimelines() {
    $this->request(array('timelines'), 0, "GET");
    $entity = json_decode($this->payload_, 1);
    $timelines = array();
    foreach ($entity['timelines'] as $data) {
      $timelines[] = new DipityTimeline($data['timeline']);
    }
    return $timelines;
  }

  /**
   * Retrieves a specified timeline.
   *
   * @param   string            $tid           timeline ID to retrieve
   * @return  DipityTimeline                   object representing the timeline
   */
  public function getTimeline($tid) {
    $this->request(array('timelines', $tid), 0, "GET");
    $entity = json_decode($this->payload_, 1);
    $data = $entity['timeline'];
    return new DipityTimeline($data);
  }

  /**
   * Updates a specified timeline.
   *
   * @param   DipityTimeline    $timeline      timeline to update
   */
  public function updateTimeline(DipityTimeline &$timeline) {
    $this->validate($timeline->data_, array('title', 'public'));
    $this->request(array('timelines', $timeline->getTid()), $timeline->data_, "PUT");
  }

  /**
   * Deletes a specified timeline.
   *
   * @param   DipityTimeline    $timeline      timeline to delete
   */
  public function deleteTimeline(DipityTimeline &$timeline) {
    $this->request(array('timelines', $timeline->getTid()), 0, "DELETE");
  }

  /**
   * Uploads an image for a specified timeline.
   *
   * @param   DipityTimeline    $timeline      timeline where image will be uploaded
   * @param   string            $file          path to image file to uplaod
   */
  public function uploadTimelineImage(DipityTimeline &$timeline, $file) {
    $this->uploadFile($file);
    $this->request(array('timelines', $timeline->getTid(), 'image'), 0, "POST");
    $timeline->data_['image_url'] = $this->getLocation();
  }

  /**
   * Creates an event in the Dipity service.
   *
   * @param   DipityEvent       $event         the event object to create in Dipity
   */
  public function createEvent(DipityEvent &$event) {
    $this->validate($event->data_, array('title', 'timestamp'));
    $this->request(array('events'), $event->data_, "POST");
    $event->setEid($this->getLocationId());
  }

  /**
   * Retrieves a specified event.
   *
   * @param   string            $eid           event ID to retrieve
   * @return  DipityEvent                      object representing the event
   */
  public function getEvent($eid) {
    $this->request(array('events', $eid), 0, "GET");
    $entity = json_decode($this->payload_, 1);
    $data = $entity['event'];
    return new DipityEvent($data);
  }

  /**
   * Updates a specified event.
   *
   * @param   DipityEvent       $event         event to update
   */
  public function updateEvent(DipityEvent &$event) {
    $this->validate($event->data_, array('title', 'timestamp'));
    $this->request(array('events', $event->getEid()), $event->data_, "PUT");
  }

  /**
   * Deletes a specified event.
   *
   * @param   DipityEvent       $event         event to delete
   */
  public function deleteEvent(DipityEvent &$event) {
    $this->request(array('events', $event->getEid()), 0, "DELETE");
  }

  /**
   * Uploads an image for a specified event.
   *
   * @param   DipityEvent       $event         event where image will be uploaded
   * @param   string            $file          path to image file to uplaod
   */
  public function uploadEventImage(DipityEvent &$event, $file) {
    $this->uploadFile($file);
    $this->request(array('events', $event->getEid(), 'image'), 0, "POST");
    $event->data_['image_url'] = $this->getLocation();
  }

  /**
   * Adds an event to a timeline.
   *
   * @param   DipityTimeline    $timeline      timeline where event will be added
   * @param   DipityEvent       $event         event to add to timeline
   * @param   int               $score         score for the event on specified timeline
   */
  public function addTimelineEvent(DipityTimeline &$timeline, DipityEvent &$event, $score = 0) {
    $data = array('event_id' => $event->getEid(), 'score' => $score);
    $this->request(array('timelines', $timeline->getTid(), 'events'), $data, "POST");
  }

  /**
   * Retrieves the set of events for a specified timeline.
   *
   * @param   DipityTimeline    $timeline      timeline of events to retrieve
   * @return  array                            array of DipityEvent objects
   */
  public function getTimelineEvents(DipityTimeline &$timeline) {
    $this->request(array('timelines', $timeline->getTid(), 'events'), 0, "GET");
    $entity = json_decode($this->payload_, 1);
    $events = array();
    foreach ($entity['events'] as $data) {
      $events[] = new DipityEvent($data['event']);
    }
    return $events;
  }

  /**
   * Removes an event from a timeline.
   *
   * @param   DipityTimeline    $timeline      timeline to remove event from
   * @param   DipityEvent       $event         event to remove from timeline
   */
  public function removeTimelineEvent(DipityTimeline &$timeline, DipityEvent &$event) {
    $this->request(array('timelines', $timeline->getTid(), 'events', $event->getEid()), 0, "DELETE");
  }

  /**
   * Creates (or updates the timestamp) of a naming entry.
   *
   * @param   string            $text          text to associate with resource identifier
   * @param   string            $id            resource identifier to associate with text
   * @param   string (out)      $existingId    resource identifier already associated with text
   *
   * @return  boolean                          <pre>false</pre> if text was previously
   *                                           associated with a different ID.  The existing
   *                                           ID is returned in <pre>$existingId</pre>.
   */
  public function createOrUpdateNamingText($text, $id, &$existingId) {
    $data = array('text' => $text, 'id' => $id);
    try {
      $this->request(array('naming', 'text'), $data, "POST");
    }
    catch (PEAR_Exception $e) {
      if ($e->getCode() == 409) {
        $entity = json_decode($this->payload_, 1);
        $existingId = $entity['existing'];
        return false;
      }
      throw $e;
    }
    return true;
  }

  /**
   * Gets a naming entry using a text label.
   *
   * @param   string            $text          text label to lookup
   * @param   string (out)      $id            resource identifier to associate with text
   * @param   string (out)      $timestamp     create or last update time of the record
   */
  public function getNamingByText($text, &$id, &$timestamp) {
    $this->request(array('naming', 'text', $text), 0, "GET");
    $data = json_decode($this->getEntity(), 1);
    $id        = $data['entry']['id'];
    $timestamp = $data['entry']['timestamp'];
  }

  /**
   * Access to the entity returned from a REST HTTP operation.
   *
   * @return  string                           the stringified entity
   */
  public function getEntity() {
    return $this->payload_;
  }

  /**
   * Access to the <pre>Location:</pre> header in the HTTP response.
   *
   * @return  string                           URI to new resource location
   */
  public function getLocation() {
    return $this->location_;
  }

  /**
   * Obtain the identifier for an object created in the Dipity REST service.
   *
   * @return  string                           ID of new resource
   */
  public function getLocationId() {
    return array_pop(explode('/', $this->location_));
  }

  /**
   * Sets the path to a file which will be uploaded in the next request.
   *
   * @access  private                          
   * @param   string            $file          path name to file
   */
  private function uploadFile($file) {
    $this->uploadFile_ = $file;
  }

  /**
   * Validates the object wrapper's data used in a REST operation.  Throws
   * an exception if the object is not ready to be used for the operation.
   *
   * @access  private                          
   * @param   array             $data          associative array of internal object wrapper data
   * @param   array             $required      data members that are required for the operation
   */
  private function validate($data, $required) {
    $invalid = array();
    foreach ($required as $key) {
      if (!isset($data[$key])) {
        $invalid[] = $key;
      }
    }
    if (count($invalid)) {
      throw new PEAR_Exception("The following object properties must be set: '" . 
                               implode("', '", $invalid) . "'.");
    }
  }

  /**
   * Composes and executes an HTTP to the Dipity REST API.
   *
   * @access  private                          
   * @param   array             $resource      elements that define URI of REST resource
   * @param   array             $params        parameters for REST operation
   * @param   string            $method        HTTP method for REST operation
   */
  private function request($resource, $params, $method) {
    $url = 'http://' . $this->server_ . '/rest/' . implode('/', array_map("urlencode", $resource));
    $kv = array();
    if ($params) {
      foreach ($params as $k => $v) {
        $kv[] = $k . '=' . (($method == "POST" || $method == "PUT") ? $v : urlencode($v));
      }
    }

    $url .= sprintf("?key=%s&sig=%s", $this->key_, $this->getSignature($params));
    if (($method == "GET" || $method == "DELETE") && count($kv)) {
      $url .= '&' . implode('&', $kv);
    }

    $ch = curl_init($url);

    $headers = array("Accept: application/json");
    if ($method == "PUT") {
      $headers[] = "X-HTTP-Method-Override: PUT";
      $method = "POST";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($method == "POST") {
      curl_setopt($ch, CURLOPT_POST, true);
      if ($this->uploadFile_) {
        if (!$params) {
          $params = array();
        }
        $params['image'] = '@' . $this->uploadFile_;
        $this->uploadFile_ = '';
      }
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$this, "header"));
    $this->payload_ = curl_exec($ch);
    
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($code >= 400) {
      throw new PEAR_Exception($this->payload_, $code);
    }

    curl_close($ch);
  }

  /**
   * Callback provided to cURL to get headers out of the response.
   *
   * @access  private                          
   * @param   string            $ch            cURL resource handle
   * @param   string            $header        the stringified header
   * @return  int                              length of header as required by cURL
   */
  private function header($ch, $header) {
    if (preg_match('/^Location/', $header)) {
      list($name, $value) = explode(':', $header, 2);
      $value = trim($value);
      if ($name == "Location") {
        $this->location_ = $value;
      }
    }

    return strlen($header);
  }

  /**
   * Calculates a signature for the parameters of a Dipity REST service operation.
   *
   * @param   array             $param         the parameters of the REST operation
   * @return  string                           the signature of the REST request
   */
  private function getSignature($params) {
    $s = $this->secret_;
    if ($params) {
//       error_log("params = " . print_r($params, 1));
      ksort($params);
      foreach ($params as $k => $v) {
        $s .= $k . $v;
      }
    }
//     error_log("sig params = " . $s . "\n");
    $hash = md5($s);
//     error_log("signature  = " . $hash . "\n");
    return $hash;
  }
}

/**
 * Wrapper for timeline data retrieved from/sent to the Dipity service.
 */
class DipityTimeline {
  private $data_;
  private $tid_;

  /**
   * Overloading method to allow DipityRestClient access to this object's
   * internal state.
   */
  public function __get($key) 
  { 
    $trace = debug_backtrace(); 
    if(isset($trace[1]['class']) && $trace[1]['class'] == 'DipityRestClient') { 
      return $this->$key; 
    } 
    trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $key, E_USER_ERROR); 
  } 

  /**
   * Construct an object to wrap timeline data for the Dipity REST API.
   *
   * @param   array             $data          state of the timeline object
   */
  public function DipityTimeline(array &$data = array()) {
    $this->tid_ = $data['tid'];
    unset($data['tid']);
    $this->data_ = $data;
  }

  /**
   * Get the timeline ID of the timeline.
   *
   * @return  string                           timeline ID
   */
  public function getTid() {
    if (isset($this->tid_)) {
      return $this->tid_;
    }
    throw new PEAR_Exception("timeline id not set");
  }

  /**
   * Set the timeline ID of the timeline.
   *
   * @param   string            $tid           timeline ID
   */
  public function setTid($tid) {
    $this->tid_ = $tid;
  }

  /**
   * Get the title of the timeline.
   *
   * @return  string                           timeline title
   */
  public function getTitle() {
    return stripslashes($this->data_['title']);
  }

  /**
   * Set the title of the timeline.
   *
   * @param   string            $title         timeline title
   */
  public function setTitle($title) {
    $this->data_['title'] = $title;
  }

  /**
   * Get the description of the timeline.
   *
   * @return  string                           timeline description
   */
  public function getDescription() {
    return stripslashes($this->data_['description']);
  }

  /**
   * Set the description of the timeline.
   *
   * @param	string	$description		timeline description
   */
  public function setDescription($description) {
    $this->data_['description'] = $description;
  }

  /**
   * Get the timeline's image URL.
   *
   * @return  string                           URL to image associated with the timeline
   */
  public function getImageURL() {
    return $this->data_['image_url'];
  }

  /**
   * Set the timeline's image URL.
   *
   * @return  string                           URL to image associated with the timeline
   */
  public function setImageURL($url) {
    $this->data_['image_url'] = $url;
  }

  /**
   * Get whether the timeline is public (or private).
   *
   * @return  boolean                          <pre>true</pre> if publicly accessible
   */
  public function getPublic() {
    return $this->data_['public'] == "1" ? true : false;
  }

  /**
   * Set whether the timeline is public (or private).
   *
   * @param   boolean           $public        <pre>true</pre> if publicly accessible
   */
  public function setPublic($public) {
    $this->data_['public'] = $public ? "1" : "0";
  }

  /**
   * Get the timeline's category.
   *
   * @return  string                           the timeline's category
   */
  public function getCategory() {
    return $this->data_['category'];
  }

  /**
   * Set the timeline's category.
   *
   * @param   string            $category      the timeline's category
   */
  public function setCategory($category) {
    $this->data_['category'] = $category;
  }

  /**
   * Get whether the timeline is part of the search results index.
   *
   * @return  boolean                          <pre>true</pre> if suppressed from index
   */
  public function getNoIndex() {
    return $this->data_['noindex'] == "1" ? true : false;
  }

  /**
   * Set whether the timeline is part of the search results index.
   *
   * @param   boolean           $noIndex       <pre>true</pre> if suppressed from index
   */
  public function setNoIndex($noIndex) {
    $this->data_['noindex'] = $noIndex ? "1" : "0";
  }
}

/**
 * Wrapper for event data retrieved from/sent to the Dipity service.
 */
class DipityEvent {
  private $data_;
  private $eid_;

  /**
   * Overloading method to allow DipityRestClient access to this object's
   * internal state.
   */
  public function __get($key) 
  { 
    $trace = debug_backtrace(); 
    if(isset($trace[1]['class']) && $trace[1]['class'] == 'DipityRestClient') { 
      return $this->$key; 
    } 
    trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $key, E_USER_ERROR); 
  } 

  /**
   * Construct an object to wrap event data for the Dipity REST API.
   *
   * @param   array             $data          state of the event object
   */
  public function DipityEvent(array &$data = array()) {
    $this->eid_ = $data['eid'];
    unset($data['eid']);
    $this->data_ = $data;
  }

  /**
   * Get the event ID of the event.
   *
   * @return  string                           event ID of the event
   */
  public function getEid() {
    if (isset($this->eid_)) {
      return $this->eid_;
    }
    throw new PEAR_Exception("event id not present");
  }

  /**
   * Set the event ID of the event.
   *
   * @param   string            $eid           event ID of the event
   */
  public function setEid($eid) {
    $this->eid_ = $eid;
  }

  /**
   * Get the title of the event.
   *
   * @return  string                           title of the event
   */
  public function getTitle() {
    return stripslashes($this->data_['title']);
  }

  /**
   * Set the title of the event.
   *
   * @param   string            $title         title of the event
   */
  public function setTitle($title) {
    $this->data_['title'] = $title;
  }

  /**
   * Get the description of the event.
   *
   * @return  string                           description of the event
   */
  public function getDescription() {
    return stripslashes($this->data_['description']);
  }

  /**
   * Set the description of the event.
   *
   * @param   string            $description   description of the event
   */
  public function setDescription($description) {
    $this->data_['description'] = $description;
  }

  /**
   * Get the date of the event in a human-readable format.
   *
   * @return  string                           the date and time of the event
   */
  public function getDate() {
    return date(DATE_RFC2822, strtotime($this->data_['timestamp']));
  }

  /**
   * Get the timestamp of the event, in seconds from the Unix epoch.
   *
   * @return  int                              seconds from the Unix epoch
   */
  public function getTimestamp() {
    return strtotime($this->data_['timestamp']);
  }

  /**
   * Set the timestamp of the event, in seconds from the Unix epoch.
   *
   * @param   int               $timestamp     seconds from the Unix epoch
   */
  public function setTimestamp($timestamp) {
    $this->data_['timestamp'] = date(DATE_RFC3339, $timestamp);
  }

  /**
   * Get the link URL of the event.
   *
   * @return  string                           URL to which the event is linked
   */
  public function getLinkURL() {
    return $this->data_['link_url'];
  }

  /**
   * Set the link URL of the event.
   *
   * @param   string            $url           URL to which the event is linked
   */
  public function setLinkURL($url) {
    $this->data_['link_url'] = $url;
  }

  /**
   * Get the image URL of the event.
   *
   * @return  string                           URL of the event image
   */
  public function getImageURL() {
    return $this->data_['image_url'];
  }

  /**
   * Set the image URL of the event.
   *
   * @param   string            $url           URL of the event image
   */
  public function setImageURL($url) {
    $this->data_['image_url'] = $url;
  }

  /**
   * Get the video URL of the event.
   *
   * @return  string                           URL of the event video
   */
  public function getVideoURL() {
    return $this->data_['video_url'];
  }

  /**
   * Set the video URL of the event.
   *
   * @param   string            $url           URL of the event video
   */
  public function setVideoURL($url) {
    $this->data_['video_url'] = $url;
  }

  /**
   * Get the location of the event.
   *
   * @return  string                           the location of the event
   */
  public function getLocation() {
    return $this->data_['location'];
  }

  /**
   * Set the location of the event.
   *
   * @param   string            $location      the location of the event
   */
  public function setLocation($location) {
    $this->data_['location'] = $location;
  }

  /**
   * Get the specificity of the event.
   *
   * @return  string                           the specificity of the event
   */
  public function getSpecificity() {
    return $this->data_['specificity'];
  }

  /**
   * Set the specificity of the event.
   *
   * @param   string            $specificity   the specificity of the event
   */
  public function setSpecificity($specificity) {
    $this->data_['specificity'] = $specificity;
  }
}

?>