<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cita extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cita'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_citas_en_calendario(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_cita->m_cargar_citas($allInputs); 
		$arrListado = array();
		foreach ($lista as $row) { 
			if( $row['es_atencion'] == 1 ){ 
				$clases = 'b-l b-2x b-success';
			}else{
				$clases = 'b-l b-2x b-primary';
			}
			$className = array($clases); 
			array_push($arrListado,
				array(
					'id' => $row['idcita'],
					'hora_desde_sql' => $row['hora_cita_inicio'],
					'hora_hasta_sql' => $row['hora_cita_fin'],
					'hora_desde' => strtotime($row['hora_cita_inicio']),
					'hora_hasta' => strtotime($row['hora_cita_fin']),
					'estado_cita' => $row['estado_cita'],
					'fecha' => $row['fecha_cita'],
					'asegurado_cert' => $row['asegurado'].' - '.$row['nombre_plan'],
					'asegurado' => array(
						'idasegurado' => $row['aseg_id'],
						'asegurado' => $row['asegurado'] 
					),
					'especialidad' => array(
						'idespecialidad' => $row['idespecialidad'],
						'especialidad' => strtoupper($row['nombre_esp']),
					),
					'proveedor' => array(
						'id' => $row['idproveedor'],
						'descripcion' => $row['nombre_comercial_pr']
					),
					'producto' => array(
						'id' => $row['idproducto'],
						'descripcion' => strtoupper($row['descripcion_prod']) 
					),
					'plan' => array(
						'id' => $row['idplan'],
						'descripcion' => $row['nombre_plan'] 
					),
					'tipoproducto' => array(
						'idtipoproducto' => $row['idtipoproducto'],
						'tipo_producto' => $row['descripcion_tp'],
					),
					'es_atencion' => $row['es_atencion'],
					'observaciones' => $row['observaciones_cita'],
					// 'atencion' => array( 
					// 		'idatencion' => (int)$row['idatencion'], 
					// 		'fecha_atencion' => $row['fecha_atencion'],
					// 		'diagnostico_notas' => $row['diagnostico_notas'],
					// 		'indicaciones_dieta' => $row['indicaciones_dieta'],
					// 		'tipo_dieta' => $row['tipo_dieta'],
					// 		'paciente' => $row['nombre'] . ' ' . $row['apellidos'],
					// 	),
					'className' => $className,
					'start' => $row['fecha_cita'] .' '. $row['hora_cita_inicio'],
					'end' => $row['fecha_cita'] .' '. $row['hora_cita_fin'],
					'title' => $row['asegurado'] .' - '. $row['nombre_comercial_pr'],
					'allDay' => FALSE,
					'durationEditable' => FALSE,
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
	public function ver_popup_form_cita()
	{
		$this->load->view('cita/popup_form_cita'); 
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		// print_r($allInputs); exit();
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES
    	// campo asegurado vacio 
    	if( empty($allInputs['asegurado_cert']) || !is_array($allInputs['asegurado_cert']) ){
    		$arrData['message'] = 'No se ingresó correctamente al asegurado. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	} 
    	// campo proveedor vacio
    	if( empty($allInputs['proveedor']) ){
    		$arrData['message'] = 'No se ingresó correctamente el proveedor. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	// campo producto vacio
    	if( empty($allInputs['producto']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el producto. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	$this->db->trans_start();
		if($this->model_cita->m_registrar($allInputs)) { // registro de cita 
			$arrData['message'] = 'Se registraron los datos correctamente';
			$arrData['flag'] = 1;
		}
		$this->db->trans_complete();
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
	public function mover_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['flag'] = 0;
		$arrData['message'] = 'Ha ocurrido un error actualizando la cita';

		$cita = $this->model_cita->m_obtener_esta_cita($allInputs['event']['id']);
		$nuevaFecha = date('Y-m-d',strtotime($allInputs['event']['start']));
		$interval = $allInputs['event']['hora_hasta'] - $allInputs['event']['hora_desde'];
		$nuevaHoraInicio = strtotime($allInputs['event']['start']);
		$nuevaHoraFin = $nuevaHoraInicio + $interval;
		//print_r($nuevaHoraInicio . ' - ' . $nuevaHoraFin);
		$allInputs['datos'] = array(
			'idcita' => $allInputs['event']['id'],
			'hora_desde' => Date('H:i:s',$nuevaHoraInicio),
			'hora_hasta' => Date('H:i:s',$nuevaHoraFin),
			'fecha' => $nuevaFecha
		);
		$this->db->trans_start();
		if($this->model_cita->m_mover_cita($allInputs['datos'])){
			if($cita['es_atencion'] == 1){
				// $datos = array(
				// 	'fecha' => $nuevaFecha,
				// 	'idatencion' => $cita['idatencion']
				// );
				// if($this->model_consulta->m_act_fecha_atencion($datos)){
				// 	$arrData['flag'] = 1;
				// 	$arrData['message'] = 'Consulta actualizada.';
				// }
			}else{
				$arrData['flag'] = 1;
				$arrData['message'] = 'Cita actualizada.';
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
		// print_r($allInputs); exit();
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES 
    	// campo proveedor vacio
    	if( empty($allInputs['proveedor']) ){
    		$arrData['message'] = 'No se ingresó correctamente el proveedor. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	// campo producto vacio
    	if( empty($allInputs['producto']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el producto. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	$this->db->trans_start();
		if($this->model_cita->m_editar($allInputs)) { // edición de cita 
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
		if( $this->model_cita->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}