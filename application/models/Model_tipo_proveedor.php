<?php
class Model_tipo_proveedor extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_tipo_proveedor_cbo($datos = FALSE){ 
		$this->db->select('tp.idtipoproveedor, tp.descripcion_tpr, tp.estado_tpr');
		$this->db->from('tipo_proveedor tp');
		$this->db->where('tp.estado_tpr', 1); // activo
		$this->db->order_by('tp.descripcion_tpr','ASC');
		return $this->db->get()->result_array();
	}
}
?>