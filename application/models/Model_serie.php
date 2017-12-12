<?php
class Model_serie extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_series()
	{
		$this->db->select("se.idserie, se.numero_serie, se.descripcion_ser");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionRS['idempresaadmin']); 
		return $this->db->get()->result_array();
	}
	public function m_cargar_serie_cbo($datos = FALSE){ 
		$this->db->select('se.idserie, se.numero_serie, se.descripcion_ser');
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin',$this->sessionRS['idempresaadmin']); 
		$this->db->order_by('se.numero_serie','ASC');
		return $this->db->get()->result_array();
	}
	public function m_cargar_esta_serie($numeroSerie)
	{
		$this->db->select("se.idserie, se.numero_serie, se.descripcion_ser");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionRS['idempresaadmin']); 
		$this->db->where('se.numero_serie',$numeroSerie);
		$this->db->limit(1);
		$fSerie = $this->db->get()->row_array();
		return $fSerie; 
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
	public function m_validar_serie_correlativo_existe($idserie,$idtipodocumento)
	{
		$this->db->select("tds.idtipodocumentoserie, tds.idserie, tds.idtipodocumentomov, tds.correlativo_actual"); 
		$this->db->from('tipo_documento_serie tds');
		$this->db->where('tds.idserie',$idserie);
		$this->db->where('tds.idtipodocumentomov',$idtipodocumento);
		$this->db->limit(1);
		$fSerieCorr = $this->db->get()->row_array();
		return $fSerieCorr; 
	}
	public function m_validar_num_serie($numeroSerie) 
	{
		$this->db->select("se.idserie");
		$this->db->from('serie se');
		$this->db->where('se.idempresaadmin', $this->sessionRS['idempresaadmin']);
		$this->db->where('se.numero_serie', $numeroSerie);
		$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'numero_serie' => strtoupper($datos['numero_serie']),	
			'descripcion_ser' => strtoupper($datos['descripcion_ser']),
			'idempresaadmin' => $this->sessionRS['idempresaadmin'] 
		);
		return $this->db->insert('serie', $data); 
	}

	// CORRELATIVO ACTUAL 
	public function m_registrar_correlativo_actual($datos)
	{
		$data = array(
			'idtipodocumentomov' => $datos['idtipodocumentomov'],
			'idserie' => $datos['idserie'],
			'correlativo_actual' => $datos['correlativo']
		);
		return $this->db->insert('tipo_documento_serie', $data); 
	}
	public function m_editar_correlativo_actual($datos)
	{
		$data = array( 
			'correlativo_actual' => $datos['correlativo'] 
		);
		$this->db->where('idserie',$datos['idserie']);
		$this->db->where('idtipodocumentomov',$datos['idtipodocumentomov']);
		return $this->db->update('tipo_documento_serie', $data);
	}
	public function m_actualizar_serie_correlativo_por_movimiento($datos)
	{
		$this->db->set('correlativo_actual', 'correlativo_actual + 1', FALSE);
		$this->db->where('idserie',$datos['serie']['id']);
		$this->db->where('idtipodocumentomov',$datos['tipo_documento_mov']['id']);
		return $this->db->update('tipo_documento_serie');
	}
}
?>