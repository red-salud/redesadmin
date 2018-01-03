<?php
class Model_cargo_contacto extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cargo_contacto_cbo($datos) 
	{
		$this->db->select('cco.idcargocontacto, cco.descripcion_ctc, cco.estado_ctc'); 
		$this->db->from('cargo_contacto cco'); 
		$this->db->where('cco.estado_ctc', 1); 
		$this->db->order_by('cco.descripcion_ctc','ASC');
		return $this->db->get()->result_array();
	}
}
?>