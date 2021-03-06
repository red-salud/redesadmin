<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactoCliente extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper','fechas_helper'));
		$this->load->model(array('model_contacto_cliente'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_contacto(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$lista = $this->model_contacto_cliente->m_cargar_contacto($paramPaginate);
		$fCount = $this->model_contacto_cliente->m_count_contacto($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcontactocliente']),
					'nombres' => strtoupper($row['nombres']),
					'apellidos' => strtoupper($row['apellidos']),	
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento']),			
					'telefono_fijo' => $row['telefono_fijo'],
					'anexo' => $row['anexo'],
					'area_encargada' => $row['area_encargada'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_cli']),
					'telefono_movil' => $row['telefono_movil'],
					'email' => $row['email'],
					'cliente_empresa' => array(
						'id'=> $row['idclienteempresa'],
						'descripcion'=> strtoupper($row['nombre_comercial_cli'])				
					),
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
	public function buscar_contacto_para_lista()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramDatos = @$allInputs['datos'];
		$paramPaginate = $allInputs['paginate'];
		$arrListado = array();
		$fCount = array();
		$lista = $this->model_contacto_cliente->m_cargar_contacto($paramPaginate,$paramDatos);
		$fCount = $this->model_contacto_cliente->m_count_contacto($paramPaginate,$paramDatos);
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcontactocliente']),
					'nombres' => strtoupper($row['nombres']),
					'apellidos' => strtoupper($row['apellidos']),	
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento']),			
					'telefono_fijo' => $row['telefono_fijo'],
					'telefono_movil' => $row['telefono_movil'],
					'anexo' => $row['anexo'],
					'area_encargada' => $row['area_encargada'],
					'email' => $row['email'],
					'razon_social' => strtoupper($row['razon_social_cli']),
					'ruc' => $row['numero_documento_cli'],
					'cliente_empresa' => array(
						'id' => trim($row['idclienteempresa']),
						'descripcion'=> strtoupper($row['nombre_comercial_cli']),
						'nombre_comercial' => strtoupper($row['nombre_comercial_cli']),
						'nombre_corto' => strtoupper($row['nombre_corto_cli']),
						'razon_social' => strtoupper($row['razon_social_cli']),
						'categoria_cliente' => array(
							'id'=> $row['idcategoriacliente'],
							'descripcion'=> $row['descripcion_cc']
						),
						'ruc' => $row['numero_documento_cli'],
						'representante_legal' => $row['representante_legal'],
						'dni_representante_legal' => $row['dni_representante_legal'],
						'direccion_legal' => $row['direccion_legal'],
						'telefono' => $row['telefono_cli'],
						'telefono_contacto'=> $row['telefono_fijo'], // contacto tel ruc
						'anexo_contacto'=> $row['anexo'] // contacto anexo 
					),	
					'contacto' => strtoupper($row['nombres'].' '.$row['apellidos']) 				
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
	public function listar_contacto_empresa_autocomplete()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);	
		$allInputs['limite'] = 15;
		$lista = $this->model_contacto_cliente->m_cargar_contacto_empresa_limite($allInputs);
		$hayStock = true;
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idcontactocliente'],
					'contacto' => strtoupper($row['contacto']),
					'ruc' => $row['ruc'],
					'razon_social' => strtoupper($row['razon_social']),
					'representante_legal' => strtoupper($row['representante_legal']),
					'dni_representante_legal' => $row['dni_representante_legal'],
					'telefono_fijo' => $row['telefono_fijo'],
					'telefono_movil' => $row['telefono_movil'],
					'anexo' => $row['anexo'],
					'area_encargada' => $row['area_encargada'],
					'idclienteempresa' => $row['idclienteempresa']
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
	public function listar_contactos_esta_empresa()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_contacto_cliente->m_cargar_contacto_esta_empresa($paramPaginate,$paramDatos);
		$fCount = $this->model_contacto_cliente->m_count_contacto_esta_empresa($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => trim($row['idcontactocliente']),
					'idclienteempresa' => $row['idclienteempresa'],
					'nombres' => strtoupper($row['nombres']),
					'apellidos' => strtoupper($row['apellidos']),
					'contacto' => strtoupper($row['nombres'].' '.$row['apellidos']),
					'anexo' => $row['anexo'],
					'area_encargada' => strtoupper($row['area_encargada']),
					'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento']),
					'fecha_nacimiento_str' => formatoFechaReporte3($row['fecha_nacimiento']),
					'telefono_fijo' => $row['telefono_fijo'],
					'telefono_movil' => $row['telefono_movil'],
					'email' => strtoupper($row['email']) 
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
		$this->load->view('contacto/mant_contacto');
	}	
	public function ver_popup_busqueda_contacto()
	{
		$this->load->view('contacto/busq_contacto_popup');
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	
		// VALIDACIONES
	    if( @$allInputs['origen'] == 'contactos' ){ 
	    	if( empty($allInputs['cliente_empresa']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    	$allInputs['idclienteempresa'] = $allInputs['cliente_empresa']['id'];
	    }else{
	    	if( empty($allInputs['idclienteempresa']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    }

    	$this->db->trans_start();
		if($this->model_contacto_cliente->m_registrar($allInputs)) { // registro de contacto 
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
	    if( @$allInputs['origen'] == 'contactos' ){ 
	    	if( empty($allInputs['cliente_empresa']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    	$allInputs['idclienteempresa'] = $allInputs['cliente_empresa']['id'];
	    }else{
	    	if( empty($allInputs['idclienteempresa']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    }
    	if( @$allInputs['origen'] == 'contactos' ){
	    	$allInputs['idclienteempresa'] = $allInputs['cliente_empresa']['id'];
	    }
		if($this->model_contacto_cliente->m_editar($allInputs)){
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
		if( $this->model_contacto_cliente->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 
}
