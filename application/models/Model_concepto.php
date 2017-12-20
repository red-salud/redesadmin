<?php
class Model_concepto extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_concepto($paramPaginate,$paramDatos=FALSE){ 
		$this->db->select('con.idconcepto, con.descripcion_con');
		$this->db->from('concepto con');
		$this->db->where('con.estado_con', 1);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}
	public function m_count_concepto($paramPaginate,$paramDatos=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('concepto con');
		$this->db->where('con.estado_con', 1);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_concepto_cbo($datos) 
	{
		$this->db->select('con.idconcepto, con.descripcion_con, con.key_concepto'); 
		$this->db->from('concepto con'); 
		$this->db->where('con.estado_con', 1); 
		//var_dump($datos['tipo_concepto']); exit(); 
		if( @$datos['tipo_concepto'] == 'C' ){ // CLIENTE 
			$this->db->where('con.destino_concepto', 1); 
		}
		if( @$datos['tipo_concepto'] == 'P' ){ // PROVEEDOR 
			$this->db->where('con.destino_concepto', 2); 
		}
		$this->db->order_by('con.descripcion_con','ASC');
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array( 
			'descripcion_con' => strtoupper($datos['descripcion_con']),	
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('concepto', $data); 
	}
	public function m_editar($datos)
	{
		$data = array(
			'descripcion_con' => strtoupper($datos['descripcion_con']),	
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idconcepto',$datos['idconcepto']);
		return $this->db->update('concepto', $data); 
	}
	public function m_anular($datos)
	{
		$data = array( 
			'estado_con' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idconcepto',$datos['idconcepto']); 
		return $this->db->update('concepto', $data); 
	}
}
?>