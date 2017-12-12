<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serie extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        // $this->sessionVP = @$this->session->userdata('sess_vp_'.substr(base_url(),-20,7));
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_serie','model_tipo_documento_serie','model_tipo_documento_mov')); 

    }
	public function listar_serie_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_serie->m_cargar_serie_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idserie'],
					'descripcion' => $row['numero_serie'],
					'observaciones' => strtoupper($row['descripcion_ser']) 
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
	public function ver_popup_formulario()
	{
		$this->load->view('serie/mant_serie');
	} 
	public function editar_correlativo_actual()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$numSerie = $allInputs['serie'];
    	$fSerie = $this->model_serie->m_cargar_esta_serie($numSerie);
    	$allInputs['idserie'] = $fSerie['idserie'];
    	$fSerieCorrelativo = $this->model_serie->m_validar_serie_correlativo_existe($allInputs['idserie'],$allInputs['idtipodocumentomov']); 
    	if( empty($fSerieCorrelativo) ){
    		// registrar 
    		if($this->model_serie->m_registrar_correlativo_actual($allInputs)){
				$arrData['message'] = 'Se registraron los datos correctamente';
	    		$arrData['flag'] = 1;
			}
    	}else{
    		// editar
    		if($this->model_serie->m_editar_correlativo_actual($allInputs)){
				$arrData['message'] = 'Se editaron los datos correctamente';
	    		$arrData['flag'] = 1;
			}
    	} 
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES     
    	$fSerie = $this->model_serie->m_validar_num_serie($allInputs['numero_serie']);
    	if( !empty($fSerie) ) {
    		$arrData['message'] = 'El número serie ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		} 
   		//$lista = $this->model_tipo_documento_mov->m_cargar_tipo_documento_grilla();
   		//print_r($lista); exit();
    	$this->db->trans_start();
		if($this->model_serie->m_registrar($allInputs)) { // registro de serie
			$arrData['idserie'] = GetLastId('idserie','serie');
			$lista = $this->model_tipo_documento_mov->m_cargar_tipo_documento_mov_cbo();
			foreach ($lista as $key => $value) {
				$arrData['idtipodocumentomov'] = $value['idtipodocumentomov'];
				if( $this->model_tipo_documento_serie->m_registrar_tipo_documento_serie($arrData) ){ 
				} 
			}
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}
