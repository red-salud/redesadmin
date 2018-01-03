<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactoProveedor extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','otros_helper','fechas_helper'));
		$this->load->model(array('model_contacto_proveedor'));
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
		$lista = $this->model_contacto_proveedor->m_cargar_contacto_proveedor($paramPaginate);
		$fCount = $this->model_contacto_proveedor->m_count_contacto_proveedor($paramPaginate);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idcontactoproveedor' => trim($row['idcontactoproveedor']),
					'nombres' => strtoupper($row['nombres_cp']), 
					'apellidos' => strtoupper($row['apellidos_cp']), 
					'chk_envio_correo'=> ($row['envio_correo_cita'] == 1) ? TRUE : FALSE, 
					'telefono_fijo' => $row['telefono_fijo_cp'],
					'anexo' => $row['anexo_cp'],
					'telefono_movil' => $row['telefono_movil_cp'],
					'email' => $row['email_cp'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_pr']),
					'proveedor' => array(
						'id'=> $row['idproveedor'],
						'descripcion'=> strtoupper($row['nombre_comercial_pr'])	
					),
					'cargo_contacto' => array(
						'id'=> $row['idcargocontacto'],
						'descripcion'=> strtoupper($row['descripcion_ctc'])	
					)
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
		$lista = $this->model_contacto_proveedor->m_cargar_contacto_proveedor($paramPaginate,$paramDatos);
		$fCount = $this->model_contacto_proveedor->m_count_contacto_proveedor($paramPaginate,$paramDatos);
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idcontactoproveedor' => trim($row['idcontactoproveedor']),
					'nombres' => strtoupper($row['nombres_cp']),
					'apellidos' => strtoupper($row['apellidos_cp']),	
					'chk_envio_correo'=> ($row['envio_correo_cita'] == 1) ? TRUE : FALSE, 
					'telefono_fijo' => $row['telefono_fijo_cp'],
					'anexo' => $row['anexo_cp'],
					'telefono_movil' => $row['telefono_movil_cp'],
					'email' => $row['email_cp'],
					'nombre_comercial' => strtoupper($row['nombre_comercial_pr']),
					'razon_social' => strtoupper($row['razon_social_pr']),
					'numero_documento' => $row['numero_documento_pr'],
					'proveedor' => array(
						'id' => trim($row['idproveedor']),
						'descripcion'=> strtoupper($row['nombre_comercial_pr']) 
					), 
					'cargo_contacto' => array(
						'id'=> $row['idcargocontacto'],
						'descripcion'=> strtoupper($row['descripcion_ctc'])	
					),
					'contacto' => strtoupper($row['nombres_cp'].' '.$row['apellidos_cp']) 
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
		$lista = $this->model_contacto_proveedor->m_cargar_contacto_proveedor_limite($allInputs);
		$hayStock = true;
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'id' => $row['idcontactoproveedor'],
					'contacto' => strtoupper($row['contacto']),
					'numero_documento' => $row['numero_documento_pr'],
					'razon_social' => strtoupper($row['razon_social_pr']),
					'telefono_fijo' => $row['telefono_fijo_cp'],
					'telefono_movil' => $row['telefono_movil_cp'],
					'anexo' => $row['anexo_cp'],
					//'cargo' => $row['cargo_cp'],
					'cargo_contacto' => array(
						'id'=> $row['idcargocontacto'],
						'descripcion'=> strtoupper($row['descripcion_ctc'])	
					),
					'idproveedor' => $row['idproveedor']
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
	public function listar_contactos_este_proveedor()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$paramPaginate = $allInputs['paginate'];
		$paramDatos = @$allInputs['datos'];
		$lista = $this->model_contacto_proveedor->m_cargar_contacto_este_proveedor($paramPaginate,$paramDatos);
		$fCount = $this->model_contacto_proveedor->m_count_contacto_este_proveedor($paramPaginate,$paramDatos);
		$arrListado = array();
		foreach ($lista as $row) { 
			array_push($arrListado,
				array(
					'idcontactoproveedor' => trim($row['idcontactoproveedor']),
					'idproveedor' => $row['idproveedor'],
					'nombres' => strtoupper($row['nombres_cp']),
					'apellidos' => strtoupper($row['apellidos_cp']),	
					'chk_envio_correo'=> ($row['envio_correo_cita'] == 1) ? TRUE : FALSE, 
					'chk_envio_correo_str'=> ($row['envio_correo_cita'] == 1) ? 'SI' : 'NO', 
					'cargo_contacto' => array( 
						'id'=> $row['idcargocontacto'],
						'descripcion'=> strtoupper($row['descripcion_ctc'])	
					),
					'telefono_fijo' => $row['telefono_fijo_cp'],
					'anexo' => $row['anexo_cp'],
					'telefono_movil' => $row['telefono_movil_cp'],
					'email' => $row['email_cp'],
					'contacto' => strtoupper($row['nombres_cp'].' '.$row['apellidos_cp'])
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
	    	if( empty($allInputs['proveedor']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    	$allInputs['idproveedor'] = $allInputs['proveedor']['id'];
	    }else{
	    	if( empty($allInputs['idproveedor']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    }

    	$this->db->trans_start();
		if($this->model_contacto_proveedor->m_registrar($allInputs)) { // registro de contacto 
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
	    	if( empty($allInputs['proveedor']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    	$allInputs['idproveedor'] = $allInputs['proveedor']['id'];
	    }else{
	    	if( empty($allInputs['idproveedor']) ){ 
	    		$arrData['message'] = 'No registró todos los campos obligatorios.';
	    		$arrData['flag'] = 0;
	    		$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
			    return;
	    	}
	    }
    	if( @$allInputs['origen'] == 'contactos' ){
	    	$allInputs['idproveedor'] = $allInputs['proveedor']['id'];
	    }
		if($this->model_contacto_proveedor->m_editar($allInputs)){ 
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
		if( $this->model_contacto_proveedor->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	} 
}
