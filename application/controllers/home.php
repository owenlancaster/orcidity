<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('orcidity_model');
	}
	
	public function index($renderData=""){	
		$this->title = "ORCIDity";
		$this->keywords = "ORCID";        
		$this->_render('pages/home',$renderData);
	}
	
	public function search() {
		$this->load->library('Orcid');
		$this->load->library('CrossRef');
		// Get the orcid ID specified in the form
		$orcid = $this->input->get('orcid');
		// Do the lookup to the orcid public api to get an array of the dois that this orcid has made public
		$dois = $this->orcid->orcid_works($orcid);
		$doi_count = count($dois);
//		error_log("doi count -> " . $doi_count);
		if ( $doi_count > 0 ) {
			$orcid_profile = $this->orcid->orcid_profile($orcid);
			$this->data['orcid_profile'] = $orcid_profile;
			$doi_dates = array();
			// Go through each doi
			foreach ( $dois as $doi ) {
//				print "doi -> $doi<br />";
				// Use crossref api library and get and return an array of the publication date of each doi
				$doi_publication_dates = $this->crossref->get_publication_dates($doi);
				// Store dates in array with doi as key and array of dates as value
				$doi_dates[$doi] = $doi_publication_dates;
			}
//			print_r($doi_dates);
			$json = $this->formatTimelineData($doi_dates, $orcid_profile);
			$this->data['json'] = $json;
			$this->data['doi_dates'] = $doi_dates;
			$this->_render('pages/orcid_mashup');
		}
		else {
			print "No publications are available for this ORCID";
		}
		
	}
	
	public function formatTimelineData($doi_dates, $orcid_profile) {

		$json = '

{
    "timeline":
    {
        "headline":"ORCID Timeline",
        "type":"default",
		"text":"ORCID Timeline for ' . $orcid_profile['givenname'] . " " . $orcid_profile['familyname'] . '",
		"startDate":"2010,1,26",
        "date": [';
		$count = count($doi_dates);
		$c = 0;
		end($doi_dates);
		$last_key = key($doi_dates);
		
		foreach ( $doi_dates as $date ) {
//			print_r($date);
			
			$year = $date['year'];
			$month = $date['month'];
			$day = $date['day'];


			$c++;
			if ( $month == "" && $day == "") {
				$start_date = $year . ",1,1";
				$end_date = $year . ",12,30";
			}
			else if ( $day == "") {
				$start_date = $year . ",$month,1";
				$end_date = $year . ",$month,30";
			}
			else {
				$start_date = $year . "," . $month . "," . $day;
				$end_date = $year . "," . $month . "," . $day;
			}
            $json .= '{
                "startDate":"' . $start_date . '",
				"endDate":"' . $end_date . '",
                "headline":"' . $date['doi'] . '",
                "text":"<p>' . $date['doi'] . '</p>",
                "asset":
                {
                    "media":"' . $date['link'] . '",
                    "credit":"",
                    "caption":""
                }
            }';
//			error_log("count ". $count . " vs " . $c);
			if ( $count != $c ) {
				$json .= ',';
			}
//			error_log("$count vs $c");
			
//			print "$j<br />";
			if ( $year == "" ) {
				continue;
			}
		}

		$json .= '
        ]
    }
}
'; 
		$file = FCPATH . "temp/json_temp.json";
		file_put_contents($file, $json);
		return $json;
	}
	
}