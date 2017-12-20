<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colaborador extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_colaborador','model_usuario')); 

    } 
	public function listar_colaboradores(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_colaborador->m_cargar_colaborador($paramPaginate);
		$fCount = $this->model_colaborador->m_count_colaborador($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcolaborador']),
					'nombres' => strtoupper($row['nombres_col']),
					'apellidos' => strtoupper($row['ap_paterno_col'].' '.$row['ap_materno_col']),
					'ap_paterno' => strtoupper($row['ap_paterno_col']),
					'ap_materno' => strtoupper($row['ap_materno_col']),
					'num_documento' => $row['numero_documento_col'],
					'celular' => $row['celular_col'],
					'email' => strtoupper($row['correo_laboral']),
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento_col']),
					'tipo_usuario' => array(
						'id'=> $row['idtipousuario'],
						'descripcion'=> $row['descripcion_tu']
					), 
					'username' => $row['username'],
					'idusuario' => $row['idusuario']
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
	public function listar_colaboradores_sin_usuario()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_colaborador->m_cargar_colaboradores_sin_usuario($paramPaginate,$paramDatos);
		$fCount = $this->model_colaborador->m_count_colaboradores_sin_usuario($paramPaginate,$paramDatos);
		//var_dump('hola'); exit();
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcolaborador']),
					'nombres' => strtoupper($row['nombres_col']),
					'apellidos' => strtoupper($row['ap_paterno_col'].' '.$row['ap_materno_col']),
					'ap_paterno' => strtoupper($row['ap_paterno_col']),
					'ap_materno' => strtoupper($row['ap_materno_col']),
					'num_documento' => $row['numero_documento_col'],
					'celular' => $row['celular_col'],
					'email' => strtoupper($row['correo_laboral']),
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento_col']),
					'tipo_usuario' => array(
						'id'=> $row['idtipousuario'],
						'descripcion'=> $row['descripcion_tu']
					), 
					'username' => $row['username'],
					'idusuario' => $row['idusuario']
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
	public function ver_popup_formulario()
	{
		$this->load->view('colaborador/mant_colaborador');
	}	

	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_colaborador->m_registrar($allInputs)) { // registro de colaborador
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function editar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	
    	$this->db->trans_start();
		if($this->model_colaborador->m_editar($allInputs)) { // edicion de colaborador
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
    	// var_dump($allInputs);exit();
    	// $fCotizacion = $this->model_colaborador->m_cargar_cotizacion_colaborador($allInputs);
    	// if( !empty($fCotizacion) ){ 
    	// 	$arrData['message'] = 'Ya se a registrado una cotización, no se puede anular'; 
    	// 	$arrData['flag'] = 0;
    	// 	$this->output
		   //  	->set_content_type('application/json')
		   //  	->set_output(json_encode($arrData));
		   //  return;
    	// } 
		if( $this->model_colaborador->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		if( $this->model_usuario->m_anular($allInputs) ){ 
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 

	public function listar_colaboradores_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_colaborador->m_cargar_colaborador_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idcolaborador'],
					'descripcion' => strtoupper($row['colaborador']) 
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