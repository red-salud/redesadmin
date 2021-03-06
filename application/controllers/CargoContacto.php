<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CargoContacto extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_cargo_contacto')); 
    }
   	public function listar_cargo_contacto_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_cargo_contacto->m_cargar_cargo_contacto_cbo($allInputs); 
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado, 
				array(
					'id' => $row['idcargocontacto'], 
					'descripcion' => strtoupper($row['descripcion_ctc']) 
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
}
?>