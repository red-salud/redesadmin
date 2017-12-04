<?php
class Model_producto extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_producto_tipo_consulta_cbo(){ 
		$this->db->select('pr.idproducto, pr.descripcion_prod, tpr.idtipoproducto, tpr.descripcion_tp, tpr.key_tp, esp.idespecialidad, esp.nombre_esp');
		$this->db->from('producto pr');
		$this->db->join('tipo_producto tpr','pr.idtipoproducto = tpr.idtipoproducto');
		$this->db->join('especialidad esp','pr.idespecialidad = esp.idespecialidad');
		$this->db->where('pr.estado_prod', 1); // activo 
		$this->db->where('tpr.key_tp','key_consulta');
		$this->db->order_by('pr.descripcion_prod','ASC');
		return $this->db->get()->result_array();
	}
}
?>