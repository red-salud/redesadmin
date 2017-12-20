<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_proveedor','model_tipo_proveedor','model_usuario','model_contacto_proveedor'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_proveedor()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_proveedor->m_cargar_proveedor($paramPaginate,$paramDatos);
		$fCount = $this->model_proveedor->m_count_proveedor($paramPaginate,$paramDatos);
		//var_dump('hola'); exit();
		$arrListado = array();
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_pr'] == 1 ){ // ACTIVO (verde)
				$objEstado['claseIcon'] = 'fa fa-check';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'ACTIVO';
			}elseif( $row['estado_pr'] == 2 ){ // OBSERVADO (amarillo) 
				$objEstado['claseIcon'] = 'fa fa-eye';
				$objEstado['claseLabel'] = 'label-warning';
				$objEstado['labelText'] = 'OBSERVADO';
			}elseif( $row['estado_pr'] == 3 ){ // INACTIVO (gris)
				$objEstado['claseIcon'] = 'fa fa-ban';
				$objEstado['claseLabel'] = 'label-default';
				$objEstado['labelText'] = 'INACTIVO';
			}
			array_push($arrListado,
				array(
					'idproveedor' => $row['idproveedor'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_pr']),
					'razon_social' => strtoupper($row['razon_social_pr']),
					'tipo_proveedor' => array(
						'id'=> $row['idtipoproveedor'],
						'descripcion'=> $row['descripcion_tpr']
					),
					'tipo_documento_identidad' => array(
						'id'=> $row['idtipodocumentoidentidad'],
						'descripcion'=> $row['descripcion_tdi']
					),
					'numero_documento' => $row['numero_documento_pr'],
					'direccion' => $row['direccion_pr'],
					'iddepartamento' => $row['cod_departamento_pr'],
					'departamento' => strtoupper($row['departamento']),
					'idprovincia' => $row['cod_provincia_pr'],
					'provincia' => strtoupper($row['provincia']),
					'iddistrito' => $row['cod_distrito_pr'],
					'distrito' => strtoupper($row['distrito']),
					'cod_sunasa'=> $row['cod_sunasa_pr'],
					'referencia'=> $row['referencia_pr'],
					'idusuario'=> $row['idusuario'],
					'username'=> $row['username'], 
					'lat' => $row['latitud'],
					'lng' => $row['longitud'],
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
	public function listar_proveedores_sin_usuario()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_proveedor->m_cargar_proveedores_sin_usuario($paramPaginate,$paramDatos);
		$fCount = $this->model_proveedor->m_count_proveedores_sin_usuario($paramPaginate,$paramDatos);
		//var_dump('hola'); exit();
		$arrListado = array();
		foreach ($lista as $row) { 
			$objEstado = array();
			if( $row['estado_pr'] == 1 ){ // ACTIVO (verde)
				$objEstado['claseIcon'] = 'fa fa-check';
				$objEstado['claseLabel'] = 'label-success';
				$objEstado['labelText'] = 'ACTIVO';
			}elseif( $row['estado_pr'] == 2 ){ // OBSERVADO (amarillo) 
				$objEstado['claseIcon'] = 'fa fa-eye';
				$objEstado['claseLabel'] = 'label-warning';
				$objEstado['labelText'] = 'OBSERVADO';
			}elseif( $row['estado_pr'] == 3 ){ // INACTIVO (gris)
				$objEstado['claseIcon'] = 'fa fa-ban';
				$objEstado['claseLabel'] = 'label-default';
				$objEstado['labelText'] = 'INACTIVO';
			} 
			array_push($arrListado,
				array(
					'idproveedor' => $row['idproveedor'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_pr']),
					'razon_social' => strtoupper($row['razon_social_pr']),
					'tipo_proveedor' => array(
						'id'=> $row['idtipoproveedor'],
						'descripcion'=> $row['descripcion_tpr']
					),
					'tipo_documento_identidad' => array(
						'id'=> $row['idtipodocumentoidentidad'],
						'descripcion'=> $row['descripcion_tdi']
					),
					'numero_documento' => $row['numero_documento_pr'],
					'direccion' => $row['direccion_pr'],
					'iddepartamento' => $row['cod_departamento_pr'],
					'departamento' => strtoupper($row['departamento']),
					'idprovincia' => $row['cod_provincia_pr'],
					'provincia' => strtoupper($row['provincia']),
					'iddistrito' => $row['cod_distrito_pr'],
					'distrito' => strtoupper($row['distrito']),
					'cod_sunasa'=> $row['cod_sunasa_pr'],
					'referencia'=> $row['referencia_pr'],
					'idusuario'=> $row['idusuario'],
					'username'=> $row['username'], 
					'lat' => $row['latitud'],
					'lng' => $row['longitud'],
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
	public function listar_proveedores_cbo()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_proveedor->m_cargar_proveedores_cbo(); 
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado, 
				array(
					'id' => $row['idproveedor'], 
					'descripcion' => strtoupper($row['nombre_comercial_pr']), 
					'razon_social' => $row['razon_social_pr'],
					'numero_documento'=> $row['numero_documento_pr']
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
	public function buscar_proveedor_para_formulario()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		if(empty($allInputs['tipo_documento']['destino']) || empty($allInputs['num_documento']) ){ 
			$arrData['message'] = 'No hay datos.';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		$arrListado = array();
		$fProveedor = $this->model_proveedor->m_buscar_este_proveedor($allInputs); 
		$fProveedor['razon_social'] = @$fProveedor['razon_social_pr']; 
		if( empty($fProveedor['razon_social_pr']) ){ 
			$arrData['message'] = 'Falta configurar los datos de los proveedores desde el mantenimiento de proveedores.';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		$fProveedor['nombre_comercial'] = @$fProveedor['nombre_comercial_pr']; 
		$fProveedor['direccion'] = @$fProveedor['direccion_pr']; 
		// var_dump($fProveedor); exit();
		if( !empty($fProveedor['idproveedor']) ){ 
	    	$arrData['datos'] = array(
	    		'proveedor'=> $fProveedor 
	    	);
	    	$arrData['message'] = 'Proveedor seleccionado correctamente.';
	    	$arrData['flag'] = 1;
		}else{
			$arrData['message'] = 'No se encontró al proveedor.';
			$arrData['flag'] = 0;
		}
		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function ver_popup_formulario()
	{
		$this->load->view('proveedor/mant_proveedor');
	}
	public function ver_popup_contactos()
	{
		$this->load->view('proveedor/mant_contactoProveedor');
	}
	public function ver_popup_busqueda_proveedores()
	{
		$this->load->view('proveedor/busq_proveedor_popup');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
	    /* VALIDAR SI EL RUC YA EXISTE */ 
    	$fProveedor = $this->model_proveedor->m_validar_proveedor_num_documento($allInputs['numero_documento']);
    	if( !empty($fProveedor) ) {
    		$arrData['message'] = 'El N° RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
   		/* validar datos de los contactos */ 
   		$contactosValidos = TRUE;
   		if( empty($allInputs['contactos']) ){ 
   			$contactosValidos = FALSE;
   		}
    	/* validar datos del usuario */
    	$usuarioValido = TRUE; 
    	if( empty($allInputs['username']) || empty($allInputs['password_view']) || empty($allInputs['password']) ){
    		$usuarioValido = FALSE; 
    	}
    	if($usuarioValido){
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
	    		$arrData['message'] = 'El Usuario ingresado ya existe.';
				$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
	   		} 
    	}
    	$this->db->trans_start();
		if($this->model_proveedor->m_registrar($allInputs)) { // registro de proveedor 
			$allInputs['idproveedor'] = GetLastId('idproveedor','proveedor'); 
			$arrData['message'] = '- Se registraron los datos del proveedor correctamente.'; 
			$arrData['flag'] = 1;
			if($usuarioValido){
				if($this->model_usuario->m_registrar($allInputs)){ 
					$allInputs['idusuario'] = GetLastId('idusuario','usuario'); 
					$this->model_proveedor->m_asociar_usuario_a_proveedor($allInputs);
					$arrData['message'] .= '- <br/> Se generó el usuario correctamente.'; 
				}
			}
			if($contactosValidos){ 
				$boolRegistrarContacto = FALSE;
				foreach ($allInputs['contactos'] as $key => $row) { 
					$row['idproveedor'] = $allInputs['idproveedor'];
					if($this->model_contacto_proveedor->m_registrar($row)){
						$boolRegistrarContacto = TRUE;
					}
				}
				if( $boolRegistrarContacto ){
					$arrData['message'] .= '- <br/> Se agregaron los contactos correctamente.'; 
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
		$arrData['message'] = 'Error al editar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
		/* VALIDAR SI EL RUC YA EXISTE */
    	$fProveedor = $this->model_proveedor->m_validar_proveedor_num_documento($allInputs['numero_documento'],TRUE,$allInputs['idproveedor']);
    	if( $fProveedor ) {
    		$arrData['message'] = 'El RUC ingresado, ya existe.';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
   		}
		if($this->model_proveedor->m_editar($allInputs)){
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
		if( $this->model_proveedor->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function cambiar_estado()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'No se pudo actualizar los datos';
    	$arrData['flag'] = 0;
		if( $this->model_proveedor->m_cambiar_estado($allInputs) ){ 
			$arrData['message'] = 'Se cambiaron los datos correctamente'; 
    		$arrData['flag'] = 1; 
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function listar_proveedor_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_proveedor->m_cargar_proveedor_cbo();
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
	public function listar_proveedor_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$allInputs['limite'] = 15;
		$lista = $this->model_proveedor->m_cargar_proveedor_limite($allInputs);
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