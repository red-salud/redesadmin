<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta extends CI_Controller {
	public function __construct()
    {
        parent::__construct(); 
        $this->load->helper(array('fechas','otros','pdf','contable','config')); 
        $this->load->model(array('model_venta','model_categoria_cliente','model_cliente_persona','model_cliente_empresa','model_configuracion', 'model_serie')); 
        $this->load->library('excel');
    	$this->load->library('Fpdfext');
        //cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
    }
	public function lista_ventas_historial()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); // var_dump($allInputs); exit(); 
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_venta->m_cargar_ventas_historial($paramPaginate,$paramDatos); 
		$totalRows = $this->model_venta->m_count_ventas_historial($paramPaginate,$paramDatos); 
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$strCliente = NULL;
			if( $row['tipo_cliente'] == 'E' ){ 
				$strCliente = $row['razon_social_ce']; 
			}
			if( $row['tipo_cliente'] == 'P' ){ 
				$strCliente = $row['cliente_persona']; 
			}
			$strMoneda = NULL;
			if( $row['moneda'] == 'S' ){ 
				$strMoneda = 'SOLES'; 
			}
			if( $row['moneda'] == 'D' ){ 
				$strMoneda = 'DÓLARES'; 
			}
			array_push($arrListado, 
				array(
					'idmovimiento' => $row['idmovimiento'],
					'numero_serie' => $row['numero_serie'],
					'numero_correlativo' => $row['numero_correlativo'],
					'fecha_registro' => darFormatoDMY($row['fecha_registro']),
					'fecha_emision' => darFormatoDMY($row['fecha_emision']),
					'cliente' => trim($row['cliente_persona_empresa']),
					// 'colaborador' => strtoupper($row['colaborador']),
					'moneda' => $strMoneda,
					'idformapago' => $row['idformapago'],
					'forma_pago' => strtoupper($row['descripcion_fp']),
					'idconcepto' => $row['idconcepto'],
					'concepto' => strtoupper($row['descripcion_con']),
					'idempresaadmin' => $row['idempresaadmin'],
					'empresa_admin' => strtoupper($row['razon_social_ea']),
					'idusuario' => $row['idusuario'], 
					'subtotal' => $row['subtotal'], 
					'igv' => $row['igv'], 
					'total' => $row['total'] 
					//'estado' => $objEstado 
				)
			);
		}
		$arrData['datos'] = $arrListado; 
    	$arrData['paginate']['totalRows'] = $totalRows['contador']; 
    	$arrData['message'] = ''; 
    	$arrData['flag'] = 1; 
		if(empty($lista)){ 
			$arrData['flag'] = 0; 
		} 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function generar_numero_venta() 
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		if( empty($allInputs['sede']) ){ 
			$arrData['message'] = '';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// Codigo para uso interno. 
		// NOMENCLATURA: 
		// V + ABV_SEDE(2) + AÑO(2) + MES(2) + DIA(2) + "X" CARACTERES (DINAMICO)
		// Ejm: VUC170827001 
		$sede = strtoupper($allInputs['sede']['abreviatura']); 
		$numCaracteres = $fConfig['cant_caracteres_correlativo_venta']; 
		$numVenta = 'V'.$sede.date('y'); 
		if($fConfig['incluye_mes_en_codigo_venta'] == 'si'){
			$numVenta .= date('m'); 
		}
		if($fConfig['incluye_dia_en_codigo_venta'] == 'si'){
			$numVenta .= date('d'); 
		}
		// OBTENER ULTIMA VENTA SEGÚN LOGICA DE CONFIGURACIÓN. 
		$allInputs['config'] = $fConfig; 
		$fVenta = $this->model_venta->m_cargar_ultima_venta_segun_config($allInputs);
		if( empty($fVenta) ){
			$numCorrelativo = 1;
		}else{
			$numCorrelativo = substr($fVenta['num_facturacion'], ($numCaracteres * -1), $numCaracteres); 
			$numCorrelativo = (int)$numCorrelativo + 1;
		}
		$numVenta .= str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);
	 	$arrDatos['num_facturacion'] = $numVenta; 
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($numVenta)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function generar_serie_correlativo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$fConfig = obtener_parametros_configuracion();
		if( empty($allInputs['serie']) || empty($allInputs['tipo_documento_mov']) ){ 
			$arrData['message'] = '';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		// Codigo para contabilidad. 
		// NOMENCLATURA: 
		// NUMERO DE SERIE + CORRELATIVO 
		// Ejm: 002-000005 
		// $numCaracteres = 7; 
		$numCaracteres = $fConfig['cant_caracteres_correlativo_venta']; 
		
		// OBTENER POSICIÓN ACTUAL DEL CORRELATIVO . 
		// $allInputs['config'] = $fConfig; 
		$fSerie = $this->model_serie->m_cargar_posicion_correlativo($allInputs);
		if( empty($fSerie) ){ 
			$arrData['message'] = 'Falta configurar las series y correlativos';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		} 
		$numCorrelativo = (int)$fSerie['correlativo_actual'] + 1;
		$numSoloCorrelativo = $numCorrelativo;
		$numCorrelativo = str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT);


		$numSerie = $allInputs['serie']['descripcion']; 
		$arrDatos['num_solo_correlativo'] = $numSoloCorrelativo; 
	 	$arrDatos['num_correlativo'] = $numCorrelativo; 
	 	$arrDatos['num_serie'] = $numSerie; 
    	$arrData['datos'] = $arrDatos;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($numCorrelativo)){ 
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function registrar()
	{
		ini_set('xdebug.var_display_max_depth', 5);
	    ini_set('xdebug.var_display_max_children', 256);
	    ini_set('xdebug.var_display_max_data', 1024);
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$fConfig = obtener_parametros_configuracion();
    	// var_dump($allInputs); exit();
		/* VALIDACIONES */

		if( $allInputs['isRegisterSuccess'] === TRUE ){ 
    		$arrData['message'] = 'Ya se registró esta venta.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
		if( count($allInputs['detalle']) < 1){
    		$arrData['message'] = 'No se ha agregado ningún elemento';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	// if( empty($allInputs['sede']['id']) ){
    	// 	$arrData['message'] = 'Debe tener asignado una sede para poder registrar los datos';
    	// 	$arrData['flag'] = 0;
    	// 	$this->output
			  //   ->set_content_type('application/json')
			  //   ->set_output(json_encode($arrData));
		   //  return;
    	// }
    	if( $allInputs['total'] == 'NaN' || empty($allInputs['total']) ){
    		$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    		$arrData['flag'] = 0;
    		$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		    return;
    	}
    	// $errorEnBucle = 'no'; 
    	// foreach ($allInputs['detalle'] as $key => $row) {
    	// 	if( empty($row['precio_unitario']) ){
    	// 		$errorEnBucle = 'si';
    	// 		break;
    	// 	}
    	// }
    	// if( $errorEnBucle === 'si' ){ 
    	// 	$arrData['message'] = 'No se puedo calcular el precio total de venta. Corrija los montos e intente nuevamente.';
    	// 	$arrData['flag'] = 0;
    	// 	$this->output
			  //   ->set_content_type('application/json')
			  //   ->set_output(json_encode($arrData));
		   //  return;
    	// }
    	if( empty($allInputs['num_serie']) || empty($allInputs['num_correlativo']) ){ 
    		$arrData['message'] = 'No se ha generado un CORRELATIVO para esta venta.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	if( empty($allInputs['tipo_documento_mov']['id']) || empty($allInputs['serie']['id']) ){ 
    		$arrData['message'] = 'No se seleccionó un tipo de documento y/o serie.';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	}
    	if( $allInputs['tipo_documento_identidad']['destino_str'] == 'ce' ){ // si es cliente empresa 
    		if( empty($allInputs['contacto']['id']) ){ 
	    		$arrData['message'] = 'No se ha asociado un CONTACTO válido. Asocie el CONTACTO.';
	    		$arrData['flag'] = 0;
	    		$this->output
			    	->set_content_type('application/json')
			    	->set_output(json_encode($arrData));
			    return;
	    	}
    	}
    	/* validar que el correlativo sea CORRECTO  */ 
    	$numCaracteres = $fConfig['cant_caracteres_correlativo_venta']; 
    	$numeroDeSerieValido = FALSE; 
    	// $fNumeroSerie = $this->model_caja->m_cargar_caja_por_este_numero_serie($allInputs['idcajamaster'],$allInputs['idtipodocumento']);
    	$fActual = $this->model_serie->m_cargar_posicion_correlativo($allInputs); 
    	$numeroSeriePad = str_pad(($fActual['correlativo_actual'] + 1), $numCaracteres, '0', STR_PAD_LEFT); 
    	$serieCorrelativoNuevo = $fActual['numero_serie'].'-'.$numeroSeriePad; 
    	//var_dump($serieCorrelativoNuevo,$allInputs['serie_correlativo'],$allInputs['num_solo_correlativo'],$fActual['correlativo_actual'] + 1); exit();
    	if( $serieCorrelativoNuevo === $allInputs['serie_correlativo'] && (int)$allInputs['num_solo_correlativo'] === (int)($fActual['correlativo_actual'] + 1) ){ 
    		$numeroDeSerieValido = TRUE; 
    	}
    	if( !$numeroDeSerieValido ){ 
    		$arrData['message'] = 'El número de serie es erróneo, por favor refresque el formulario <span class="icon-bg"><i class="fa fa-reload"></i></span>';
    		$arrData['flag'] = 0;
    		$this->output
		    	->set_content_type('application/json')
		    	->set_output(json_encode($arrData));
		    return;
    	} 
    	$this->db->trans_start();
    	if( $allInputs['tipo_documento_identidad']['destino'] == 1 ){ // cliente empresa 
    		$allInputs['tipo_cliente'] = 'E'; // empresa 
    	} 
    	if( $allInputs['tipo_documento_identidad']['destino'] == 2 ){ // cliente persona 
    		$allInputs['tipo_cliente'] = 'P'; // persona 
    	} 
		if( $this->model_venta->m_registrar_venta($allInputs) ){ 
			$arrData['idmovimiento'] = GetLastId('idmovimiento','movimiento');
			foreach ($allInputs['detalle'] as $key => $elemento) { 
				$elemento['idmovimiento'] = $arrData['idmovimiento'];  
				if( $this->model_venta->m_registrar_detalle_venta($elemento) ){ 
					$arrData['message'] = 'Los datos se registraron correctamente.'; 
					$arrData['flag'] = 1; 
				} 
			} 
			// actualizar correlativo actual 
			$allInputs['nuevo_correlativo'] = $fActual['correlativo_actual'] + 1;
			if( $this->model_serie->m_actualizar_correlativo($allInputs) ){ 
				$arrData['message'] .= '<br/> Se actualizó el correlativo.'; 
				$arrData['flag'] = 1; 
			} 
		} 
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}