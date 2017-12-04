<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cliente_empresa','model_cliente_persona'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function buscar_cliente_para_formulario()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		if(empty($allInputs['tipo_documento']['destino']) || empty($allInputs['numero_documento']) ){ 
			$arrData['message'] = 'No hay datos.';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		$arrListado = array();
		if( $allInputs['tipo_documento']['destino'] == 1 ){ // empresa 
			$fCliente = $this->model_cliente_empresa->m_buscar_cliente_empresa($allInputs);
			$fCliente['id'] = @$fCliente['idclienteempresa'];
		}
		if( $allInputs['tipo_documento']['destino'] == 2 ){ // persona 
			$fCliente = $this->model_cliente_persona->m_buscar_cliente_persona($allInputs);
			if( $fCliente['sexo_cli'] == 'M' ){
				$fCliente['desc_sexo'] = 'MASCULINO';
			}
			if( $fCliente['sexo_cli'] == 'F' ){
				$fCliente['desc_sexo'] = 'FEMENINO';
			}
			$fCliente['email'] = strtoupper($fCliente['email_cli']); 
			$fCliente['id'] = @$fCliente['idclientepersona'];
			$fCliente['cliente'] = strtoupper(@$fCliente['nombres_cli'].' '.@$fCliente['ap_paterno_cli'].' '.@$fCliente['ap_materno_cli']); 
			$fCliente['sexo'] = array( 
				'id'=> @$fCliente['sexo_cli'],
				'descripcion'=> @$fCliente['desc_sexo'] 
			);
		}
		// var_dump($fCliente); exit();
		if( !empty($fCliente['id']) ){
			$fCliente['categoria_cliente'] = array( 
				'id'=> $fCliente['idcategoriacliente'],
				'descripcion'=> $fCliente['descripcion_cc']
			); 

	    	$arrData['datos'] = array(
	    		'cliente'=> $fCliente 
	    	);
	    	$arrData['message'] = 'Cliente seleccionado correctamente.';
	    	$arrData['flag'] = 1;
		}else{
			$arrData['message'] = 'No se encontró al cliente..';
			$arrData['flag'] = 0;
		}
		
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function buscar_cliente_para_lista()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		// var_dump($allInputs);
		$paramDatos = $allInputs['datos']; 
		$paramPaginate = $allInputs['paginate'];
		if(empty($paramDatos['tipo_cliente'])){ 
			$arrData['message'] = 'No hay datos.';
    		$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return; 
		}
		$arrListado = array();
		$fCount = array();
		if( $paramDatos['tipo_cliente'] == 'ce' ){ // empresa 
			$lista = $this->model_cliente_empresa->m_cargar_cliente_empresa($paramPaginate); 
			$fCount = $this->model_cliente_empresa->m_count_cliente_empresa($paramPaginate);
			foreach ($lista as $row) { 
				array_push($arrListado,
					array(
						'id' => $row['idclienteempresa'],
						'idclienteempresa' => $row['idclienteempresa'],
						'nombre_comercial' => strtoupper($row['nombre_comercial_cli']),
						'nombre_corto' => strtoupper($row['nombre_corto_cli']),
						'razon_social' => strtoupper($row['razon_social_cli']),
						'categoria_cliente' => array(
							'id'=> $row['idcategoriacliente'],
							'descripcion'=> $row['descripcion_cc']
						),
						'ruc' => $row['numero_documento_cli'],
						'numero_documento' => $row['numero_documento_cli'],
						'representante_legal' => $row['representante_legal'],
						'dni_representante_legal' => $row['dni_representante_legal'],
						'direccion_legal' => $row['direccion_legal'],
						'telefono' => $row['telefono_cli']
					)
				);
			}
		}
		if( $paramDatos['tipo_cliente'] == 'cp' ){ // persona 
			$lista = $this->model_cliente_persona->m_cargar_cliente_persona($paramPaginate);
			$fCount = $this->model_cliente_persona->m_count_cliente_persona($paramPaginate);
			foreach ($lista as $row) { 
				if( $row['sexo_cli'] == 'M' ){
					$row['desc_sexo'] = 'MASCULINO';
				}
				if( $row['sexo_cli'] == 'F' ){
					$row['desc_sexo'] = 'FEMENINO';
				}
				array_push($arrListado,
					array(
						'id' => $row['idclientepersona'],
						'idclientepersona' => $row['idclientepersona'],
						'nombres' => strtoupper($row['nombres_cli']),
						'apellidos' => strtoupper($row['ap_paterno_cli'].' '.$row['ap_materno_cli']),
						'ap_paterno'=> strtoupper($row['ap_paterno_cli']),
						'ap_materno'=> strtoupper($row['ap_materno_cli']),
						'cliente' => strtoupper($row['nombres_cli'].' '.$row['ap_paterno_cli'].' '.$row['ap_materno_cli']),
						'numero_documento' => $row['numero_documento_cli'],
						'categoria_cliente' => array(
							'id'=> $row['idcategoriacliente'],
							'descripcion'=> $row['descripcion_cc']
						),
						'sexo'=> array(
							'id'=> $row['sexo_cli'],
							'descripcion'=> $row['desc_sexo'] 
						),
						'edad' => devolverEdad($row['fecha_nacimiento_cli']),
						'fecha_nacimiento' => darFormatoDMY($row['fecha_nacimiento_cli']),
						'fecha_nacimiento_str' => formatoFechaReporte3($row['fecha_nacimiento_cli']),
						'telefono_fijo' => $row['telefono_fijo_cli'],
						'telefono_movil' => $row['telefono_movil_cli'],
						'email' => $row['email_cli']
					)
				);
			}
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
	public function ver_popup_busqueda_clientes()
	{
		$this->load->view('cliente/busq_cliente_popup');
	}
}