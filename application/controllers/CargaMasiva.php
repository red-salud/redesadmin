<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CargaMasiva extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        // $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_categoria_cliente')); 

    }
    
}
