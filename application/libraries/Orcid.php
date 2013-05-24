<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ORCID {
	
	public function orcid_works($orcid) {
		$orcid_url = "http://pub.orcid.org/" . $orcid . "/orcid-works/";
		$orcid_xml =  simplexml_load_file($orcid_url);
		$orcid_work = $orcid_xml->{'orcid-profile'}->{'orcid-activities'}->{'orcid-works'}->{'orcid-work'};
		$dois = array();
//		print_r($orcid_work);
		foreach ( $orcid_work as $work) {
			// Get the DOI - not sure if this is the standard structure of ORCID xml - what if there's more than one - just getting the first element of array here...so probably hardcoding this
//			print_r($work->{'work-external-identifiers'}->{'work-external-identifier'});
			$doi = (string) $work->{'work-external-identifiers'}->{'work-external-identifier'}->{'work-external-identifier-id'}[0];
			$dois[] = $doi; // Add to dois array
//			print "$doi<br />";
		}
		return $dois;
	}
	
	public function orcid_profile($orcid) { // My own function from another project to get ORCID profile - not for hackathon
//		$orcid = $this->input->post('orcid');
		$orcid_url = "http://pub.orcid.org/" . $orcid;
		$orcid_xml =  simplexml_load_file($orcid_url);
		$given_name = $orcid_xml->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'given-names'};
		$family_name = $orcid_xml->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'family-name'};
		$orcid_data = array();
		$orcid_data['givenname'] = (string) $given_name;
		$orcid_data['familyname'] = (string) $family_name;
		return $orcid_data;
//		echo json_encode($orcid_data, JSON_FORCE_OBJECT);
//		echo json_encode($orcid_data);
	}
	

}
