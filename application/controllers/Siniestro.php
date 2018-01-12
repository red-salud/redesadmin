<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siniestro extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_siniestro','model_cita'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	} 
	public function listar_historial_siniestros()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = $allInputs['datos'];
		$lista = $this->model_siniestro->m_cargar_siniestros($paramPaginate,$paramDatos);
		$fCount = $this->model_siniestro->m_count_siniestros($paramPaginate,$paramDatos);
		//var_dump('hola'); exit();
		$arrListado = array();
		foreach ($lista as $row) { 
			$objEstado = array();
			$objEstado['valor'] = $row['estado_siniestro'];
			if( $row['estado_siniestro'] == 1 ){ // ABIERTO (amarillo)
				$objEstado['claseIcon'] = 'fa fa-check';
				$objEstado['claseLabel'] = 'label-warning';
				$objEstado['labelText'] = 'ABIERTO';
				
			}elseif( $row['estado_siniestro'] == 2 ){ // CERRADO (verde) 
				$objEstado['claseIcon'] = 'fa fa-eye';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'CERRADO';
			}elseif( $row['estado_siniestro'] == 0 ){ // ANULADO (rojo)
				$objEstado['claseIcon'] = 'fa fa-ban';
				$objEstado['claseLabel'] = 'label-danger';
				$objEstado['labelText'] = 'ANULADO';
			}elseif( $row['estado_siniestro'] == 3 ){ // NO ESPECIFICADO (gris)
				$objEstado['claseIcon'] = 'fa fa-ban';
				$objEstado['claseLabel'] = 'label-default';
				$objEstado['labelText'] = 'NO ESPECIFICADO';
			}
			array_push($arrListado,
				array(
					'idsiniestro' => $row['idsiniestro'],
					'idcita' => $row['idcita'],
					'mes_atencion' => darFormatoMesAno($row['fecha_atencion']),
					'fecha_atencion' => formatoFechaReporte3($row['fecha_atencion']),
					'aseg_num_doc' => $row['aseg_numDoc'],
					'asegurado' => $row['asegurado'],
					'aseg_telefono' => $row['aseg_telf'],
					'num_orden_atencion' => $row['num_orden_atencion'],
					'nombre_plan' => $row['nombre_plan'],
					'proveedor' => $row['nombre_comercial_pr'],
					'cliente' => $row['nombre_comercial_cli'],
					'especialidad' => $row['nombre_esp'],
					'estado_obj' => $objEstado
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
		//var_dump('<pre>',$arrData); exit();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0; 
    	if( empty($allInputs['idsiniestro']) ){
    		$arrData['message'] = 'No se ha seleccionado una atención. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json') 
			    ->set_output(json_encode($arrData));
			return;
    	}
		if( $this->model_siniestro->m_anular($allInputs) ){ 
			$arrData['message'] = '- Se anularon los datos del siniestro correctamente <br /> ';
    		$arrData['flag'] = 1; 
    		if( !empty($allInputs['idcita']) ){
    			if( $this->model_cita->m_retornar_estado_confirmado($allInputs) ){ 
	    			$arrData['message'] .= '- Se anularon los datos de la cita correctamente <br /> ';
	    		}
    		}else{
    			$arrData['message'] .= '- Esta atención no cuenta con reserva previa; no aparecerá en el calendario tras su anulación. <br /> ';
    		}
    		
		} 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}