<?php
class Model_proveedor extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_proveedor($paramPaginate,$paramDatos=FALSE){
		// var_dump($paramDatosCo);
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr, pr.referencia_pr, pr.cod_sunasa_pr, pr.latitud, pr.longitud, pr.estado_pr, cod_departamento_pr, cod_provincia_pr, cod_distrito_pr, 
			tpr.idtipoproveedor, tpr.descripcion_tpr, tdi.idtipodocumentoidentidad, tdi.descripcion_tdi, 
			dpto.descripcion_ubig AS departamento, prov.descripcion_ubig AS provincia, dist.descripcion_ubig AS distrito, us.idusuario, us.username');
		$this->db->from('proveedor pr');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor');
		$this->db->join('tipo_documento_identidad tdi', 'pr.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad'); 
		$this->db->join('usuario us', 'pr.idusuario = us.idusuario','left');
		$this->db->join("ubigeo dpto","pr.cod_departamento_pr = dpto.iddepartamento  AND dpto.idprovincia = '00' AND dpto.iddistrito = '00'", 'left');
		$this->db->join("ubigeo prov","pr.cod_provincia_pr = prov.idprovincia AND prov.iddepartamento = pr.cod_departamento_pr AND prov.iddistrito = '00'", 'left');
		$this->db->join('ubigeo dist',"pr.cod_distrito_pr = dist.iddistrito AND dist.iddepartamento = pr.cod_departamento_pr AND dist.idprovincia = pr.cod_provincia_pr", 'left');
		if( !empty($paramDatos['tipo_proveedor']['id']) && !($paramDatos['tipo_proveedor']['id'] == 'all') ){ 
			$this->db->where('tpr.idtipoproveedor',$paramDatos['tipo_proveedor']['id']); 
		}
		$this->db->where_in('pr.estado_pr', array(1,2,3)); // 1:activo 2:observado 3:inactivo 	
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
	public function m_count_proveedor($paramPaginate,$paramDatos=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('proveedor pr');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor');
		$this->db->join('tipo_documento_identidad tdi', 'pr.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad'); 
		$this->db->join('usuario us', 'pr.idusuario = us.idusuario','left');
		if( !empty($paramDatos['tipo_proveedor']['id']) && !($paramDatos['tipo_proveedor']['id'] == 'all') ){ 
			$this->db->where('tpr.idtipoproveedor',$paramDatos['tipo_proveedor']['id']); 
		}
		$this->db->where_in('pr.estado_pr', array(1,2,3)); // 1:activo 2:observado 3:inactivo 
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
	public function m_cargar_proveedores_sin_usuario($paramPaginate,$paramDatos=FALSE)
	{
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr, pr.referencia_pr, pr.cod_sunasa_pr, pr.latitud, pr.longitud, pr.estado_pr, cod_departamento_pr, cod_provincia_pr, cod_distrito_pr, 
			tpr.idtipoproveedor, tpr.descripcion_tpr, tdi.idtipodocumentoidentidad, tdi.descripcion_tdi, 
			dpto.descripcion_ubig AS departamento, prov.descripcion_ubig AS provincia, dist.descripcion_ubig AS distrito, us.idusuario, us.username');
		$this->db->from('proveedor pr');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor');
		$this->db->join('tipo_documento_identidad tdi', 'pr.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad'); 
		$this->db->join('usuario us', 'pr.idusuario = us.idusuario','left');
		$this->db->join("ubigeo dpto","pr.cod_departamento_pr = dpto.iddepartamento  AND dpto.idprovincia = '00' AND dpto.iddistrito = '00'", 'left');
		$this->db->join("ubigeo prov","pr.cod_provincia_pr = prov.idprovincia AND prov.iddepartamento = pr.cod_departamento_pr AND prov.iddistrito = '00'", 'left');
		$this->db->join('ubigeo dist',"pr.cod_distrito_pr = dist.iddistrito AND dist.iddepartamento = pr.cod_departamento_pr AND dist.idprovincia = pr.cod_provincia_pr", 'left');
		if( !empty($paramDatos['tipo_proveedor']['id']) && !($paramDatos['tipo_proveedor']['id'] == 'all') ){ 
			$this->db->where('tpr.idtipoproveedor',$paramDatos['tipo_proveedor']['id']); 
		}
		$this->db->where('us.idusuario IS NULL');
		$this->db->where_in('pr.estado_pr', array(1,2,3)); // 1:activo 2:observado 3:inactivo 	
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
	public function m_count_proveedores_sin_usuario($paramPaginate,$paramDatos=FALSE)
	{
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('proveedor pr');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor');
		$this->db->join('tipo_documento_identidad tdi', 'pr.idtipodocumentoidentidad = tdi.idtipodocumentoidentidad'); 
		$this->db->join('usuario us', 'pr.idusuario = us.idusuario','left');
		if( !empty($paramDatos['tipo_proveedor']['id']) && !($paramDatos['tipo_proveedor']['id'] == 'all') ){ 
			$this->db->where('tpr.idtipoproveedor',$paramDatos['tipo_proveedor']['id']); 
		}
		$this->db->where('us.idusuario IS NULL');
		$this->db->where_in('pr.estado_pr', array(1,2,3)); // 1:activo 2:observado 3:inactivo 
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
	public function m_cargar_proveedores_cbo()
	{
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr'); 
		$this->db->from('proveedor pr'); 
		$this->db->where_in('pr.estado_pr', array(1,2)); // activo y observado 
		return $this->db->get()->result_array();
	}
	public function m_buscar_este_proveedor($datos)
	{
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr, 
			tpr.idtipoproveedor, tpr.descripcion_tpr'); 
		$this->db->from('proveedor pr');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor'); 
		$this->db->where_in('pr.estado_pr', array(1,2,3)); // activo  
		$this->db->where('pr.numero_documento_pr', $datos['num_documento']); 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	// VALIDACIONES 
	public function m_validar_proveedor_num_documento($numDocumento,$excepcion = FALSE,$idproveedor=NULL) 
	{
		$this->db->select('pr.idproveedor');
		$this->db->from('proveedor pr');
		$this->db->where_in('pr.estado_pr',array(1,2,3)); // activo observado anulado 
		$this->db->where('pr.numero_documento_pr',$numDocumento);
		if( $excepcion ){
			$this->db->where_not_in('pr.idproveedor',$idproveedor); 
		}
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}	
	public function m_cargar_proveedor_cbo($datos = FALSE){ 
		$this->db->select("pr.idproveedor, pr.nombre_comercial_pr");
		$this->db->from('proveedor pr');
		$this->db->where_in('pr.estado_pr', array(1,2,3)); //activo
		$this->db->order_by('pr.nombre_comercial_pr','ASC');
		return $this->db->get()->result_array();
	}

	public function m_cargar_proveedor_limite($datos)
	{
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr');
		$this->db->from('proveedor pr');
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		$this->db->like($datos['searchColumn'], $datos['searchText']);
		$this->db->order_by('pr.nombre_comercial_pr');
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'idtipoproveedor' => $datos['tipo_proveedor']['id'],
			'idtipodocumentoidentidad'=> 3, // ruc 
			'razon_social_pr' => strtoupper($datos['razon_social']), 
			'nombre_comercial_pr' => strtoupper($datos['nombre_comercial']), 
			'numero_documento_pr' => $datos['numero_documento'], 
			'direccion_pr' => $datos['direccion'],	
			'referencia_pr' => empty($datos['referencia']) ? NULL : $datos['referencia'], 
			'cod_distrito_pr' => $datos['iddistrito'],
			'cod_provincia_pr' => $datos['idprovincia'],
			'cod_departamento_pr' => $datos['iddepartamento'],
			'cod_sunasa_pr' => empty($datos['cod_sunasa']) ? NULL : $datos['cod_sunasa'], 
			'latitud' => empty($datos['lat']) ? NULL : $datos['lat'],
			'longitud' => empty($datos['lng']) ? NULL : $datos['lng'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s') 
		);
		return $this->db->insert('proveedor', $data);
	}	
	public function m_editar($datos){
		$data = array(
			'idtipoproveedor' => $datos['tipo_proveedor']['id'],
			'idtipodocumentoidentidad'=> 3, // ruc 
			'razon_social_pr' => strtoupper($datos['razon_social']), 
			'nombre_comercial_pr' => strtoupper($datos['nombre_comercial']), 
			'numero_documento_pr' => $datos['numero_documento'], 
			'direccion_pr' => $datos['direccion'],	
			'referencia_pr' => empty($datos['referencia']) ? NULL : $datos['referencia'], 
			'cod_distrito_pr' => $datos['iddistrito'],
			'cod_provincia_pr' => $datos['idprovincia'],
			'cod_departamento_pr' => $datos['iddepartamento'],
			'cod_sunasa_pr' => empty($datos['cod_sunasa']) ? NULL : $datos['cod_sunasa'], 
			'latitud' => empty($datos['lat']) ? NULL : $datos['lat'],
			'longitud' => empty($datos['lng']) ? NULL : $datos['lng'],
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idproveedor',$datos['idproveedor']);
		return $this->db->update('proveedor', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_pr' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idproveedor',$datos['idproveedor']);
		return $this->db->update('proveedor', $data);
	}
	public function m_cambiar_estado($datos)
	{
		$data = array(
			'estado_pr' => $datos['estado'],
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idproveedor',$datos['idproveedor']);
		return $this->db->update('proveedor', $data);
	}
	public function m_asociar_usuario_a_proveedor($datos)
	{
		$data = array(
			'idusuario' => $datos['idusuario']
		);
		$this->db->where('idproveedor',$datos['idproveedor']);
		return $this->db->update('proveedor', $data); 
	}


}
?>