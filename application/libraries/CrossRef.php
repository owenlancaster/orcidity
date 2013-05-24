<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CrossRef {
	
	public function get_publication_dates($doi) {
		$cross_ref_uri = "http://doi.crossref.org/servlet/query?pid=ol8@leicester.ac.uk&id=" . $doi . "&format=unixref";
//		error_log($cross_ref_uri);
		$crossref_xml =  simplexml_load_file($cross_ref_uri);
		$publication_dates = array();
		$year = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_issue'}->{'publication_date'}->{'year'};
		$month = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_issue'}->{'publication_date'}->{'month'};
		$day = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_issue'}->{'publication_date'}->{'day'};
		$link = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_article'}->{'doi_data'}->{'resource'};
		$title = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_article'}->{'titles'}->{'title'};
		$timestamp = $crossref_xml->{'doi_record'}->{'crossref'}->{'journal'}->{'journal_article'}->{'doi_data'}->{'timestamp'};
//		print "$year $month $day<br />";
		$publication_dates['doi'] = $doi;
		$publication_dates['year'] = (string) $year;
		$publication_dates['month'] = (string) $month;
		$publication_dates['day'] = (string) $day;
		$publication_dates['link'] = (string) $link;
		$publication_dates['title'] = (string) $title;
		return $publication_dates;
		
	}

}
