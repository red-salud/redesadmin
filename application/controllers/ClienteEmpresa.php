<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClienteEmpresa extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cliente_empresa','model_categoria_cliente'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_cliente_empresa()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_cliente_empresa->m_cargar_cliente_empresa($paramPaginate);
		$fCount = $this->model_cliente_empresa->m_count_cliente_empresa($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idclienteempresa']),
					'nombre_comercial' => strtoupper($row['nombre_comercial_cli']),
					'nombre_corto' => strtoupper($row['nombre_corto_cli']),
					'razon_social' => strtoupper($row['razon_social_cli']),
					'categoria_cliente' => array(
						'id'=> $row['idcategoriacliente'],
						'descripcion'=> $row['descripcion_cc']
					),
					'numero_documento' => $row['numero_documento_cli'],
					'representante_legal' => $row['representante_legal'],
					'dni_representante_legal' => $row['dni_representante_legal'],
					'direccion_legal' => $row['direccion_legal'],
					'pagina_web' => $row['pagina_web_cli'],
					'telefono' => $row['telefono_cli']
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
		$this->load->view('cliente-empresa/mant_clienteEmpresa');
	}
	public function ver_popup_contactos()
	{
		$this->load->view('cliente-empresa/mant_contactoEmpresa');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
	    /* VALIDAR SI EL RUC YA EXISTE */ 
    	$fCliente = $this->model_cliente_empresa->m_validar_cliente_empresa_num_documento($allInputs['numero_documento']);
    	if( !empty($fCliente) ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
    	
    	$this->db->trans_start();
		if($this->model_cliente_empresa->m_registrar($allInputs)) { // registro de cliente empresa 
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
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
		/* VALIDAR SI EL RUC YA EXISTE */
    	$fCliente = $this->model_cliente_empresa->m_validar_cliente_empresa_num_documento($allInputs['numero_documento'],TRUE,$allInputs['id']);
    	if( $fCliente ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
		if($this->model_cliente_empresa->m_editar($allInputs)){
			$arrData['message'] = 'Se editaron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function anular()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);

		$arrData['message'] = 'No se pudo anular los datos';
    	$arrData['flag'] = 0;
		if( $this->model_cliente_empresa->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}

	public function listar_cliente_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_cliente_empresa->m_cargar_cliente_empresa_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idclienteempresa'],
					'descripcion' => strtoupper($row['nombre_comercial_cli']) 
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

	public function listar_cliente_empresa_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs['limite'] = 15;
		$lista = $this->model_cliente_empresa->m_cargar_cliente_empresa_limite($allInputs);
		$hayStock = true;
		$arrListado = array();

		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idclienteempresa'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_cli'])
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