<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        // $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_producto')); 

    }
	public function listar_productos_tipo_consulta_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_producto->m_cargar_producto_tipo_consulta_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array( 
					'id' => $row['idproducto'], 
					'idespecialidad' => $row['idespecialidad'],
					'descripcion' => strtoupper($row['descripcion_prod']), 
					'key_tp'=> $row['key_tp'] 
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
