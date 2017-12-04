<?php
class Model_cita extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_citas($datos)
	{
		$this->db->select("CONCAT(COALESCE(as.aseg_nom1,''), ' ', COALESCE(as.aseg_nom2,''), ' ', COALESCE(as.aseg_ape1,''), ' ', COALESCE(as.aseg_ape2,'')) AS asegurado",FALSE);
		$this->db->select('ci.idcita, ci.fecha_cita, ci.hora_cita_inicio, ci.hora_cita_fin, ci.observaciones_cita, ci.estado_cita, ci.es_atencion, 
			cas.certAse_id, as.aseg_id, prov.idproveedor, prov.nombre_comercial_pr, prov.razon_social_pr, prov.numero_documento_pr, 
			prod.idproducto, prod.descripcion_prod, tpr.idtipoproducto, tpr.descripcion_tp, tpr.key_tp, esp.idespecialidad, esp.nombre_esp, 
			pl.idplan, pl.nombre_plan'); 
		$this->db->from('cita ci'); 
		$this->db->join('certificado_asegurado cas','ci.idcertificadoasegurado = cas.certAse_id'); 
		$this->db->join('certificado cert','cas.cert_id = cert.cert_id'); 
		$this->db->join('plan pl','cert.plan_id = pl.idplan'); 
		$this->db->join('asegurado as','ci.idasegurado = as.aseg_id'); 
		$this->db->join('proveedor prov','ci.idproveedor = prov.idproveedor'); 
		$this->db->join('producto prod','ci.idproducto = prod.idproducto'); 
		$this->db->join('tipo_producto tpr','prod.idtipoproducto = tpr.idtipoproducto'); 
		$this->db->join('especialidad esp','ci.idespecialidad = esp.idespecialidad'); 
		$this->db->where('ci.fecha_cita BETWEEN '. $this->db->escape(darFormatoYMD($datos['desde']).' 00:00').' AND '. $this->db->escape( darFormatoYMD($datos['hasta']).' 23:59')); 
		if(!empty($datos['proveedor']['id'])){
			$this->db->where('ci.idproveedor', $datos['proveedor']['id']);
		}
		$this->db->where_in('ci.estado_cita',array(1,2)); // reservado y atendido 
		return $this->db->get()->result_array();
	}
	public function m_obtener_esta_cita($idcita)
	{
		$this->db->select('ci.idcita, ci.fecha_cita, ci.hora_cita_inicio, ci.hora_cita_fin, ci.observaciones_cita, ci.estado_cita, ci.es_atencion'); 
		$this->db->from('cita ci'); 
		$this->db->where('ci.idcita', $idcita); 
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_registrar($datos)
	{
		$data = array(
			'idasegurado' => $datos['asegurado_cert']['idasegurado'],
			'idcertificadoasegurado' => $datos['asegurado_cert']['idcertificadoasegurado'],
			'idproveedor'=> $datos['proveedor']['id'], 
			'idusuario'=>$this->sessionRS['idusuario'], 
			'idproducto'=>$datos['producto']['id'], 
			'idespecialidad'=>$datos['producto']['idespecialidad'], 
			'fecha_cita'=>$datos['fecha'], 
			'hora_cita_inicio'=> $datos['hora_desde_str'],
			'hora_cita_fin'=> $datos['hora_hasta_str'],
			'observaciones_cita'=> empty($datos['observaciones']) ? NULL : $datos['observaciones'],
			'idempresaadmin'=> $this->sessionRS['idempresaadmin'],
			'createdat' => date('Y-m-d H:i:s'),
			'updatedat' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('cita', $data);
	}
	public function m_editar($datos){ 
		$data = array( 
			'idproveedor'=> $datos['proveedor']['id'], 
			'idproducto'=>$datos['producto']['id'], 
			'idespecialidad'=>$datos['producto']['idespecialidad'], 
			'fecha_cita'=>$datos['fecha'], 
			'hora_cita_inicio'=> $datos['hora_desde_str'],
			'hora_cita_fin'=> $datos['hora_hasta_str'],
			'observaciones_cita'=> empty($datos['observaciones']) ? NULL : $datos['observaciones'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcita',$datos['id']);
		return $this->db->update('cita', $data);
	}
	public function m_mover_cita($datos)
	{
		$data = array( 
			'fecha_cita'=>$datos['fecha'], 
			'hora_cita_inicio'=> $datos['hora_desde'],
			'hora_cita_fin'=> $datos['hora_hasta'],
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcita',$datos['idcita']);
		return $this->db->update('cita', $data);
	}
	public function m_anular($datos)
	{
		// var_dump($datos);exit();
		$data = array(
			'estado_cita' => 0,
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcita',$datos['id']);
		return $this->db->update('cita', $data);
	}	

}
?>