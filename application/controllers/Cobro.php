<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
class Cobro extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_cobro')); 

    } 
	public function listar_historial_cobros(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_cobro->m_cargar_cobros($paramPaginate,$paramDatos);
		$fCount = $this->model_cobro->m_count_cobros($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			$objFacturado = array();
			if( $row['facturado'] == 1 ){ // SIN CANCELAR  
				$objFacturado['claseIcon'] = 'fa-check';
				$objFacturado['claseLabel'] = 'label-info';
				$objFacturado['labelText'] = 'FACTURADO';
				$objFacturado['valor'] = $row['facturado'];
			}
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
			array_push($arrListado,
				array(
					'idcobro' => trim($row['cob_id']),
					'contratante' => strtoupper($row['contratante']),
					'canal_cliente'=> strtoupper($row['nombre_comercial_cli']),
					'numero_doc_cont' => $row['cont_numDoc'],
					'fecha_inicio_vig' => formatoFechaReporte3(darFormatoYMD($row['cert_iniVig'])),
					'fecha_inicio_cob' => formatoFechaReporte3(darFormatoYMD($row['cob_iniCobertura'])),
					'fecha_cobro' => formatoFechaReporte3(darFormatoYMD($row['cob_fechCob'])),
					'vez_cobro' => $row['cob_vezCob'],
					'moneda'=> $row['cob_moneda'],
					'importe' => $rowImporte,
					'plan' => $row['nombre_plan'],
					'num_certificado' => $row['cert_num'],
					'facturado' => $objFacturado  
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
}