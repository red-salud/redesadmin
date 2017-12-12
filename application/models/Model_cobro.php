<?php
class Model_cobro extends CI_Model { 
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_cobros($paramPaginate, $paramDatos){ 
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE);
		$this->db->select('cob.cob_id, cob_fechCob, cob_vezCob, cob_importe, cob_moneda, cob_iniCobertura, cob_finCobertura, cob.facturado, 
			cert.cert_id, cert.cert_num, cert.cert_iniVig ,pl.idplan, pl.nombre_plan, pl.codigo_plan, el.idelemento, el.descripcion_ele, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc, cont_telf, ce.idclienteempresa, ce.nombre_comercial_cli'); 
		$this->db->from('cobro cob'); 
		$this->db->join('certificado cert','cob.cert_id = cert.cert_id'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('plan pl','cob.plan_id = pl.idplan'); 
		$this->db->join('elemento el','pl.codigo_plan = el.codigo_elemento','left'); // agregamos elemento para el boletaje masivo 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cob.cob_fechCob BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		// $this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		// $this->db->where_in('cob.estado_movimiento', array(1)); // activo 
		if(!empty($paramDatos['plan']) && $paramDatos['plan']['id'] !== 'ALL' ){ 
			$this->db->where('pl.idplan', $paramDatos['plan']['id']); 
		}
		if( !empty($paramDatos['facturado']) ){ 
			$this->db->where('cob.facturado', $paramDatos['facturado']); 
		}
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){ 
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
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
	public function m_count_cobros($paramPaginate, $paramDatos)
	{
		$this->db->select('COUNT(*) AS contador'); 
		$this->db->from('cobro cob'); 
		$this->db->join('certificado cert','cob.cert_id = cert.cert_id'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('plan pl','cob.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cob.cob_fechCob BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		// $this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		// $this->db->where_in('cob.estado_movimiento', array(1)); // activo 
		if(!empty($paramDatos['plan']) && $paramDatos['plan']['id'] !== 'ALL' ){ 
			$this->db->where('pl.idplan', $paramDatos['plan']['id']); 
		}
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){ 
			foreach ($paramPaginate['searchColumn'] as $key => $value) { 
				if( !empty($value) ){ 
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_actualizar_cobros_log_seguimiento($datos)
	{
		$data = array( 
			'boletaje_log' => $datos['message'],
			'facturado' => $datos['facturado'],
			'error_log' => $datos['error'],
			'idmovimiento' => $datos['idmovimiento'] 
		); 
		$this->db->where('cob_id',$datos['idcobro']); 
		return $this->db->update('cobro', $data); 
	} 
}
?>