<?php
class Model_cliente_empresa extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cliente_empresa($paramPaginate=FALSE){
		// var_dump($paramDatosCo);
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial_cli, ce.nombre_corto_cli, ce.razon_social_cli, ce.numero_documento_cli, ce.representante_legal, ce.dni_representante_legal, ce.direccion_legal, ce.telefono_cli, pagina_web_cli, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
		$this->db->where('estado_cli', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']); 			
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
	public function m_count_cliente_empresa($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
		$this->db->where('estado_cli', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']);
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
	public function m_buscar_cliente_empresa($datos)
	{
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial_cli, ce.nombre_corto_cli, ce.razon_social_cli, ce.numero_documento_cli, ce.representante_legal, ce.dni_representante_legal, ce.direccion_legal, ce.telefono_cli, pagina_web_cli, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('cliente_empresa ce');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente'); 
		$this->db->where('ce.estado_cli', 1); // activo  
		$this->db->where('ce.numero_documento_cli', $datos['num_documento']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	// VALIDACIONES 
	public function m_validar_cliente_empresa_num_documento($numDocumento,$excepcion = FALSE,$idclienteempresa=NULL) 
	{
		$this->db->select('ce.idclienteempresa');
		$this->db->from('cliente_empresa ce');
		$this->db->where('ce.estado_cli',1);
		$this->db->where('ce.numero_documento_cli',$numDocumento);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']);
		if( $excepcion ){
			$this->db->where_not_in('ce.idclienteempresa',$idclienteempresa);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'nombre_comercial_cli' => strtoupper($datos['nombre_comercial']), 
			'nombre_corto_cli' => strtoupper($datos['nombre_corto']),
			'razon_social_cli' => strtoupper($datos['razon_social']),	
			'numero_documento_cli' => $datos['numero_documento'],	
			'representante_legal' => empty($datos['representante_legal']) ? NULL : strtoupper($datos['representante_legal']),	
			'dni_representante_legal' => empty($datos['dni_representante_legal']) ? NULL : $datos['dni_representante_legal'],	
			'direccion_legal' => $datos['direccion_legal'],	
			'telefono_cli' => empty($datos['telefono']) ? NULL : $datos['telefono'],
			'pagina_web_cli' => empty($datos['pagina_web']) ? NULL : strtoupper($datos['pagina_web']),
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'idempresaadmin' => $this->sessionRS['idempresaadmin'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s'),
			'fecha_alta_cli'=> date('Y-m-d H:i:s')
		);
		return $this->db->insert('cliente_empresa', $data);
	}	
	public function m_editar($datos){
		$data = array(
			'nombre_comercial_cli' => strtoupper($datos['nombre_comercial']), 
			'nombre_corto_cli' => strtoupper($datos['nombre_corto']),
			'razon_social_cli' => strtoupper($datos['razon_social']),	
			'numero_documento_cli' => $datos['numero_documento'],	
			'representante_legal' => empty($datos['representante_legal']) ? NULL : strtoupper($datos['representante_legal']),	
			'dni_representante_legal' => empty($datos['dni_representante_legal']) ? NULL : $datos['dni_representante_legal'],	
			'direccion_legal' => $datos['direccion_legal'],	
			'telefono_cli' => empty($datos['telefono']) ? NULL : $datos['telefono'],
			'pagina_web_cli' => empty($datos['pagina_web']) ? NULL : strtoupper($datos['pagina_web']),
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idclienteempresa',$datos['id']);
		return $this->db->update('cliente_empresa', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_cli' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idclienteempresa',$datos['id']);
		return $this->db->update('cliente_empresa', $data);
	}

	public function m_cargar_cliente_empresa_cbo($datos = FALSE){ 
		$this->db->select("ce.idclienteempresa, ce.nombre_comercial_cli");
		$this->db->from('cliente_empresa ce');
		$this->db->where('ce.estado_cli', 1); //activo
		$this->db->order_by('ce.nombre_comercial_cli','ASC');
		return $this->db->get()->result_array();
	}

	public function m_cargar_cliente_empresa_limite($datos)
	{
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial_cli');
		$this->db->from('cliente_empresa ce');
		$this->db->where('ce.estado_cli', 1);
		$this->db->like($datos['searchColumn'], $datos['searchText']);
		$this->db->order_by('ce.nombre_comercial_cli');
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}

}
?>