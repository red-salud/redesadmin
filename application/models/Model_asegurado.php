<?php
class Model_asegurado extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_buscar_contratante_desde_asegurado($datos){ 
		$this->db->select('cont.cont_id, cont_numDoc'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->where('as.aseg_numDoc',trim($datos['doc_asegurado'])); 
		$this->db->group_by('cont.cont_id');
		return $this->db->get()->result_array();
	} 
	public function m_editar_asegurado_inline($datos){ 
		$data = array(
			'aseg_email' => $datos['correo_electronico'],
			'aseg_sexo' => $datos['sexo'],
			'aseg_fechNac' => darFormatoYMD($datos['fecha_nacimiento_edit']),
			'aseg_telf' => $datos['telefono'],
			'aseg_direcc' => $datos['direccion'] 
		);
		$this->db->where('aseg_id',$datos['idasegurado']);
		return $this->db->update('asegurado', $data);
	}
}
?>