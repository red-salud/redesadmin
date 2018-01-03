<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'third_party/spout242/src/Spout/Autoloader/autoload.php';

//lets Use the Spout Namespaces 
use Box\Spout\Writer\WriterFactory; 
use Box\Spout\Common\Type; 
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;
class CentralReportes extends CI_Controller {
	public function __construct()
    { 
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS. 
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_cobro')); 

    } 
    public function exportar_lista_cobros() 
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_cobro->m_cargar_cobros(NULL,$paramDatos);
	    // $z = new XMLReader;
	    // var_dump($z); exit();
	    ini_set('max_execution_time', 600); 
	    ini_set('memory_limit','2G'); 
	    $writer = WriterFactory::create(Type::XLSX); 
	    $fileName = $allInputs['titulo'].'.xls'; 
	    $filePath = 'assets/dinamic/excelTemporales/'.$fileName; 
	    $writer->openToFile($filePath); 
	    
	    // $writer->openToBrowser($fileName); // stream data directly to the browser 
	    $singleRow = array('N° CERTIFICADO','N° DNI','CONTRATANTE','PLAN','FECHA DE COBRO','VEZ DE COBRO','IMPORTE'); 
	    $writer->addRow($singleRow); 
	    
	    $arrData['flag'] = 0; 
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
	        $writer->addRow( 
	          array( 
	            $row['cert_num'], 
	            $row['cont_numDoc'], 
	            $row['cont_nom1'].' '.$row['cont_nom2'].' '.$row['cont_ape1'].' '.$row['cont_ape2'], 
	            $row['nombre_plan'], 
	            formatoFechaReporte3(darFormatoYMD($row['cob_fechCob'])), 
	            $row['cob_vezCob'], 
	            $rowImporte 
	          ) 
	        ); 
	    } 
	    $writer->addRow( $singleRow ); 
	    $arrData = array( 
	      'urlTempEXCEL'=> $filePath, 
	      'flag'=> 1 
	    ); 
	    $writer->close();
	    $this->output
	        ->set_content_type('application/json') 
	        ->set_output(json_encode($arrData)); 
	}
} 
?>