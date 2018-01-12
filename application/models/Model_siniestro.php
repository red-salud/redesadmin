<?php
class Model_siniestro extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_siniestros($paramPaginate, $paramDatos){ 
		$this->db->select("CONCAT(COALESCE(ase.aseg_nom1,''), ' ', COALESCE(ase.aseg_nom2,''), ' ', COALESCE(ase.aseg_ape1,''), ' ', COALESCE(ase.aseg_ape2,'')) AS asegurado",FALSE);
		$this->db->select('ase.aseg_id, ase.aseg_numDoc, ase.aseg_telf, sin.idsiniestro, sin.fecha_atencion, sin.num_orden_atencion, sin.estado_siniestro,
			cert.cert_id, cert.cert_num, cert.cert_iniVig, pl.idplan, pl.nombre_plan, pl.codigo_plan, prov.idproveedor, prov.nombre_comercial_pr, 
			ce.idclienteempresa, ce.nombre_comercial_cli, esp.idespecialidad, esp.nombre_esp, ci.idcita'); 
		$this->db->from('siniestro sin'); 
		$this->db->join('asegurado ase','sin.idasegurado = ase.aseg_id'); 
		$this->db->join('proveedor prov','sin.idproveedor = prov.idproveedor'); 
		$this->db->join('especialidad esp','sin.idespecialidad = esp.idespecialidad'); 
		$this->db->join('certificado cert','sin.idcertificado = cert.cert_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->join('cita ci','sin.idcita = ci.idcita','left'); 
		$this->db->where('sin.fecha_atencion BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
		// $this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		// $this->db->where_in('cob.estado_movimiento', array(1)); // activo 
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
	public function m_count_siniestros($paramPaginate, $paramDatos)
	{
		$this->db->select('COUNT(*) AS contador'); 
		$this->db->from('siniestro sin'); 
		$this->db->join('asegurado ase','sin.idasegurado = ase.aseg_id'); 
		$this->db->join('proveedor prov','sin.idproveedor = prov.idproveedor'); 
		$this->db->join('especialidad esp','sin.idespecialidad = esp.idespecialidad'); 
		$this->db->join('certificado cert','sin.idcertificado = cert.cert_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('cliente_empresa ce','pl.idclienteempresa = ce.idclienteempresa'); 
		$this->db->where('sin.fecha_atencion BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto'])); 
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
	public function m_cargar_ultimo_siniestro() 
	{
		$this->db->select('sin.idsiniestro, sin.num_orden_atencion'); 
		$this->db->from('siniestro sin'); 
		$this->db->order_by('sin.idsiniestro','DESC'); 
		$this->db->limit(1); 
		$fData = $this->db->get()->row_array(); 
		return $fData; 
	}
	public function m_aperturar_atencion($datos)
	{
		$data = array(
			'idasegurado' => $datos['asegurado_cert']['idasegurado'],
			'idcertificado' => $datos['asegurado_cert']['idcertificado'],
			'idareahospitalaria' => 1, // consulta externa 
			'idhistoria' => $datos['asegurado_cert']['idhistoria'],
			'idproveedor'=> $datos['proveedor']['id'], 
			'idespecialidad'=> $datos['producto']['idespecialidad'], 
			//'idproducto'=> $datos['producto']['id'],
			'fecha_atencion'=> $datos['fecha'], 
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s'),
			'num_orden_atencion' => $datos['num_orden_atencion'],
			'idcita'=> $datos['idcita'] 
		);
		return $this->db->insert('siniestro', $data); 
	}
	public function m_anular($datos)
	{
		$data = array( 
			'estado_siniestro' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		);
		$this->db->where('idsiniestro',$datos['idsiniestro']); 
		return $this->db->update('siniestro', $data); 
	}
}
?>