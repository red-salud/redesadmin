<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificado extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_certificado','model_cobro','model_asegurado')); 

    } 
	public function listar_historial_certificados(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		//print_r($paramDatos); //exit();
		$lista = $this->model_certificado->m_cargar_certificados($paramPaginate,$paramDatos);
		$fCount = $this->model_certificado->m_count_certificados($paramPaginate,$paramDatos);
		//print_r($fCount);
		$arrListado = array(); 
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['cert_estado'] == 1 ){ // SIN CANCELAR  
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'SIN CANCELAR';
				$objEstado['valor'] = $row['cert_estado'];
			}
			if( $row['cert_estado'] == 3 ){ // CANCELADO 
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'CANCELADO';
				$objEstado['valor'] = $row['cert_estado'];
			}
			/* 
				1: ACTIVO 
				2: INACTIVO				
				3: PERIODO DE CARENCIA
				4: ACTIVO MANUAL
			*/ 
			$objEstadoAtencion = array(); 
			if( $row['cant_cobros'] >= 1 ){ // hay cobros 
				$fechaAuxFinCobertura = date_create($row['ultima_cobertura']);
				date_add($fechaAuxFinCobertura, date_interval_create_from_date_string(30 + $row['dias_mora'].' days')); 
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}else{ // no hay cobros 
				$fechaAuxIniVigencia = date_create($row['cert_iniVig']);
				$fechaAuxFinCobertura = date_add($fechaAuxIniVigencia, date_interval_create_from_date_string(30 + $row['dias_mora'].' days'));
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}
			// si hay una atención dentro de los 7 días pasará a INACTIVO 
			if( !empty($row['ultima_atencion']) ){ 
				$fechaUltimaAtencion = date_create($row['ultima_atencion']);
				$fechaUltimaAtencionMasXDias = date_add($fechaUltimaAtencion, date_interval_create_from_date_string($row['dias_atencion'].' days'));
				if( strtotime($fechaUltimaAtencionMasXDias) > strtotime(date('Y-m-d')) ){ // solo mayor 
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}
			$fechaAuxFinPeriodoCarencia = date_create($row['cert_iniVig']); 
			date_add($fechaAuxFinPeriodoCarencia, date_interval_create_from_date_string($row['dias_carencia'] .' days')); 
			$fechaFinPeriodoCarencia = date_format($fechaAuxFinPeriodoCarencia, 'Y-m-d'); 
			if( strtotime($fechaFinPeriodoCarencia) > strtotime(date('Y-m-d')) ){ 
				$objEstadoAtencion['descripcion'] = 'PER. DE CARENCIA'; 
				$objEstadoAtencion['valor'] = 3;
			}
			if( $row['cert_upProv'] == 1 ){ 
				$objEstadoAtencion['descripcion'] = 'ACTIVO MANUAL'; 
				$objEstadoAtencion['valor'] = 4;
			}
			
			array_push($arrListado, 
				array( 
					'idcertificado' => trim($row['cert_id']),
					'num_certificado' => $row['cert_num'], 
					'canal_cliente'=> strtoupper($row['nombre_comercial_cli']),
					'contratante' => strtoupper($row['contratante']),
					'numero_doc_cont' => $row['cont_numDoc'],
					'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
					'fecha_fin_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_finVig'])),
					'numero_propuesta' => $row['cert_numpropuesta'],
					'idplan' => $row['idplan'],
					'plan' => $row['nombre_plan'],
					'dias_carencia'=> $row['dias_carencia'],
					'dias_mora'=> $row['dias_mora'],
					'estado_atencion' => $objEstadoAtencion,
					'estado' => $objEstado 
				)
			);
		}
		//print_r('hola'); 
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $fCount['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 
	public function listar_historial_certificados_detalle()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_certificado->m_cargar_certificados_detalle($paramPaginate,$paramDatos);
		$fCount = $this->model_certificado->m_count_certificados_detalle($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['cert_estado'] == 1 ){ // ACTIVO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'SIN CANCELAR';
				$objEstado['valor'] = $row['cert_estado'];
			}
			if( $row['cert_estado'] == 3 ){ // CANCELADO    
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'CANCELADO';
				$objEstado['valor'] = $row['cert_estado'];
			} 
			/* 
				1: ACTIVO 
				2: INACTIVO 
				3: PERIODO DE CARENCIA 
				4: ACTIVO MANUAL 
			*/ 
			$objEstadoAtencion = array(); 
			if( $row['cant_cobros'] >= 1 ){ // hay cobros 
				$fechaAuxFinCobertura = date_create($row['ultima_cobertura']);
				date_add($fechaAuxFinCobertura, date_interval_create_from_date_string(30 + $row['dias_mora'].' days')); 
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}else{ // no hay cobros 
				$fechaAuxIniVigencia = date_create($row['cert_iniVig']);
				$fechaAuxFinCobertura = date_add($fechaAuxIniVigencia, date_interval_create_from_date_string(30 + $row['dias_mora'].' days'));
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}
			$fechaAuxFinPeriodoCarencia = date_create($row['cert_iniVig']); 
			date_add($fechaAuxFinPeriodoCarencia, date_interval_create_from_date_string($row['dias_carencia'] .' days')); 
			$fechaFinPeriodoCarencia = date_format($fechaAuxFinPeriodoCarencia, 'Y-m-d'); 
			if( strtotime($fechaFinPeriodoCarencia) > strtotime(date('Y-m-d')) ){ 
				$objEstadoAtencion['descripcion'] = 'PER. DE CARENCIA'; 
				$objEstadoAtencion['valor'] = 3;
			}
			if( $row['cert_upProv'] == 1 ){ 
				$objEstadoAtencion['descripcion'] = 'ACTIVO MANUAL'; 
				$objEstadoAtencion['valor'] = 4;
			}
			array_push($arrListado,
				array( 
					'idcertificadoasegurado' => trim($row['certase_id']), 
					'idcertificado' => trim($row['cert_id']), 
					'num_certificado' => $row['cert_num'], 
					'canal_cliente'=> strtoupper($row['nombre_comercial_cli']),
					'consecutivo'=> (int)$row['certase_conse'],
					'contratante' => strtoupper($row['contratante']),
					'numero_doc_cont' => $row['cont_numDoc'],
					'asegurado' => strtoupper($row['asegurado']),
					'numero_doc_aseg' => $row['aseg_numDoc'],
					'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
					'fecha_fin_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_finVig'])),
					'numero_propuesta' => $row['cert_numpropuesta'],
					'idplan' => $row['idplan'],
					'plan' => $row['nombre_plan'], 
					'dias_carencia'=> $row['dias_carencia'],
					'dias_mora'=> $row['dias_mora'],
					'estado_atencion' => $objEstadoAtencion,
					'estado' => $objEstado 
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $fCount['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function listar_certificados_de_asegurados()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		if(empty($allInputs) || empty($allInputs['numero_documento'])){ 
			$arrData['message'] = 'No se envió ningun parámetro de búsqueda.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
		}
		$lista = $this->model_certificado->m_cargar_certificados_de_asegurados($allInputs);
		$arrListado = array(); 
		$i = 0;
		foreach ($lista as $row) { 
			// ESTADO DE CERTIFICADO 
			$objEstado = array();
			if( $row['cert_estado'] == 1 ){ // ACTIVO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'success';
				$objEstado['labelText'] = 'SIN CANCELAR'; 
				$objEstado['valor'] = $row['cert_estado']; 
			} 
			if( $row['cert_estado'] == 3 ){ // CANCELADO    
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'danger';
				$objEstado['labelText'] = 'CANCELADO';
				$objEstado['valor'] = $row['cert_estado']; 
			} 
			// ESTADO DE ATENCIÓN 
			/* 
				1: ACTIVO 
				2: INACTIVO - 				
				3: PERIODO DE CARENCIA
				4: ACTIVO MANUAL
			*/ 
			$objEstadoAtencion = array(); 
			if( $row['cant_cobros'] >= 1 ){ // hay cobros 
				$fechaAuxFinCobertura = date_create($row['ultima_cobertura']);
				date_add($fechaAuxFinCobertura, date_interval_create_from_date_string(30 + $row['dias_mora'].' days')); 
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2; 
				}
			}else{ // no hay cobros 
				$fechaAuxIniVigencia = date_create($row['cert_iniVig']);
				$fechaAuxFinCobertura = date_add($fechaAuxIniVigencia, date_interval_create_from_date_string(30 + $row['dias_mora'].' days'));
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1; 
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2; 
				}
			}
			// si hay una atención dentro de los 7 días pasará a INACTIVO 
			if( !empty($row['ultima_atencion']) ){ 
				$fechaUltimaAtencion = date_create($row['ultima_atencion']);
				// var_dump($row['dias_atencion']); exit();
				$fechaUltimaAtencionMasXDias = date_add($fechaUltimaAtencion, date_interval_create_from_date_string($row['dias_atencion'].' days'));
				$fechaUltimaAtencionMasXDiasDate = date_format($fechaUltimaAtencionMasXDias, 'Y-m-d'); 
				if( strtotime($fechaUltimaAtencionMasXDiasDate) > strtotime(date('Y-m-d')) ){ // solo mayor 
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}
			$fechaAuxFinPeriodoCarencia = date_create($row['cert_iniVig']); 
			date_add($fechaAuxFinPeriodoCarencia, date_interval_create_from_date_string($row['dias_carencia'] .' days')); 
			$fechaFinPeriodoCarencia = date_format($fechaAuxFinPeriodoCarencia, 'Y-m-d'); 
			if( strtotime($fechaFinPeriodoCarencia) > strtotime(date('Y-m-d')) ){ 
				$objEstadoAtencion['descripcion'] = 'PER. DE CARENCIA'; 
				$objEstadoAtencion['valor'] = 3; 
			}
			if( $row['cert_upProv'] == 1 ){ 
				$objEstadoAtencion['descripcion'] = 'ACTIVO MANUAL'; 
				$objEstadoAtencion['valor'] = 4; 
			} 
			// otros 
			if( empty($row['ultima_atencion']) ){ 
				$row['ultima_atencion'] = '-';
			}
			if( empty($row['lugar_ultima_atencion']) ){ 
				$row['lugar_ultima_atencion'] = '-'; 
			}
			if( empty($row['aseg_sexo']) ){ 
				$row['aseg_sexo'] = '-'; 
			}
			if( empty($row['aseg_email']) ){ 
				$row['aseg_email'] = '-'; 
			}
			if( empty($row['edad']) ){ 
				$row['edad'] = '-'; 
			}
			array_push($arrListado,
				array(
					'idcertificadoasegurado' => trim($row['certase_id']),
					'idasegurado' => trim($row['aseg_id']),
					'descripcion' => strtoupper($row['asegurado']).' - '.$row['nombre_plan'],
					'num_certificado'=> $row['cert_num'],
					'fecha_inicio_vig' => formatoFechaReporte3($row['cert_iniVig']),
					'fecha_fin_vig' => formatoFechaReporte3($row['cert_finVig']),
					'asegurado' => strtoupper($row['asegurado']),
					'numero_doc_aseg' => $row['aseg_numDoc'],
					'idplan' => $row['idplan'],
					'nombre_plan' => strtoupper($row['nombre_plan']),
					'prima_monto' => number_format($row['prima_monto'],2),
					'fecha_nacimiento'=> formatoFechaReporte3($row['aseg_fechNac']),
					'fecha_nacimiento_edit'=> darFormatoDMY($row['aseg_fechNac']),
					'edad_actual' => $row['edad'],
					'direccion'=> strtoupper($row['aseg_direcc']),
					'telefono' => $row['aseg_telf'],
					'correo_electronico' => strtoupper($row['aseg_email']),
					'sexo' => strtoupper($row['aseg_sexo']),
					'ultima_atencion'=> formatoFechaReporte3($row['ultima_atencion']), 
					'lugar_ultima_atencion'=> $row['lugar_ultima_atencion'], 
					'estado_atencion' => $objEstadoAtencion, 
					'estado_certificado' => $objEstado 
				)
			);
			$i++;
		} 
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1; // 1:normal; 2:varios registros; 0:vacio 
    	if( $i > 1 ){
    		$arrData['flag'] = 2;
    	}
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_asegurados_de_certificado()
	{
		$this->load->view('certificado/popup_asegurados_de_certificado');
	}
	public function ver_popup_cobros_de_certificado()
	{
		$this->load->view('certificado/popup_cobros_de_certificado');
	}
	public function ver_popup_eleccion_certificado()
	{
		$this->load->view('certificado/popup_eleccion_certificado');
	}
	public function listar_asegurados_de_certificado()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_certificado->m_cargar_asegurados_de_certificado($allInputs['datos']);
		$arrListado = array();
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['cert_estado'] == 1 ){ // ACTIVO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'label-info';
				$objEstado['labelText'] = 'SIN CANCELAR';
			}
			if( $row['cert_estado'] == 3 ){ // CANCELADO    
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'CANCELADO';
			}
			array_push($arrListado,
				array( 
					'idcertificadoasegurado' => trim($row['certase_id']),
					'canal_cliente'=> strtoupper($row['nombre_comercial_cli']),
					'consecutivo'=> (int)$row['certase_conse'],
					'asegurado' => strtoupper($row['asegurado']),
					'numero_doc_aseg' => $row['aseg_numDoc'],
					'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
					'fecha_fin_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_finVig'])),
					'idplan' => $row['idplan'],
					'plan' => $row['nombre_plan'],
					'estado' => $objEstado  
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function listar_cobros_de_certificado()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_certificado->m_cargar_cobros_de_certificado($paramPaginate,$paramDatos);
		$fCount = $this->model_certificado->m_count_cobros_de_certificado($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			/* LOGICA DE IMPORTE SEGÚN CANAL */
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
			// FRECUENCIA DE PAGO 
			$rowFrecCobro = NULL; 
			if( $row['cobDet_frec'] == 1 ){
				$rowFrecCobro = 'MENSUAL';
			}
			if( $row['cobDet_frec'] == 3 ){
				$rowFrecCobro = 'TRIMESTRAL';
			}
			if( $row['cobDet_frec'] == 6 ){
				$rowFrecCobro = 'SEMESTRAL';
			}
			if( $row['cobDet_frec'] == 12 ){
				$rowFrecCobro = 'ANUAL';
			}
			array_push($arrListado,
				array(
					'idcobro' => trim($row['cob_id']),
					'fecha_cobro' => formatoFechaReporte3(darFormatoYMD($row['cob_fechCob'])),
					'vez_cobro' => (int)$row['cob_vezCob'],
					'importe' => $rowImporte,
					'medio_pago' => $row['descripcion_mp'],
					'frecuencia_cobro'=> $rowFrecCobro,
					'fecha_inicio_cobert' => formatoFechaReporte3(darFormatoYMD($row['cob_iniCobertura'])),
					'fecha_fin_cobert' => formatoFechaReporte3(darFormatoYMD($row['cob_finCobertura'])),
					'plan' => $row['nombre_plan'] 
				)
			);
		}
    	$arrData['datos'] = $arrListado;
    	$arrData['paginate']['totalRows'] = $fCount['contador'];
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function buscar_fichas_certificados()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrListado = array();
		if( empty($allInputs['cuadro_busqueda']) ){ 
			$arrData['message'] = 'Digite un valor válido en el cuadro de texto.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
		}
		$allInputs['cuadro_busqueda'] = trim($allInputs['cuadro_busqueda']);
		// VALIDAR SI ES ASEGURADO O CONTRATANTE O AMBOS 
		$lista = $this->model_certificado->m_buscar_certificados_y_cobros_por_asegurado_contratante($allInputs); 
		if( empty($lista) ){ 
			// BUSCAR SI ES ASEGURADO 
			$arrParams = array(
				'doc_asegurado'=> $allInputs['cuadro_busqueda'] 
			);
			$listaCont = $this->model_asegurado->m_buscar_contratante_desde_asegurado($arrParams); 
			if( empty($listaCont) ){
				$arrData['message'] = 'Los datos para éste registro son inconsistentes.';
				$arrData['flag'] = 0; 
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
			}
			$arrNumDocConts = array(); 
			foreach ($listaCont as $key => $row) {
				$arrNumDocConts[] = trim($row['cont_numDoc']); 
			}
			// BUSCAR CONTRATANTES DE ASEGURADO. 
			$lista = $this->model_certificado->m_buscar_certificados_y_cobros_por_asegurado_contratante(NULL,$arrNumDocConts); 
		}
		foreach ($lista as $key => $row) { 
			// ESTADO DE CERTIFICADO 
			$objEstado = array();
			if( $row['cert_estado'] == 1 ){ // ACTIVO 
				$objEstado['claseIcon'] = 'fa-check';
				$objEstado['claseLabel'] = 'success';
				$objEstado['labelText'] = 'SIN CANCELAR'; 
				$objEstado['valor'] = $row['cert_estado']; 
			} 
			if( $row['cert_estado'] == 3 ){ // CANCELADO    
				$objEstado['claseIcon'] = 'fa-ban';
				$objEstado['claseLabel'] = 'danger';
				$objEstado['labelText'] = 'CANCELADO';
				$objEstado['valor'] = $row['cert_estado']; 
			} 
			// ESTADO DE LA ATENCIÓN 
			/* 
				1: ACTIVO 
				2: INACTIVO - 				
				3: PERIODO DE CARENCIA
				4: ACTIVO MANUAL
			*/ 
			$objEstadoAtencion = array(); 
			if( $row['cant_cobros'] >= 1 ){ // hay cobros 
				$fechaAuxFinCobertura = date_create($row['ultima_cobertura']);
				date_add($fechaAuxFinCobertura, date_interval_create_from_date_string(30 + $row['dias_mora'].' days')); 
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1;
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}else{ // no hay cobros 
				$fechaAuxIniVigencia = date_create($row['cert_iniVig']);
				$fechaAuxFinCobertura = date_add($fechaAuxIniVigencia, date_interval_create_from_date_string(30 + $row['dias_mora'].' days'));
				$fechaFinCobertura = date_format($fechaAuxFinCobertura, 'Y-m-d'); 
				if( strtotime($fechaFinCobertura) > strtotime(date('Y-m-d')) ){ 
					$objEstadoAtencion['descripcion'] = 'ACTIVO'; 
					$objEstadoAtencion['valor'] = 1; 
				}else{
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2; 
				}
			}
			// si hay una atención dentro de los 7 días pasará a INACTIVO 
			if( !empty($row['ultima_atencion']) ){ 
				$fechaUltimaAtencion = date_create($row['ultima_atencion']);
				// var_dump($row['dias_atencion']); exit();
				$fechaUltimaAtencionMasXDias = date_add($fechaUltimaAtencion, date_interval_create_from_date_string($row['dias_atencion'].' days'));
				$fechaUltimaAtencionMasXDiasDate = date_format($fechaUltimaAtencionMasXDias, 'Y-m-d'); 
				if( strtotime($fechaUltimaAtencionMasXDiasDate) > strtotime(date('Y-m-d')) ){ // solo mayor 
					$objEstadoAtencion['descripcion'] = 'INACTIVO'; 
					$objEstadoAtencion['valor'] = 2;
				}
			}
			$fechaAuxFinPeriodoCarencia = date_create($row['cert_iniVig']); 
			date_add($fechaAuxFinPeriodoCarencia, date_interval_create_from_date_string($row['dias_carencia'] .' days')); 
			$fechaFinPeriodoCarencia = date_format($fechaAuxFinPeriodoCarencia, 'Y-m-d'); 
			if( strtotime($fechaFinPeriodoCarencia) > strtotime(date('Y-m-d')) ){ 
				$objEstadoAtencion['descripcion'] = 'PER. DE CARENCIA'; 
				$objEstadoAtencion['valor'] = 3; 
			}
			if( $row['cert_upProv'] == 1 ){ 
				$objEstadoAtencion['descripcion'] = 'ACTIVO MANUAL'; 
				$objEstadoAtencion['valor'] = 4; 
			} 

			$boolClassSelected = FALSE;
			if( trim($row['cont_numDoc']) == $allInputs['cuadro_busqueda'] ){ 
				$boolClassSelected = TRUE;
			}
			$arrAux = array( 
				'idcertificado'=> $row['cert_id'],
				'num_certificado'=> $row['cert_num'],
				'numero_doc_cont'=> trim($row['cont_numDoc']),
				'contratante'=> strtoupper($row['contratante']),
				'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
				'fecha_fin_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_finVig'])),
				'fecha_cancelacion'=> formatoFechaReporte3(darFormatoYMD($row['can_finVig'])),
				'numero_propuesta' => $row['cert_numpropuesta'],
				'idplan' => $row['idplan'],
				'plan' => $row['nombre_plan'], 
				'dias_carencia'=> $row['dias_carencia'],
				'dias_mora'=> $row['dias_mora'],
				'canal_cliente'=> strtoupper($row['nombre_comercial_cli']), 
				'estado'=> $objEstado,
				'estado_atencion' => $objEstadoAtencion,
				'open_certificado'=> FALSE,
				'classSelected' => $boolClassSelected, 
				'asegurados'=> array(),
				'cobros'=> array(),
				'atenciones'=> array()  
			);
			$arrListado[$row['cert_id']] = $arrAux; 
		} 
		// asegurados 
		foreach ($lista as $key => $row) { 
			$row['aseg_numDoc'] = trim($row['aseg_numDoc']); 
			$boolClassSelected = FALSE;
			if( $row['aseg_numDoc'] == $allInputs['cuadro_busqueda'] ){ 
				$boolClassSelected = TRUE;
			}
			$arrAux = array(
				'idcertificadoasegurado' => trim($row['certase_id']),
				'consecutivo'=> (int)$row['certase_conse'],
				'asegurado' => strtoupper($row['asegurado']),
				'numero_doc_aseg' => $row['aseg_numDoc'],
				'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
				'fecha_fin_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_finVig'])),
				'classSelected' => $boolClassSelected  
			); 
			if( !empty($row['certase_id']) ){ 
				$arrListado[trim($row['cert_id'])]['asegurados'][trim($row['certase_id'])] = $arrAux; 
			} 
		} 
		// cobros 
		foreach ($lista as $key => $row) { 
			/* LOGICA DE IMPORTE SEGÚN CANAL */
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
			// FRECUENCIA DE PAGO 
			$rowFrecCobro = NULL; 
			if( $row['cobDet_frec'] == 1 ){
				$rowFrecCobro = 'MENSUAL';
			}
			if( $row['cobDet_frec'] == 3 ){
				$rowFrecCobro = 'TRIMESTRAL';
			}
			if( $row['cobDet_frec'] == 6 ){
				$rowFrecCobro = 'SEMESTRAL';
			}
			if( $row['cobDet_frec'] == 12 ){
				$rowFrecCobro = 'ANUAL';
			}
			$arrAux = array(
				'idcobro' => trim($row['cob_id']),
				'fecha_cobro' => formatoFechaReporte3(darFormatoYMD($row['cob_fechCob'])),
				'vez_cobro' => (int)$row['cob_vezCob'],
				'importe' => $rowImporte,
				'medio_pago' => $row['descripcion_mp'],
				'frecuencia_cobro'=> $rowFrecCobro,
				'fecha_inicio_cobert' => formatoFechaReporte3(darFormatoYMD($row['cob_iniCobertura'])),
				'fecha_fin_cobert' => formatoFechaReporte3(darFormatoYMD($row['cob_finCobertura']))
			); 
			if( !empty($row['cob_id']) ){
				$arrListado[trim($row['cert_id'])]['cobros'][trim($row['cob_id'])] = $arrAux; 
			}
		}
		// atenciones 
		foreach ($lista as $key => $row) {
			$arrAux = array(
				'idsiniestro' => trim($row['idsiniestro']),
				'fecha_atencion' => formatoFechaReporte3(darFormatoYMD($row['fecha_atencion'])),
				'numero_doc_aseg' => $row['aseg_numDoc'],
				'especialidad' => $row['nombre_esp'],
				'lugar_atencion' => $row['nombre_comercial_pr'] 
			); 
			if( !empty($row['idsiniestro']) ){
				$arrListado[trim($row['cert_id'])]['atenciones'][trim($row['idsiniestro'])] = $arrAux; 
			}
		}
		// open me 
		$i = 1;
		foreach ($arrListado as $key => $row) { 
			if($i === 1){
				$arrListado[$key]['open_certificado'] = TRUE;
			}
			$i++; 
		}
		// REINDEXADO 
		$arrListado = array_values($arrListado); 
		foreach ($arrListado as $key => $row) { 
			$arrListado[$key]['cobros'] = array_values($arrListado[$key]['cobros']); 
			$arrListado[$key]['atenciones'] = array_values($arrListado[$key]['atenciones']); 
		}
		function fnOrderingVezCobro($a, $b) { 
	    	return $b['vez_cobro'] - $a['vez_cobro']; 
	    }
		// REORDENADO 
		foreach ($arrListado as $key => $row) { 
			usort($arrListado[$key]['cobros'], 'fnOrderingVezCobro'); 
		}

		$arrData['datos'] = $arrListado;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function activar_certificado_manual()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo activar el certificado';
    	$arrData['flag'] = 0;
		if( $this->model_certificado->m_activar_certificado_manual($allInputs) ){ 
			$arrData['message'] = 'Se activó el certificado correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function deshacer_activar_certificado_manual()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo deshacer la activación del certificado';
    	$arrData['flag'] = 0;
		if( $this->model_certificado->m_deshacer_activar_certificado_manual($allInputs) ){ 
			$arrData['message'] = 'Se deshizo la activación del certificado correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}