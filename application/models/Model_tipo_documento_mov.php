<?php
class Model_tipo_documento_mov extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_tipo_documento_mov_cbo($datos = FALSE){ 
		$this->db->select('tdm.idtipodocumentomov, tdm.descripcion_tdm, tdm.key_tdm, tdm.porcentaje_imp, tdm.abreviatura_tdm'); 
		$this->db->from('tipo_documento_mov tdm'); 
		$this->db->where('tdm.estado_tdm', 1); // activo 
		$this->db->order_by('tdm.idtipodocumentomov','ASC'); 
		return $this->db->get()->result_array(); 
	}
}
?>