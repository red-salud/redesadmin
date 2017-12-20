<?php
class Model_tipo_documento_identidad extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_tipo_documento_identidad_cbo($datos = FALSE){ 
		$this->db->select('tdi.idtipodocumentoidentidad, tdi.descripcion_tdi, tdi.abreviatura_tdi, tdi.destino_tdi, tdi.estado_tdi');
		$this->db->from('tipo_documento_identidad tdi');
		$this->db->where('tdi.estado_tdi', 1); // activo
		if(!empty($datos['destino'])){
			if( $datos['destino'] == 1 ){ // proveedor empresa
				$this->db->where('tdi.destino_tdi', 1); // activo 
			}
		}
		$this->db->order_by('tdi.descripcion_tdi','DESC');
		return $this->db->get()->result_array();
	}
}
?>