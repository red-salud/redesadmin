<?php
class Model_acceso extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
 	// ACCESO AL SISTEMA
	public function m_logging_user($data){
		$this->db->select('COUNT(*) AS logged, us.idusuario, us.estado_us, us.username, 
			tu.idtipousuario, tu.key_tu',FALSE); 
		$this->db->from('usuario us');
		$this->db->join('colaborador co', 'us.idusuario = co.idusuario AND co.estado_col = 1');
		$this->db->join('tipo_usuario tu', 'us.idtipousuario = tu.idtipousuario AND tu.estado_tu = 1');
		$this->db->where('us.username', $data['usuario']);
		$this->db->where('us.password', do_hash($data['password'] , 'md5'));
		$this->db->where('us.estado_us', 1); 
		$this->db->group_by('us.idusuario'); 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_combo_empresa_admin_matriz_session($idusuario=FALSE) 
	{
		/* LOGICA MULTIEMPRESA: */ 
		
		$this->db->select('us.idusuario, ea.idempresaadmin, ea.razon_social_ea AS empresa_admin'); 
		$this->db->from('usuario us, empresa_admin ea'); 
		$this->db->where('ea.estado_ea', 1); 
		$this->db->where('us.estado_us', 1); 
		if( empty($idusuario) ){
			$this->db->where('idusuario', $this->sessionRS['idusuario']);
		}else{
			$this->db->where('idusuario', $idusuario);
		}
		return $this->db->get()->result_array();
	}
	public function m_cambiar_empresa_session($idempresaadmin,$idusuario)
	{
		$this->db->select('ea.idempresaadmin, ea.razon_social_ea, ea.nombre_comercial_ea, ea.ruc_ea, ea.nombre_logo, us.idusuario');
		$this->db->from('usuario us, empresa_admin ea');
		$this->db->where('ea.idempresaadmin',$idempresaadmin); // empresaadmin
		$this->db->where('us.idusuario',$idusuario);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_actualizar_datos_usuario_ultima_sesion($datos)
	{
		$data = array(
			'ultimo_inicio_sesion' => date('Y-m-d H:i:s'),
			'ip_address'=>  $_SERVER['REMOTE_ADDR']  
		);
		$this->db->where('idusuario',$datos['idusuario']);
		return $this->db->update('usuario', $data);
	}
}
?>