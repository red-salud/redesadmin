<?php
class Model_cliente_persona extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cliente_persona($paramPaginate){ 
		$this->db->select('CONCAT(cp.nombres_cli," ",cp.ap_paterno_cli," ",cp.ap_materno_cli) AS cliente', FALSE);
		$this->db->select('cp.idclientepersona, cp.numero_documento_cli, cp.nombres_cli, cp.ap_paterno_cli, cp.ap_materno_cli, cp.sexo_cli, cp.telefono_fijo_cli, cp.telefono_movil_cli, cp.email_cli, cp.fecha_nacimiento_cli, 
			cc.idcategoriacliente, cc.descripcion_cc, tdi.idtipodocumentoidentidad, tdi.descripcion_tdi');
		$this->db->from('cliente_persona cp');
		$this->db->join('categoria_cliente cc', 'cp.idcategoriacliente = cc.idcategoriacliente');
		$this->db->join('tipo_documento_identidad tdi', 'cp.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad');
		$this->db->where('cp.estado_cli', 1);
		$this->db->where('cp.idempresaadmin', $this->sessionRS['idempresaadmin']);
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
	public function m_count_cliente_persona($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('cliente_persona cp');
		$this->db->join('categoria_cliente cc', 'cp.idcategoriacliente = cc.idcategoriacliente');
		$this->db->join('tipo_documento_identidad tdi', 'cp.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad');
		$this->db->where('cp.estado_cli', 1);
		$this->db->where('cp.idempresaadmin', $this->sessionRS['idempresaadmin']);
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
	public function m_buscar_cliente_persona($datos)
	{
		$this->db->select('cp.idclientepersona, cp.numero_documento_cli, cp.nombres_cli, cp.ap_paterno_cli, cp.ap_materno_cli, cp.sexo_cli, cp.telefono_fijo_cli, cp.telefono_movil_cli, cp.email_cli, 
			cc.idcategoriacliente, cc.descripcion_cc');
		$this->db->from('cliente_persona cp');
		$this->db->join('categoria_cliente cc', 'cp.idcategoriacliente = cc.idcategoriacliente');
		$this->db->where('cp.estado_cli', 1); // activo 
		$this->db->where('cp.numero_documento_cli', $datos['num_documento']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	// VALIDACIONES 
	public function m_validar_cliente_persona_num_documento($numDocumento,$excepcion = FALSE,$idclientepersona=NULL) 
	{
		$this->db->select('cp.idclientepersona');
		$this->db->from('cliente_persona cp');
		$this->db->where('cp.estado_cli',1);
		$this->db->where('cp.numero_documento_cli',$numDocumento);
		$this->db->where('cp.idempresaadmin', $this->sessionRS['idempresaadmin']);
		if( $excepcion ){
			$this->db->where_not_in('cp.idclientepersona',$idclientepersona);
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'idtipodocumentoidentidad' => 1, // DNI 
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'numero_documento_cli' => $datos['num_documento'],
			'nombres_cli' => strtoupper($datos['nombres']),	
			'ap_paterno_cli' => strtoupper($datos['ap_paterno']),
			'ap_materno_cli' => strtoupper($datos['ap_materno']),
			'sexo_cli' => $datos['sexo']['id'], 
			'telefono_movil_cli' => $datos['telefono_movil'],	
			'telefono_fijo_cli' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'],	
			'email_cli' => empty($datos['email']) ? NULL : strtoupper($datos['email']),
			'fecha_nacimiento_cli' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'idempresaadmin' => $this->sessionRS['idempresaadmin'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('cliente_persona', $data);
	}	
	public function m_editar($datos){
		$data = array(
			// 'idtipodocumentoidentidad' => 1, // DNI 
			'idcategoriacliente' => $datos['categoria_cliente']['id'],
			'numero_documento_cli' => $datos['num_documento'],
			'nombres_cli' => strtoupper($datos['nombres']),	
			'ap_paterno_cli' => strtoupper($datos['ap_paterno']),
			'ap_materno_cli' => strtoupper($datos['ap_materno']),
			'sexo_cli' => $datos['sexo']['id'], 
			'telefono_movil_cli' => $datos['telefono_movil'],
			'telefono_fijo_cli' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'],	
			'email_cli' => empty($datos['email']) ? NULL : strtoupper($datos['email']),
			'fecha_nacimiento_cli' => empty($datos['fecha_nacimiento']) ? NULL : darFormatoYMD($datos['fecha_nacimiento']),	
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idclientepersona',$datos['id']);
		return $this->db->update('cliente_persona', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_cli' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idclientepersona',$datos['id']);
		return $this->db->update('cliente_persona', $data);
	}	
}
?>