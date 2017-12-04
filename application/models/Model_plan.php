<?php
class Model_plan extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_plan_cbo($datos = FALSE){ 
		$this->db->select('pl.idplan, pl.nombre_plan, pl.codigo_plan');
		$this->db->from('plan pl');
		$this->db->where('pl.estado_plan', 1); // habilitado 
		$this->db->order_by('pl.nombre_plan','ASC'); 
		return $this->db->get()->result_array(); 
	}
	public function m_cargar_condiciones_de_este_plan($datos)
	{
		$this->db->select('pl.idplan, pl.codigo_plan, pl.nombre_plan, pl.dias_carencia, pl.dias_mora, pl.dias_atencion, 
			pl_det.idplandetalle, pl_det.texto_web, var_pl.idvariableplan, var_pl.nombre_var, var_pl.observaciones'); 
		$this->db->from('plan pl');
		$this->db->join('plan_detalle pl_det', 'pl.idplan = pl_det.idplan'); 
		$this->db->join('variable_plan var_pl', 'pl_det.idvariableplan = var_pl.idvariableplan'); 
		$this->db->where('pl.estado_plan', 1); // habilitado 
		$this->db->where('pl_det.estado_pd', 1); // habilitado 
		$this->db->where('pl.idplan', $datos['idplan']); // un plan  
		$this->db->order_by('pl_det.idplandetalle','ASC'); 
		return $this->db->get()->result_array(); 
	}
}
?>