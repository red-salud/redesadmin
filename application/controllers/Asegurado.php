<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asegurado extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_certificado','model_cobro','model_asegurado')); 

    } 	
	public function editar_asegurado_inline()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		// print_r($allInputs); exit();
		$arrData['message'] = 'Error al modificar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES 
    	// campo proveedor vacio
    	if( empty($allInputs['asegurado']) ){ 
    		$arrData['message'] = 'No se a seleccionado asegurado.'; 
			$arrData['flag'] = 0; 
			$this->output 
			    ->set_content_type('application/json') 
			    ->set_output(json_encode($arrData)); 
			return; 
    	}
    	$this->db->trans_start();
		if($this->model_asegurado->m_editar_asegurado_inline($allInputs['asegurado'])) { // edición de asegurado 
			$arrData['message'] = 'Se editaron los datos correctamente'; 
			$arrData['flag'] = 1; 
		} 
		$this->db->trans_complete(); 
		$this->output 
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData)); 
	}
}