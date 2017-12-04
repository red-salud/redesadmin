<?php
class Model_certificado extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_certificados($paramPaginate, $paramDatos){ 
		$this->db->select('(SELECT MAX(fecha_atencion) AS ultima_atencion FROM siniestro sin WHERE sin.idcertificado = cert.cert_id LIMIT 1) AS ultima_atencion',FALSE); 
		$this->db->select('(SELECT COUNT(*) AS contador FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS cant_cobros',FALSE); 
		$this->db->select('(SELECT MAX(cob_finCobertura) AS ultima_cobertura FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS ultima_cobertura',FALSE); 
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE); 
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_iniVig, cert.cert_finVig, cert.cert_numpropuesta, cert.cert_estado, cert.cert_upProv, 
			pl.idplan, pl.nombre_plan, pl.codigo_plan, pl.dias_carencia, pl.dias_mora, pl.dias_atencion, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc, ce.idclienteempresa, ce.nombre_comercial_cli'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cert.cert_iniVig BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' . $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		// $this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		// $this->db->where_in('estado_movimiento', array(1)); // activo 
		if(!empty($paramDatos['plan']) && $paramDatos['plan']['id'] !== 'ALL' ){ 
			$this->db->where('pl.idplan', $paramDatos['plan']['id']); 
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
	public function m_count_certificados($paramPaginate, $paramDatos)
	{
		$this->db->select('COUNT(*) AS contador'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cert.cert_iniVig BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' . $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
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
	public function m_cargar_certificados_detalle($paramPaginate, $paramDatos)
	{
		$this->db->select('(SELECT MAX(fecha_atencion) AS ultima_atencion FROM siniestro sin WHERE sin.idcertificado = cert.cert_id LIMIT 1) AS ultima_atencion',FALSE); 
		$this->db->select('(SELECT COUNT(*) AS contador FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS cant_cobros',FALSE); 
		$this->db->select('(SELECT MAX(cob_finCobertura) AS ultima_cobertura FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS ultima_cobertura',FALSE); 
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE);
		$this->db->select("CONCAT(COALESCE(aseg_nom1,''), ' ', COALESCE(aseg_nom2,''), ' ', COALESCE(aseg_ape1,''), ' ', COALESCE(aseg_ape2,'')) AS asegurado",FALSE);
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_iniVig, cert.cert_finVig, cert.cert_numpropuesta, cert.cert_estado, cert.cert_upProv, 
			pl.idplan, pl.nombre_plan, pl.codigo_plan, pl.dias_carencia, pl.dias_mora, pl.dias_atencion, cas.certase_id, cas.certase_conse, 
			as.aseg_id, as.aseg_nom1, as.aseg_nom2, as.aseg_ape1, as.aseg_ape2, as.aseg_numDoc, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc, ce.idclienteempresa, ce.nombre_comercial_cli'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cert.cert_iniVig BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		if(!empty($paramDatos['plan']) && $paramDatos['plan']['id'] !== 'ALL' ){ 
			$this->db->where('pl.idplan', $paramDatos['plan']['id']); 
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
	public function m_count_certificados_detalle($paramPaginate, $paramDatos)
	{
		$this->db->select("COUNT(*) AS contador"); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cert.cert_iniVig BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		if(!empty($paramDatos['plan']) && $paramDatos['plan']['id'] !== 'ALL' ){ 
			$this->db->where('pl.idplan', $paramDatos['plan']['id']); 
		}
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){ 
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_asegurados_de_certificado($datos)
	{
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE);
		$this->db->select("CONCAT(COALESCE(aseg_nom1,''), ' ', COALESCE(aseg_nom2,''), ' ', COALESCE(aseg_ape1,''), ' ', COALESCE(aseg_ape2,'')) AS asegurado",FALSE);
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_numpropuesta, cert.cert_estado, 
			pl.idplan, pl.nombre_plan, pl.codigo_plan, cas.certase_id, cas.cert_iniVig, cas.cert_finVig, cas.certase_conse, 
			as.aseg_id, as.aseg_nom1, as.aseg_nom2, as.aseg_ape1, as.aseg_ape2, as.aseg_numDoc, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc, ce.idclienteempresa, ce.nombre_comercial_cli'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('cert.cert_id',$datos['idcertificado']);
		return $this->db->get()->result_array();
	}
	public function m_cargar_certificados_de_asegurados($datos) // cert_iniVig 
	{
		$this->db->select("DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(aseg_fechNac)), '%Y')+0 AS edad",FALSE);
		$this->db->select('(
			SELECT prov.nombre_comercial_pr  
			FROM siniestro sin 
			INNER JOIN proveedor prov ON sin.idproveedor = prov.idproveedor 
			WHERE sin.idcertificado = cert.cert_id 
			ORDER BY prov.idproveedor DESC 
			LIMIT 1) AS lugar_ultima_atencion',FALSE); 
		$this->db->select('(SELECT MAX(fecha_atencion) AS ultima_atencion FROM siniestro sin WHERE sin.idcertificado = cert.cert_id LIMIT 1) AS ultima_atencion',FALSE); 
		$this->db->select('(SELECT COUNT(*) AS contador FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS cant_cobros',FALSE); 
		$this->db->select('(SELECT MAX(cob_finCobertura) AS ultima_cobertura FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS ultima_cobertura',FALSE);
		$this->db->select("CONCAT(COALESCE(aseg_nom1,''), ' ', COALESCE(aseg_nom2,''), ' ', COALESCE(aseg_ape1,''), ' ', COALESCE(aseg_ape2,'')) AS asegurado",FALSE); 
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_numpropuesta, cert.cert_estado, cas.certase_id, cert.cert_iniVig, cert.cert_finVig, cert.cert_upProv, 
			as.aseg_id, as.aseg_fechNac, as.aseg_nom1, as.aseg_nom2, as.aseg_ape1, as.aseg_ape2, as.aseg_numDoc, as.aseg_direcc, as.aseg_telf, 
			as.aseg_sexo, as.aseg_email, pl.idplan, pl.nombre_plan, pl.dias_carencia, pl.dias_mora, pl.dias_atencion, pl.codigo_plan, pl.prima_monto'); 
		$this->db->from('certificado cert'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		if( !empty($datos['search']) ){
			$this->db->like("CONCAT(COALESCE(aseg_nom1,''), ' ', COALESCE(aseg_nom2,''), ' ', COALESCE(aseg_ape1,''), ' ', COALESCE(aseg_ape2,'')) ",$datos['search']); 
		}
		if( !empty($datos['numero_documento']) ){ 
			$this->db->where('as.aseg_numDoc',$datos['numero_documento']); 
		}
		// $this->db->limit(20); 
		return $this->db->get()->result_array(); 
	}
	public function m_cargar_cobros_de_certificado($paramPaginate, $paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE);
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_numpropuesta, cert.cert_estado, 
			pl.idplan, pl.nombre_plan, pl.codigo_plan, cob.cob_id, cob.cob_fechCob, cob.cob_vezCob, cob.cob_importe, cob.cob_moneda, 
			cob.cob_iniCobertura, cob.cob_finCobertura, dcob.cobDet_medioPago, dcob.cobDet_frec, mp.descripcion_mp, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc'); 
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('cobro cob','cert.cert_id = cob.cert_id'); 
		$this->db->join('cobro_deta dcob','cob.cob_id = dcob.cob_id','left'); 
		$this->db->join('medio_pago mp','dcob.cobDet_medioPago = mp.idmediopago','left'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->where('cert.cert_id',$paramDatos['idcertificado']);
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
	public function m_count_cobros_de_certificado($paramPaginate, $paramDatos)
	{
		$this->db->select("COUNT(*) AS contador");
		$this->db->from('certificado cert'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('cobro cob','cert.cert_id = cob.cert_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->where('cert.cert_id',$paramDatos['idcertificado']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){ 
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key, $value, FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_buscar_certificados_y_cobros_por_asegurado_contratante($datos = NULL,$arrNumDocCont = NULL) 
	{
		$this->db->select('(SELECT MAX(fecha_atencion) AS ultima_atencion FROM siniestro sin_sc WHERE sin_sc.idcertificado = cert.cert_id LIMIT 1) AS ultima_atencion',FALSE); 
		$this->db->select('(SELECT MAX(cob_finCobertura) AS ultima_cobertura FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS ultima_cobertura',FALSE); 
		$this->db->select('(SELECT COUNT(*) AS contador FROM cobro co WHERE co.cert_id = cert.cert_id LIMIT 1) AS cant_cobros',FALSE); 
		$this->db->select("CONCAT(COALESCE(cont_nom1,''), ' ', COALESCE(cont_nom2,''), ' ', COALESCE(cont_ape1,''), ' ', COALESCE(cont_ape2,'')) AS contratante",FALSE);
		$this->db->select("CONCAT(COALESCE(aseg_nom1,''), ' ', COALESCE(aseg_nom2,''), ' ', COALESCE(aseg_ape1,''), ' ', COALESCE(aseg_ape2,'')) AS asegurado",FALSE);
		$this->db->select('cert.cert_id, cert.cert_num, cert.cert_numpropuesta, cert.cert_estado, cert.cert_upProv, 
			pl.idplan, pl.nombre_plan, pl.dias_carencia, pl.dias_mora, pl.dias_atencion, pl.codigo_plan, cas.certase_id, cas.cert_iniVig, cas.cert_finVig, cas.certase_conse, 
			as.aseg_id, as.aseg_nom1, as.aseg_nom2, as.aseg_ape1, as.aseg_ape2, as.aseg_numDoc, 
			cont.cont_id, cont_nom1, cont_nom2, cont_ape1, cont_ape2, cont_numDoc, ce.idclienteempresa, ce.nombre_comercial_cli, 
			cob.cob_id, cob.cob_fechCob, cob.cob_vezCob, cob.cob_importe, cob.cob_moneda, 
			cob.cob_iniCobertura, cob.cob_finCobertura, dcob.cobDet_medioPago, dcob.cobDet_frec, mp.descripcion_mp, 
			sin.idsiniestro, sin.fecha_atencion, esp.idespecialidad, esp.nombre_esp, prov.idproveedor, prov.nombre_comercial_pr'); 
		$this->db->from('certificado cert'); 
		$this->db->join('cobro cob','cert.cert_id = cob.cert_id','left'); 
		$this->db->join('cobro_deta dcob','cob.cob_id = dcob.cob_id','left'); 
		$this->db->join('medio_pago mp','dcob.cobDet_medioPago = mp.idmediopago','left'); 
		$this->db->join('contratante cont','cert.cont_id = cont.cont_id'); 
		$this->db->join('certificado_asegurado cas','cert.cert_id = cas.cert_id'); 
		$this->db->join('asegurado as','cas.aseg_id = as.aseg_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa');
		// siniestro 
		$this->db->join('siniestro sin','as.aseg_id = sin.idasegurado AND as.aseg_numDoc = "'.$datos['cuadro_busqueda'].'"','left'); 
		$this->db->join('especialidad esp','sin.idespecialidad = esp.idespecialidad','left'); 
		$this->db->join('proveedor prov','sin.idproveedor = prov.idproveedor','left'); 
		if( !empty($datos['cuadro_busqueda']) ){ 
			$this->db->where('cont.cont_numDoc',$datos['cuadro_busqueda']);
		}
		if( !empty($arrNumDocCont) ){
			$this->db->where_in('cont.cont_numDoc',$arrNumDocCont);
		}
		$this->db->order_by('cert.cert_id','DESC');
		return $this->db->get()->result_array();
	}
	public function m_activar_certificado_manual($datos)
	{
		$data = array(
			'cert_upProv' => 1 // activacion manual
		);
		$this->db->where('cert_id',$datos['idcertificado']);
		return $this->db->update('certificado', $data);
	}
	public function m_deshacer_activar_certificado_manual($datos)
	{
		$data = array(
			'cert_upProv' => NULL // deshacer activacion manual
		);
		$this->db->where('cert_id',$datos['idcertificado']);
		return $this->db->update('certificado', $data);
	}
}
?>