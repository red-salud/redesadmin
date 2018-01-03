<?php
class Model_historia extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_generar_historia_clinica($datos)
	{
		$data = array(
			'idasegurado' => strtoupper($datos['idasegurado']) 
		);
		return $this->db->insert('historia', $data); 
	}
}
?>