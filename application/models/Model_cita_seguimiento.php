<?php
class Model_cita_seguimiento extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_seguimiento_de_cita($datos)
	{
		$this->db->select('ci.idcita, ci.fecha_cita, cse.idcitaseguimiento, cse.fecha_registro, cse.contenido, cse.estado_cs, 
			us.idusuario, us.username, 
			co.nombres_col, co.ap_paterno_col, co.ap_materno_col'); 
		$this->db->from('cita ci'); 
		$this->db->join('cita_seguimiento cse','ci.idcita = cse.idcita'); 
		$this->db->join('usuario us','cse.idusuario = us.idusuario'); 
		$this->db->join('colaborador co','us.idusuario = co.idusuario'); 
		$this->db->where('ci.idcita',$datos['idcita']); 
		$this->db->where('cse.estado_cs',1); 
		$this->db->order_by('cse.idcitaseguimiento','DESC'); 
		return $this->db->get()->result_array(); 
	}
	public function m_obtener_esta_cita_seguimiento($datos)
	{
		$this->db->select("CONCAT_WS(' ',aseg.aseg_nom1, aseg.aseg_nom2, aseg.aseg_ape1, aseg.aseg_ape2) AS asegurado",FALSE);
		$this->db->select('ci.idcita, ci.fecha_cita, cse.idcitaseguimiento, cse.fecha_registro, cse.contenido, cse.estado_cs, 
			us.idusuario, us.username, co.nombres_col, co.ap_paterno_col, co.ap_materno_col, 
			aseg.aseg_id, aseg.aseg_nom1, aseg.aseg_nom2, aseg.aseg_ape1, aseg.aseg_ape2'); 
		$this->db->from('cita ci'); 
		$this->db->join('cita_seguimiento cse','ci.idcita = cse.idcita'); 
		$this->db->join('asegurado aseg','ci.idasegurado = aseg.aseg_id'); 
		$this->db->join('usuario us','cse.idusuario = us.idusuario'); 
		$this->db->join('colaborador co','us.idusuario = co.idusuario'); 
		$this->db->where('cse.idcitaseguimiento',$datos['idcitaseguimiento']); 
		return $this->db->get()->row_array(); 
	} 
	public function m_registrar($datos)
	{
		$data = array( 
			'idcita' => $datos['idcita'],
			'fecha_registro' => date('Y-m-d H:i:s'),
			'idusuario'=> $this->sessionRS['idusuario'], 
			'contenido'=> $datos['contenido'] 
		);
		return $this->db->insert('cita_seguimiento', $data);
	} 
	public function m_anular($datos)
	{
		$data = array(
			'estado_cs' => 0 
		);
		$this->db->where('idcitaseguimiento',$datos['idcitaseg']);
		return $this->db->update('cita_seguimiento', $data);
	}
}
?>