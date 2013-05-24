<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function orcidityEmail($from_email, $from_name, $to_email, $subject, $message, $cc = NULL, $bcc = NULL) {
	$ci = get_instance();
	$ci->load->library('email');
	$ci->email->from($from_email, $from_name);
	$ci->email->to($to_email);
//	$ci->email->cc('another@another-example.com');
//	$ci->email->bcc('them@their-example.com');
	$ci->email->subject($subject);
	$ci->email->message($message);
	$ci->email->send();
}

function setCurrentURL() {
	$ci =& get_instance();
	$ci->load->helper('url');
	$ci->load->library('session');
//	$current = $ci->uri->uri_string();
//	$current = $ci->config->site_url().$ci->uri->uri_string();
	$current = current_url();
	$ci->session->set_userdata('return_to', $current);
//	error_log("c -> " . $ci->session->userdata('return_to'));
}


function isDefined($string) {
	if ( isset($string) && !empty($string)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function isNotDefined($string) {
	if ( ! isset($string) && empty($string)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

?>