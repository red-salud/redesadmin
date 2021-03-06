<?php
class Model_elemento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_elemento($paramPaginate,$paramDatos=FALSE){ 
		$this->db->select('el.idelemento, el.descripcion_ele, el.precio_referencial, el.tipo_elemento, 
			 cael.idcategoriaelemento, cael.descripcion_cael, cael.color_cael, cael.key_cael');
		$this->db->from('elemento el');
		$this->db->join('categoria_elemento cael', 'el.idcategoriaelemento = cael.idcategoriaelemento'); 
		$this->db->where('el.estado_ele', 1);
		if( !empty($paramDatos['tipo_elemento']) ){
			$this->db->where('el.tipo_elemento', $paramDatos['tipo_elemento']);
		}
		if( !empty($paramDatos['categoria_key']) ){
			$this->db->where('cael.key_cael', $paramDatos['categoria_key']); 
		}
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
	public function m_count_elemento($paramPaginate,$paramDatos=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('elemento el');
		$this->db->join('categoria_elemento cael', 'el.idcategoriaelemento = cael.idcategoriaelemento');
		$this->db->where('el.estado_ele', 1);
		if( !empty($paramDatos['tipo_elemento']) ){
			$this->db->where('el.tipo_elemento', $paramDatos['tipo_elemento']);
		}
		if( !empty($paramDatos['categoria_key']) ){
			$this->db->where('cael.key_cael', $paramDatos['categoria_key']); 
		}
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
	public function m_cargar_elemento_cbo($datos)
	{
		$this->db->select('el.idelemento, el.descripcion_ele, el.precio_referencial');
		$this->db->from('elemento el');
		$this->db->join('categoria_elemento cael','el.idcategoriaelemento = cael.idcategoriaelemento');
		$this->db->where('el.estado_ele', 1); 
		$this->db->where('cael.key_cael', 'key_plan_salud'); 
		$this->db->order_by('el.descripcion_ele','ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_elemento_proveedor_cbo($datos)
	{
		$this->db->select('el.idelemento, el.descripcion_ele, el.precio_referencial');
		$this->db->from('elemento el');
		$this->db->join('categoria_elemento cael','el.idcategoriaelemento = cael.idcategoriaelemento');
		$this->db->where('el.estado_ele', 1); 
		$this->db->where('cael.key_cael', 'key_proveedor'); 
		$this->db->order_by('el.descripcion_ele','ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_elementos_limite($datos)
	{
		$this->db->select('el.idelemento, el.descripcion_ele, el.precio_referencial, el.tipo_elemento, 
			cael.idcategoriaelemento, cael.descripcion_cael, cael.color_cael');
		$this->db->from('elemento el');
		$this->db->join('categoria_elemento cael', 'el.idcategoriaelemento = cael.idcategoriaelemento');
		$this->db->where('el.estado_ele', 1); 
		if( !empty($datos['categoria_key']) ){ 
			$this->db->where('cael.key_cael', $datos['categoria_key']); 
		} 
		$this->db->like($datos['searchColumn'], $datos['searchText']);
		$this->db->order_by('el.descripcion_ele');
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'idcategoriaelemento' => $datos['categoria_elemento']['id'], 
			'descripcion_ele' => strtoupper($datos['descripcion_ele']),	
			'tipo_elemento' => strtoupper($datos['tipo_elemento']['id']),	
			'precio_referencial' => empty($datos['precio_referencial']) ? NULL : $datos['precio_referencial'], 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('elemento', $data); 
	}
	public function m_editar($datos)
	{
		$data = array(
			'idcategoriaelemento' => $datos['categoria_elemento']['id'], 
			'descripcion_ele' => strtoupper($datos['descripcion_ele']),	
			'tipo_elemento' => strtoupper($datos['tipo_elemento']['id']), 
			'precio_referencial' => empty($datos['precio_referencial']) ? NULL : $datos['precio_referencial'], 
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idelemento',$datos['id']);
		return $this->db->update('elemento', $data); 
	}
	public function m_anular($datos)
	{
		$data = array( 
			'estado_ele' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idelemento',$datos['id']); 
		return $this->db->update('elemento', $data); 
	}
}
?>