<?php
class Model_serie extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_serie_cbo($datos = FALSE){ 
		$this->db->select('se.idserie, se.numero_serie, se.descripcion_ser');
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin',$this->sessionRS['idempresaadmin']); 
		$this->db->order_by('se.numero_serie','ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_posicion_correlativo($datos)
	{
		$this->db->select('se.idserie, se.numero_serie, tdm.idtipodocumentomov, tdm.descripcion_tdm, tds.correlativo_actual');
		$this->db->from('serie se');
		$this->db->join('tipo_documento_serie tds', 'se.idserie = tds.idserie');
		$this->db->join('tipo_documento_mov tdm', 'tds.idtipodocumentomov = tdm.idtipodocumentomov');
		$this->db->where('se.idempresaadmin',$this->sessionRS['idempresaadmin']); 
		$this->db->where('tdm.idtipodocumentomov',$datos['tipo_documento_mov']['id']);
		$this->db->where('se.idserie',$datos['serie']['id']);
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_actualizar_correlativo($datos)
	{
		$data = array(
			'correlativo_actual' => $datos['nuevo_correlativo'] 
		);
		$this->db->where('idtipodocumentomov',$datos['tipo_documento_mov']['id']);
		$this->db->where('idserie',$datos['serie']['id']);
		return $this->db->update('tipo_documento_serie', $data); 
	}
}
?>