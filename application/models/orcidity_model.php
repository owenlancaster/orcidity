<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orcidity_model extends CI_Model {

	public function getSourcesFull () {
		$query = $this->db->get('sources');
		return $query;
	}

	
}
