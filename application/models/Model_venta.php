<?php
class Model_venta extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}
	public function m_cargar_ventas_historial($paramPaginate, $paramDatos)
	{
		$this->db->select("CONCAT(COALESCE(cp.numero_documento_cli,''), ' ', COALESCE(ce.numero_documento_cli,'')) AS numero_documento",FALSE);
		$this->db->select("CONCAT(COALESCE(cp.nombres_cli,''), ' ', COALESCE(cp.ap_paterno_cli,''), ' ', COALESCE(cp.ap_materno_cli,''), ' ', COALESCE(ce.razon_social_cli,'')) AS cliente_persona_empresa",FALSE); // razon_social
		$this->db->select("CONCAT(cp.nombres_cli, ' ', cp.ap_paterno_cli, ' ', cp.ap_materno_cli) AS cliente_persona",FALSE);
		$this->db->select('mov.idmovimiento, mov.fecha_registro, mov.fecha_emision, mov.tipo_cliente, mov.moneda, 
			mov.modo_igv, mov.subtotal, mov.igv, mov.total, mov.estado_movimiento, mov.numero_serie, mov.numero_correlativo, 
			us.idusuario, us.username, 
			ea.idempresaadmin, ea.razon_social_ea, ea.nombre_comercial_ea, ea.ruc_ea, con.idconcepto, con.descripcion_con, 
			ce.idclienteempresa, ce.razon_social_cli, ce.nombre_comercial_cli, cp.idclientepersona, fp.idformapago, fp.descripcion_fp', FALSE); 
		$this->db->from('movimiento mov'); 
		//$this->db->join('colaborador col','mov.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','mov.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','mov.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join('concepto con','mov.idconcepto = con.idconcepto'); 
		$this->db->join("cliente_empresa ce","mov.idcliente = ce.idclienteempresa AND mov.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","mov.idcliente = cp.idclientepersona AND mov.tipo_cliente = 'P'",'left'); 
		$this->db->join('forma_pago fp','mov.idformapago = fp.idformapago'); 
		$this->db->where('mov.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));  
		$this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		$this->db->where_in('mov.estado_movimiento', array(1)); // activo 
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
	public function m_count_ventas_historial($paramPaginate, $paramDatos)
	{
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('movimiento mov'); 
		//$this->db->join('colaborador col','mov.idcolaborador = col.idcolaborador'); 
		$this->db->join('usuario us','mov.idusuarioregistro = us.idusuario'); 
		$this->db->join('empresa_admin ea','mov.idempresaadmin = ea.idempresaadmin'); 
		$this->db->join('concepto con','mov.idconcepto = con.idconcepto'); 
		$this->db->join("cliente_empresa ce","mov.idcliente = ce.idclienteempresa AND mov.tipo_cliente = 'E'",'left'); 
		$this->db->join("cliente_persona cp","mov.idcliente = cp.idclientepersona AND mov.tipo_cliente = 'P'",'left'); 
		$this->db->join('forma_pago fp','mov.idformapago = fp.idformapago'); 
		$this->db->where('mov.fecha_emision BETWEEN '. $this->db->escape( darFormatoYMD($paramDatos['desde']).' '.$paramDatos['desdeHora'].':'.$paramDatos['desdeMinuto']) .' AND ' 
			. $this->db->escape( darFormatoYMD($paramDatos['hasta']).' '.$paramDatos['hastaHora'].':'.$paramDatos['hastaMinuto']));  
		$this->db->where('ea.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		$this->db->where_in('mov.estado_movimiento', array(1)); // activo 
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
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_ultima_venta_segun_config($datos)
	{
		$this->db->select('mo.idmovimiento, mo.num_facturacion');
		$this->db->from('movimiento mo');
		// $this->db->where_in('mo.estado_movimiento'); 
		// $this->db->where('se.idsede',$datos['sede']['id']);
		if($datos['config']['incluye_mes_en_codigo_venta'] == 'no' && $datos['config']['incluye_dia_en_codigo_venta'] == 'no'){
			$this->db->where('YEAR(DATE(mo.fecha_registro))', (int)date('Y')); // año 
		}
		if($datos['config']['incluye_mes_en_codigo_venta'] == 'si' && $datos['config']['incluye_dia_en_codigo_venta'] == 'no'){
			$this->db->where('YEAR(DATE(mo.fecha_registro))', (int)date('Y')); // año 
			$this->db->where("DATE_FORMAT(DATE(mo.fecha_registro),'%m')",date('m')); // mes 
		}
		if($datos['config']['incluye_mes_en_codigo_venta'] == 'si' && $datos['config']['incluye_dia_en_codigo_venta'] == 'si'){
			$this->db->where('DATE(mo.fecha_registro)',date('Y-m-d')); // año, mes y dia
		}
		$this->db->where('mo.idempresaadmin', $this->sessionRS['idempresaadmin']); // empresa session 
		$this->db->order_by('mo.fecha_registro','DESC');
		$this->db->limit(1);
		return $this->db->get()->row_array();
	}
	public function m_cargar_esta_venta_por_codigo($datos)
	{
		
	}
	public function m_registrar_venta($datos)
	{ 
		$data = array( 
			'idconcepto'=> $datos['concepto']['id'],
			'tipo_cliente'=> $datos['tipo_cliente'],
			'idcliente'=> $datos['cliente']['id'],
			'idusuarioregistro'=> $this->sessionRS['idusuario'],
			'dir_movimiento'=> 'E',
			'tipo_movimiento'=> 2,
			'idtipodocumentomov'=> $datos['tipo_documento_mov']['id'],
			'idempresaadmin'=> $this->sessionRS['idempresaadmin'],
			'fecha_registro'=> date('Y-m-d H:i:s'),
			'fecha_emision'=> darFormatoYMD($datos['fecha_emision']),
			'numero_serie'=> $datos['num_serie'],
			'numero_correlativo'=> $datos['num_correlativo'],
			'idformapago'=> $datos['forma_pago']['id'],
			'moneda'=> $datos['moneda']['str_moneda'],
			'modo_igv'=> $datos['modo_igv'],
			//'total_inafecto'=> ,
			'subtotal'=> $datos['subtotal'],
			'igv'=> $datos['igv'],
			'total'=> $datos['total'] 
		);
		return $this->db->insert('movimiento', $data);
	}
	public function m_registrar_detalle_venta($datos)
	{
		$data = array( 
			'idmovimiento'=> $datos['idmovimiento'],
			'idelemento'=> $datos['id'],
			'periodo'=> empty($datos['periodo']) ? NULL : $datos['periodo'],
			'cantidad'=> $datos['cantidad'],
			'precio_unitario'=> $datos['precio_unitario'],
			'importe_con_igv'=> $datos['importe_con_igv'],
			'importe_sin_igv'=> $datos['importe_sin_igv'],
			'si_inafecto'=> $datos['excluye_igv'],
			'igv_detalle'=> $datos['igv'] 
		);
		return $this->db->insert('detalle_movimiento', $data);
	}
}
?>