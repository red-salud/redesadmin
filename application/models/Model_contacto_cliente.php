<?php
class Model_contacto_cliente extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_contacto($paramPaginate,$paramDatos=FALSE){
		$this->db->select("co.idcontactocliente, co.nombres, co.apellidos, co.fecha_nacimiento, co.telefono_fijo, co.telefono_movil, co.email, co.anexo, co.area_encargada");
		$this->db->select('ce.idclienteempresa, ce.nombre_comercial_cli, ce.nombre_corto_cli, ce.razon_social_cli, ce.numero_documento_cli, ce.representante_legal, ce.dni_representante_legal, 
			ce.direccion_legal, ce.telefono_cli, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('contacto_cliente co');
		$this->db->join('cliente_empresa ce', 'co.idclienteempresa = ce.idclienteempresa');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente','left');
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.estado_cli', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']);
		if(!empty($paramDatos['cliente'])){ 
			$this->db->where('ce.idclienteempresa', $paramDatos['cliente']['id']);
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

	public function m_count_contacto($paramPaginate,$paramDatos=FALSE){ 
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('contacto_cliente co');
		$this->db->join('cliente_empresa ce', 'co.idclienteempresa = ce.idclienteempresa');
		$this->db->join('categoria_cliente cc', 'ce.idcategoriacliente = cc.idcategoriacliente');
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.estado_cli', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']);
		if(!empty($paramDatos['cliente'])){ 
			$this->db->where('ce.idclienteempresa', $paramDatos['cliente']['id']);
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

	public function m_cargar_contacto_esta_empresa($paramPaginate,$paramDatos){
		// var_dump($paramDatos);exit();
		$this->db->select('co.idcontactocliente, co.nombres, co.apellidos, co.fecha_nacimiento, co.telefono_fijo, co.telefono_movil, co.email, co.anexo, co.area_encargada,
			ce.idclienteempresa, ce.nombre_comercial_cli, ce.numero_documento_cli, ce.nombre_corto_cli, ce.razon_social_cli, ce.representante_legal, ce.dni_representante_legal');
		$this->db->from('cliente_empresa ce');
		$this->db->join('contacto_cliente co', 'ce.idclienteempresa = co.idclienteempresa');
		$this->db->where('ce.estado_cli', 1);
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.idclienteempresa', $paramDatos['id']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
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

	public function m_count_contacto_esta_empresa($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_empresa ce');
		$this->db->join('contacto_cliente co', 'ce.idclienteempresa = co.idclienteempresa');
		$this->db->where('ce.estado_cli', 1);
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.idclienteempresa', $paramDatos['id']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_contacto_empresa_limite($datos)
	{
		$this->db->select('co.idcontactocliente, CONCAT(co.nombres, " ", co.apellidos) as contacto, co.area_encargada, co.idclienteempresa, co.telefono_fijo, co.telefono_movil, co.anexo, 
			ce.numero_documento_cli, ce.razon_social_cli, ce.representante_legal, ce.dni_representante_legal');
		$this->db->from('contacto_cliente co');
		$this->db->join('cliente_empresa ce', 'co.idclienteempresa = ce.idclienteempresa');
		$this->db->where('co.estado_co', 1);
		$this->db->where('ce.estado_cli', 1);
		$this->db->where('ce.idempresaadmin', $this->sessionRS['idempresaadmin']);
		$this->db->like('CONCAT(co.nombres, " ", co.apellidos)', $datos['searchText']);
		$this->db->order_by('co.apellidos');
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}
	// VALIDACIONES 
	
	// CRUD 
	public function m_registrar($datos)
	{
		$data = array( 
			'idclienteempresa' => $datos['idclienteempresa'],
			'nombres' => $datos['nombres'],
			'apellidos' => empty($datos['apellidos']) ? NULL : $datos['apellidos'],	
			'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'telefono_fijo' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'],	
			'telefono_movil' => empty($datos['telefono_movil']) ? NULL : $datos['telefono_movil'], 
			'email' => empty($datos['email']) ? NULL : $datos['email'], 
			'anexo' => empty($datos['anexo']) ? NULL : $datos['anexo'], 
			'area_encargada' => empty($datos['area_encargada']) ? NULL : strtoupper($datos['area_encargada']), 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('contacto_cliente', $data);
	}	
	public function m_editar($datos){ 
		$data = array( 
			'idclienteempresa' => $datos['idclienteempresa'],
			'nombres' => $datos['nombres'],
			'apellidos' => empty($datos['apellidos']) ? NULL : $datos['apellidos'],	
			'fecha_nacimiento' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'telefono_fijo' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'],	
			'telefono_movil' => empty($datos['telefono_movil']) ? NULL : $datos['telefono_movil'], 
			'email' => empty($datos['email']) ? NULL : $datos['email'], 
			'anexo' => empty($datos['anexo']) ? NULL : $datos['anexo'], 
			'area_encargada' => empty($datos['area_encargada']) ? NULL : strtoupper($datos['area_encargada']), 
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcontactocliente',$datos['id']);
		return $this->db->update('contacto_cliente', $data);
	} 
	public function m_anular($datos)
	{
		$data = array( 
			'estado_co' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		); 
		$this->db->where('idcontactocliente',$datos['idcontactocliente']);
		return $this->db->update('contacto_cliente', $data);
	} 
}
?>