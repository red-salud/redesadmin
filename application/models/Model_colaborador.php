<?php
class Model_colaborador extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	//CARGAR PERFIL
	public function m_cargar_perfil($idusuario, $idempresa = FALSE){ 
		$this->db->select('co.idcolaborador, co.nombres_col, co.ap_paterno_col, co.ap_materno_col, co.correo_laboral, co.numero_documento_col, co.fecha_nacimiento_col, co.nombre_foto, 
			us.idusuario ,us.username, us.ultimo_inicio_sesion, 
			tu.idtipousuario, tu.descripcion_tu, tu.key_tu, 
			ea.idempresaadmin, ea.razon_social_ea, ea.nombre_comercial_ea, ea.ruc_ea, ea.nombre_logo',FALSE);
		$this->db->from('colaborador co, empresa_admin ea');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario');
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario');
		// $this->db->join('', 'uea.idempresaadmin = ea.idempresaadmin');
		$this->db->where('us.idusuario', $idusuario);
		$this->db->where('ea.estado_ea', 1);
		$this->db->where('co.estado_col', 1);
		if( $idempresa ){ 
			$this->db->where('ea.idempresaadmin', $idempresa);
		}
		$this->db->order_by('ea.razon_social_ea', 'ASC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}	
	public function m_cargar_colaborador($paramPaginate=FALSE){ 
		$this->db->select('co.idcolaborador, co.nombres_col, co.ap_paterno_col, co.ap_materno_col, co.correo_laboral, co.numero_documento_col, co.fecha_nacimiento_col, co.nombre_foto, co.celular_col, 
			us.idusuario, us.username,us.password_view, us.ultimo_inicio_sesion, 
			tu.idtipousuario, tu.descripcion_tu, tu.key_tu');
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario AND us.estado_us = 1','left'); 
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario');
		$this->db->where('co.estado_col', 1);
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
	public function m_count_colaborador($paramPaginate=FALSE){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('colaborador co');
		$this->db->join('usuario us', 'co.idusuario = us.idusuario AND us.estado_us = 1','left'); 
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario');
		$this->db->where('co.estado_col', 1);
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
	public function m_cargar_colaborador_cbo(){
		$this->db->select("co.idcolaborador, CONCAT(co.nombres_col, ' ', co.ap_paterno_col, ' ', co.ap_materno_col) AS colaborador",FALSE);
		$this->db->from('colaborador co');
		$this->db->where('co.estado_col', 1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'idusuario' => $datos['idusuario'],
			'idcargo' => $datos['idcargo'],
			'idtipodocumentopersona' => $datos['tipodocumentopersona']['id'],
			'nombres_col' => strtoupper_total($datos['nombres']),
			'ap_paterno_col' => strtoupper_total($datos['ap_paterno']),
			'ap_materno_col' => strtoupper_total($datos['ap_materno']),
			'fecha_nacimiento_col' => empty($datos['fecha_nacimiento'])? NULL : darFormatoYMD($datos['fecha_nacimiento']), 
			'numero_documento_col'=> $datos['num_documento'],
			'estado_civil'=> empty($datos['estado_civil']) ? NULL : $datos['estado_civil'],
			'correo_laboral' => empty($datos['email'])? NULL : strtoupper_total($datos['email']), 
			'celular_col'=> $datos['celular'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('colaborador', $data);
	}	
	public function m_editar_foto($datos){
		$data = array(
			'nombre_foto' => $datos['nombre_foto'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcolaborador',$datos['idcolaborador']);
		return $this->db->update('colaborador', $data);
	}	
	public function m_editar($datos){
		$data = array( 
			'nombres_col' => strtoupper_total($datos['nombres']),
			'ap_paterno_col' => strtoupper_total($datos['ap_paterno']),
			'ap_materno_col' => strtoupper_total($datos['ap_materno']),
			'fecha_nacimiento_col' => empty($datos['fecha_nacimiento'])? NULL : darFormatoYMD($datos['fecha_nacimiento']), 
			'numero_documento_col'=> $datos['num_documento'],
			'estado_civil'=> empty($datos['estado_civil']) ? NULL : $datos['estado_civil'],
			'correo_laboral' => empty($datos['email'])? NULL : strtoupper_total($datos['email']), 
			'celular_col'=> $datos['celular'],
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idcolaborador',$datos['id']);
		return $this->db->update('colaborador', $data);
	}

	public function m_anular($datos)
	{
		$data = array(
			'estado_col' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcolaborador',$datos['id']);
		return $this->db->update('colaborador', $data);
	}

}
?>