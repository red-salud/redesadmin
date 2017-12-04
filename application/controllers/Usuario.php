<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionRS.
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_usuario','model_proveedor','model_colaborador')); 
    }

	public function listar_usuario(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_usuario->m_cargar_usuario($paramPaginate);
		$fCount = $this->model_usuario->m_count_usuario($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idusuario' => $row['idusuario'],
					'tipo_usuario' => array(
						'id'=> $row['idtipousuario'],
						'descripcion'=> $row['descripcion_tu']
					),					
					'username' => strtoupper($row['username']),
					'ult_inicio_sesion' => formatoFechaReporte4($row['ultimo_inicio_sesion']),
					'idcolaborador'=> $row['idcolaborador'],
					'colaborador'=> $row['colaborador'],
					'idproveedor'=> $row['idproveedor'],
					'proveedor'=> $row['proveedor'] 
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
		$this->load->view('usuario/mant_usuario');
	}
	public function ver_popup_asociar_proveedor()
	{
		$this->load->view('usuario/mant_asociar_proveedor');
	}
	public function ver_popup_asociar_colaborador()
	{
		$this->load->view('usuario/mant_asociar_colaborador');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;

    	// VALIDACIONES  

    	/* VALIDAR QUE SE HAYA REGISTRADO CLAVE */
		if( empty($allInputs['password']) || empty($allInputs['password_view']) ){ 
			$arrData['message'] = 'Los campos de contraseña están vacios.';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}

    	/* VALIDAR QUE LAS CLAVES COINCIDAN */
		if($allInputs['password'] != $allInputs['password_view']){
			$arrData['message'] = 'Las contraseñas no coinciden, inténtelo nuevamente';
	    	$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
		}
		/* VALIDAR SI EL USUARIO YA EXISTE */	
    	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username']);
    	if( !empty($fUsuario) ) {
    		$arrData['message'] = 'El Usuario ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}   	
   		/* VALIDAR QUE SE ASOCIE UN PROVEEDOR */ 
   		if( $allInputs['tipo_usuario']['key_tu'] == 'key_proveedor' ){ 
   			if( empty($allInputs['proveedor']) || empty($allInputs['idproveedor']) ){
   				$arrData['message'] = 'No se asoció ningún proveedor.';
				$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
   			}
   			
   		}else{ 
   		/* VALIDAR QUE SE ASOCIE UN COLABORADOR */
   			if( empty($allInputs['colaborador']) || empty($allInputs['idcolaborador']) ){
   				$arrData['message'] = 'No se asoció ningún colaborador.';
				$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return; 
   			} 
   		} 
		$this->db->trans_start();
		if($this->model_usuario->m_registrar($allInputs)) { // registro de usuario 
			$allInputs['idusuario'] = GetLastId('idusuario','usuario'); 
			$arrData['message'] = '- Se registraron los datos correctamente'; 
			$arrData['flag'] = 1; 
			if( $allInputs['tipo_usuario']['key_tu'] == 'key_proveedor' ){ 
				// asociar un proveedor 
				if( $this->model_proveedor->m_asociar_usuario_a_proveedor($allInputs) ){ 
					$arrData['message'] .= '<br/> - Se asoció al proveedor'; 
				}
			}else{
				// asociar un colaborador 
				if( $this->model_colaborador->m_asociar_usuario_a_colaborador($allInputs) ){ 
					$arrData['message'] .= '<br/> - Se asoció al colaborador'; 
				}
			} 
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
		/* VALIDAR SI EL USUARIO YA EXISTE */
    	$fUsuario = $this->model_usuario->m_validar_usuario_username($allInputs['username'],TRUE,$allInputs['idusuario']);
    	if( $fUsuario ) {
    		$arrData['message'] = 'El Usuario ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
    	$this->db->trans_start();
		if($this->model_usuario->m_editar($allInputs)) { // edicion de usuario 
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
		if( $this->model_usuario->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 	

	 public function listar_tipo_usuario_cbo(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_usuario->m_cargar_usuario_cbo();
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idtipousuario'],
					'descripcion' => strtoupper($row['descripcion_tu']),
					'key_tu'=> $row['key_tu']
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