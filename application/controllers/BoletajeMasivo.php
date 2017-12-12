<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BoletajeMasivo extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7)); 
        $this->load->helper(array('fechas','otros','config')); 
        $this->load->model(array('model_cobro','model_cliente_persona','model_serie','model_venta','model_configuracion')); 
    }
	public function ver_popup_procesar_boletaje()
	{
		$this->load->view('boletaje-masivo/procesamiento_masivo'); 
	}
	public function procesar_cobros_boleta_masivo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$paramFilters = $allInputs['filters']; 
		$paramDatos = $allInputs['datos']; 
		// print_r($allInputs); exit(); 

		$arrData['message'] = 'Error al procesar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES 
    	// campo comprobante vacio
    	if( empty($allInputs['datos']['tipo_documento_mov']) || empty($allInputs['datos']['tipo_documento_mov']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el Comprobante. Corrija y vuelva a intentarlo'; 
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	} 
    	// campo serie vacio 
    	if( empty($allInputs['datos']['serie']) || empty($allInputs['datos']['serie']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el número de serie. Corrija y vuelva a intentarlo'; 
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	} 
    	// campo concepto vacio 
    	if( empty($allInputs['datos']['concepto']) || empty($allInputs['datos']['concepto']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el concepto/glosa. Corrija y vuelva a intentarlo'; 
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	} 
    	// campo moneda vacio 
    	// if( empty($allInputs['datos']['moneda']) ){
    		// $arrData['message'] = 'No se ingresó correctamente el moneda. Corrija y vuelva a intentarlo'; 
			// $arrData['flag'] = 0;
			// $this->output
			    // ->set_content_type('application/json') facturado
			    // ->set_output(json_encode($arrData));
			// return;
    	// } 
    	$arrLog = array(); 
    	$arrLog['message'] = NULL; 
		$lista = $this->model_cobro->m_cargar_cobros(FALSE,$paramFilters); 
		if( empty($lista) ){ 
    		$arrData['message'] = 'No hay cobros generados en el rango de fechas seleccionado. Vuelva a seleccionar un rango de fechas.'; 
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	} 
		$countCobrosProcesados = 0;
		$countCobrosBoleteados = 0;
		$countCobrosNoBoleteados = 0; 
		$countError = 0;
		$countOK = 0;
		$this->db->trans_start();
		$fConfig = obtener_parametros_configuracion();
		
		foreach ($lista as $key => $row) { 
			// asignacion de variables para cliente persona  
			$arrTempCliente = array(
				'categoria_cliente' => array( 
					'id' => 3, 
					'descripcion' => 'INDEPENDIENTE' 
				),
				'num_documento' => trim($row['cont_numDoc']),
				'nombres' => trim($row['cont_nom1']).' '.trim($row['cont_nom2']),
				'ap_paterno' => $row['cont_ape1'],
				'ap_materno' => $row['cont_ape2'],
				'sexo' => array(
					'id' => 'I' // INDEFINIDO - no envian el campo sexo en los datos del contratante en las tramas. 
				),
				'telefono_movil' => $row['cont_telf'] 
			);
			// castear importe a número. 
			/* LOGICA DE IMPORTE */
			$rowImporte = $row['cob_importe']; 
			$countImporte = strlen($row['cob_importe']); 
			// var_dump($countImporte,$row['cob_importe']); exit(); 
			if( $countImporte == 4 ){ 
				$part1 = substr($rowImporte, 0, 2); 
				$part2 = substr($rowImporte, -2, 2); 
				$rowImporte = (float)$part1.'.'.$part2; 
			}
			if( $countImporte == 5 ){ 
				$part1 = substr($rowImporte, 0, 3); 
				$part2 = substr($rowImporte, -2, 2); 
				$rowImporte = (float)$part1.'.'.$part2; 
			} 
			// buscar si el cliente no está registrado 
			$fCliente = $this->model_cliente_persona->m_validar_cliente_persona_num_documento($arrTempCliente['num_documento']); 
			if( empty($fCliente) ){ 
				// registro de cliente 
				if($this->model_cliente_persona->m_registrar($arrTempCliente)){ 
					$tempIdClientePersona = GetLastId('idclientepersona','cliente_persona'); 
					$arrLog['message'] .= 'Cliente registrado correctamente. <br />'; 
				}
			}else{ 
				$arrLog['message'] .= 'Cliente previamente registrado. <br />'; 
				$tempIdClientePersona = $fCliente[0]['idclientepersona']; 
			} 
			// generar correlativo 
			$arrCorrelativo = array();
			$numCorrelativo = NULL; 
			$numCaracteres = $fConfig['cant_caracteres_correlativo_venta'];
			$arrPosicion = array(
				'tipo_documento_mov'=> $paramDatos['tipo_documento_mov'], 
				'serie'=> $paramDatos['serie'] 
			);
			$fSerie = $this->model_serie->m_cargar_posicion_correlativo($arrPosicion);
			$numCorrelativo = (int)$fSerie['correlativo_actual'] + 1;
			$numSoloCorrelativo = $numCorrelativo;
			$numCorrelativo = str_pad($numCorrelativo, $numCaracteres, '0', STR_PAD_LEFT); 

			// $numSerie = $allInputs['serie']['descripcion']; 
			$arrCorrelativo['num_solo_correlativo'] = $numSoloCorrelativo; 
		 	$arrCorrelativo['num_correlativo'] = $numCorrelativo; 
		 	$arrCorrelativo['num_serie'] = $paramDatos['serie']['descripcion']; 

			// asignacion de variables para venta 
			$strMoneda = NULL;
			if( $row['cob_moneda'] == 'PEN' ){
				$strMoneda = 'S'; // soles 
			}
			if( $row['cob_moneda'] == 'USD' ){
				$strMoneda = 'D'; // soles 
			}
			// calculo IGV 
			$arrImportes = array( 
				'importe_con_igv' => round($rowImporte,2),
				'importe_sin_igv' => round(($rowImporte / 1.18), 2),
				'igv' => round(0.18 * ($rowImporte / 1.18) , 2)
			);
			$arrTempVenta = array( 
				'concepto' => $paramDatos['concepto'],
				'tipo_cliente' => 'P',// persona 
				'cliente' => array(
					'id' => $tempIdClientePersona 
				), 
				'tipo_documento_mov' => $paramDatos['tipo_documento_mov'],
				'fecha_emision' => darFormatoDMY($row['cob_fechCob']), // fecha de emision = fecha de cobro 
				'num_serie' => $arrCorrelativo['num_serie'],
				'num_correlativo' => $arrCorrelativo['num_correlativo'], 
				'forma_pago' => array( 
					'id' => 1 // al contado 
				),
				'moneda' => array( 
					'str_moneda'=> $strMoneda 
				), 
				'modo_igv' => 1, // incluye igv 
				'subtotal' => $arrImportes['importe_sin_igv'],
				'igv' => $arrImportes['igv'],
				'total' => $arrImportes['importe_con_igv'] 
			);
			$todoOK = 'si';
			// validaciones en el log 
			if( empty($arrImportes['importe_con_igv']) || !is_numeric($arrImportes['importe_con_igv']) ){ 
	    		$arrLog['message'] .= '- Monto total inválido. <br />'; 
	    		$todoOK = 'no';
	    	}
	    	if( empty($arrImportes['igv']) || !is_numeric($arrImportes['igv']) ){ 
	    		$arrLog['message'] .= '- IGV inválido. <br />'; 
	    		$todoOK = 'no';
	    	}
	    	if( empty($arrImportes['importe_sin_igv']) || !is_numeric($arrImportes['importe_sin_igv']) ){ 
	    		$arrLog['message'] .= '- Sub Total inválido. <br />'; 
	    		$todoOK = 'no';
	    	} 
	    	// si no hay un enlace entre elemento y plan, entonces no se pruede procesar 
	    	if( empty($row['idelemento']) ){ 
	    		$arrLog['message'] .= '- No hay enlace entre el elemento de la facturación y el plan. <br />'; 
	    		$todoOK = 'no';
	    	} 
	    	$arrLog['facturado'] = 2;
	    	$arrLog['error'] = NULL;
	    	$arrLog['idmovimiento'] = NULL; 
	    	if( $todoOK == 'si' ){ 
				// registramos cabecera 
		    	if( $this->model_venta->m_registrar_venta($arrTempVenta) ){ 
		    		$tempIdMovimiento = GetLastId('idmovimiento','movimiento'); 
		    		$arrLog['idmovimiento'] = $tempIdMovimiento; 
					$arrLog['message'] .= '- Cabecera de venta registrada correctamente. <br />'; 
					// detalle 
					$arrTempDetalle = array(
						'idmovimiento' => $tempIdMovimiento,
						'id' => $row['idelemento'], // idelemento 
						'periodo' => NULL, 
						'cantidad' => 1, 
						'precio_unitario' => $arrImportes['importe_con_igv'], 
						'importe_con_igv' => $arrImportes['importe_con_igv'], 
						'importe_sin_igv' => $arrImportes['importe_sin_igv'], 
						'excluye_igv' => 2, // no 
						'igv' => $arrImportes['igv'] 
					);
					// registramos detalle 
					if( $this->model_venta->m_registrar_detalle_venta($arrTempDetalle) ){ 
						$arrLog['message'] .= '- Detalle de venta registrada correctamente. <br />'; 
					} 
					// actualizar correlativo actual. 
					$arrCorrelativo['nuevo_correlativo'] = $arrCorrelativo['num_solo_correlativo']/* + 1*/; 
					$arrCorrelativo['tipo_documento_mov'] = $paramDatos['tipo_documento_mov']; 
					$arrCorrelativo['serie'] = $paramDatos['serie']; 
					if( $this->model_serie->m_actualizar_correlativo($arrCorrelativo) ){ 
						$arrLog['message'] .= '<br/> Se actualizó el correlativo.'; 
					} 
					$arrLog['facturado'] = 1; 
					$countOK++; 
		    	}
	    	}else{ 
	    		$countError++; 
	    		$arrLog['error'] = 'ERROR'; 
	    	} 
	    	// actualizamos cobros con log de seguimiento
	    	$arrLog['idcobro'] = $row['cob_id']; 
	    	$this->model_cobro->m_actualizar_cobros_log_seguimiento($arrLog); 
	    	$arrLog['message'] = NULL; 
		}
		$countAll = $countOK + $countError; 
		$arrDatosSalida = array( 
			'cant_cobros'=> $countAll, 
			'cant_cobros_procesados'=> $countOK, 
			'cant_cobros_no_procesados'=> $countError 
		);
		$arrData['datos'] = $arrDatosSalida;
		$arrData['message'] = 'Se encontraron <b>'.$countAll.'</b> cobros de las cuales se procesaron correctamente <b>'.$countOK.'</b>.'; 
		$arrData['flag'] = 1; 
		$this->db->trans_complete(); 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}