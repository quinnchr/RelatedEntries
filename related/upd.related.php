<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Related_upd {

	var $version = '0.1';
	
	function Related_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => 'Related' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'n',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		$fields = array('entry_id'	 => array('type' => 'int', 'constraint'	 => '11'),
						'field_id'	 => array('type' => 'int', 'constraint'	 => '11'),
						'frequency'	 => array('type' => 'text'),
						'related'	 => array('type' => 'text'),
						'last_cache' => array('type' => 'timestamp'));

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->create_table('related_posts');
		
		unset($fields);
		
		return TRUE;
	}
	
	function uninstall()
	{
		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Related'));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', 'Related');
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', 'Related');
		$this->EE->db->delete('actions');

		$this->EE->dbforge->drop_table('related_posts');		

		return TRUE;
	}

	function update($current='')
	{
		return TRUE;
	}
	
}
