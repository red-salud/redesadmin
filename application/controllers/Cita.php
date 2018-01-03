<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cita extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper'));
		$this->load->model(array('model_cita','model_siniestro','model_historia','model_proveedor','model_contacto_proveedor'));
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
			if( $row['estado_cita'] == 1 ){ // por confirmar 
				$clases = 'b-l b-2x bg-warning bg text-dark ';
			}
			if( $row['estado_cita'] == 2 ){ // confirmado 
				$clases = 'b-l b-2x bg-primary bg ';
			}
			if( $row['estado_cita'] == 3 ){ // atención  
				$clases = 'b-l b-2x bg-success bg ';
			}
			// ESTADO CITA 
			$rowDescripcion = '';
			if( $row['estado_cita'] == 1 ){ // por confirmar 
				$rowDescripcion = 'POR CONFIRMAR';
			}
			if( $row['estado_cita'] == 2 ){ // confirmado 
				$rowDescripcion = 'CONFIRMADO';
			}
			if( $row['estado_cita'] == 3 ){ // atención 
				$rowDescripcion = 'ATENCIÓN';
			}
			$className = array($clases); 
			array_push($arrListado,
				array(
					'id' => $row['idcita'],
					'hora_desde_sql' => $row['hora_cita_inicio'],
					'hora_hasta_sql' => $row['hora_cita_fin'],
					'hora_desde' => strtotime($row['hora_cita_inicio']),
					'hora_hasta' => strtotime($row['hora_cita_fin']),
					'estado_cita' => array(
						'id'=> $row['estado_cita'],
						'descripcion'=> $rowDescripcion
					),
					'idestadocitafijo' => $row['estado_cita'],
					'fecha' => $row['fecha_cita'],
					//'asegurado_cert' => $row['asegurado'].' - '.$row['nombre_plan'], 
					'asegurado_cert'=> array( 
						'idasegurado' => $row['aseg_id'],
						'asegurado' => $row['asegurado'],
						'idhistoria' => $row['idhistoria'],
						'idcertificado' => $row['cert_id'] 
					),
					'asegurado' => array(
						'idasegurado' => $row['aseg_id'],
						'asegurado' => $row['asegurado'],
						'num_documento' => $row['aseg_numDoc'] 
					),
					'especialidad' => array(
						'idespecialidad' => $row['idespecialidad'],
						'especialidad' => strtoupper($row['nombre_esp']),
					),
					'proveedor' => array(
						'id' => $row['idproveedor'],
						'descripcion' => $row['nombre_comercial_pr'],
						'num_documento' => $row['numero_documento_pr'] 
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
					'observaciones' => $row['observaciones_cita'],
					'siniestro' => array( 
						'idsiniestro' => (int)$row['idsiniestro'], 
						'fecha_atencion' => $row['fecha_atencion']
					),
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
	public function listar_esta_cita_calendario($idcita=FALSE)
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		if( $idcita ){
			$allInputs['idcita'] = $idcita; 
		}
		//print_r($idcita); 
		
		$fCita = $this->model_cita->m_cargar_esta_cita($allInputs); 
		$arrListado = array();
		// foreach ($lista as $row) { 
		if( $fCita['estado_cita'] == 1 ){ // por confirmar 
			$clases = 'b-l b-2x bg-warning bg text-dark ';
		}
		if( $fCita['estado_cita'] == 2 ){ // confirmado 
			$clases = 'b-l b-2x bg-primary bg ';
		}
		if( $fCita['estado_cita'] == 3 ){ // atención  
			$clases = 'b-l b-2x bg-success bg ';
		}
		// ESTADO CITA 
		$rowDescripcion = '';
		if( $fCita['estado_cita'] == 1 ){ // por confirmar 
			$rowDescripcion = 'POR CONFIRMAR';
		}
		if( $fCita['estado_cita'] == 2 ){ // confirmado 
			$rowDescripcion = 'CONFIRMADO';
		}
		if( $fCita['estado_cita'] == 3 ){ // atención 
			$rowDescripcion = 'ATENCIÓN';
		}
		$className = array($clases); 
		$fCitaRow = array( 
			'id' => $fCita['idcita'],
			'hora_desde_sql' => $fCita['hora_cita_inicio'],
			'hora_hasta_sql' => $fCita['hora_cita_fin'],
			'hora_desde' => strtotime($fCita['hora_cita_inicio']),
			'hora_hasta' => strtotime($fCita['hora_cita_fin']),
			'estado_cita' => array(
				'id'=> $fCita['estado_cita'],
				'descripcion'=> $rowDescripcion
			),
			'idestadocitafijo' => $fCita['estado_cita'],
			'fecha' => $fCita['fecha_cita'],
			//'asegurado_cert' => $fCita['asegurado'].' - '.$fCita['nombre_plan'], 
			'asegurado_cert'=> array( 
				'idasegurado' => $fCita['aseg_id'],
				'asegurado' => $fCita['asegurado'],
				'idhistoria' => $fCita['idhistoria'],
				'idcertificado' => $fCita['cert_id'] 
			),
			'asegurado' => array(
				'idasegurado' => $fCita['aseg_id'],
				'asegurado' => $fCita['asegurado'],
				'num_documento' => $fCita['aseg_numDoc'] 
			),
			'especialidad' => array(
				'idespecialidad' => $fCita['idespecialidad'],
				'especialidad' => strtoupper($fCita['nombre_esp']),
			),
			'proveedor' => array(
				'id' => $fCita['idproveedor'],
				'descripcion' => $fCita['nombre_comercial_pr'],
				'num_documento' => $fCita['numero_documento_pr'] 
			), 
			'producto' => array(
				'id' => $fCita['idproducto'],
				'descripcion' => strtoupper($fCita['descripcion_prod']) 
			),
			'plan' => array(
				'id' => $fCita['idplan'],
				'descripcion' => $fCita['nombre_plan'] 
			),
			'tipoproducto' => array(
				'idtipoproducto' => $fCita['idtipoproducto'],
				'tipo_producto' => $fCita['descripcion_tp'],
			),
			'observaciones' => $fCita['observaciones_cita'],
			'siniestro' => array( 
				'idsiniestro' => (int)$fCita['idsiniestro'], 
				'fecha_atencion' => $fCita['fecha_atencion']
			)/*,
			'className' => $className,
			'start' => $row['fecha_cita'] .' '. $row['hora_cita_inicio'],
			'end' => $row['fecha_cita'] .' '. $row['hora_cita_fin'],
			'title' => $row['asegurado'] .' - '. $row['nombre_comercial_pr'],
			'allDay' => FALSE,
			'durationEditable' => FALSE,*/
		);
		// } 
    	$arrData['datos']['row'] = $fCitaRow;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($lista)){
			$arrData['flag'] = 0;
		}
		if( $idcita ){
			return $fCitaRow; 
		}else{
			$this->output 
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
		}
		
	}
	public function obtener_configuracion_correo_cita()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrParams = array( 
			'num_documento' => $allInputs['proveedor']['num_documento'] 
		);
		$fProveedor = $this->model_proveedor->m_buscar_este_proveedor($arrParams); 
		$arrParams['idproveedor'] = $fProveedor['idproveedor'];  // idproveedor

		$listaContactosProv = $this->model_contacto_proveedor->m_cargar_contactos_para_correo_de_este_proveedor($arrParams); 
		$arrInfoData = array( 
			'idproveedor' => $fProveedor['idproveedor'],
			'num_documento' => $fProveedor['numero_documento_pr'],
			'nombre_comercial_pr' => $fProveedor['numero_documento_pr'],
			'correo_laboral' => $this->sessionRS['correo_laboral'],
			'titulo_solicitud'=> 'SOLICITUD DE ATENCIÓN MÉDICA',
			'titulo_confirmacion'=> 'CONFIRMACIÓN DE ATENCIÓN MÉDICA',
			'cuerpo_solicitud' => NULL,
			'cuerpo_confirmacion' => NULL,
			'contactos_comma' => NULL,
			'contactos' => array() 
		); 
		$arrSoloCorreo = array();
		foreach ($listaContactosProv as $key => $row) { 
			$tempFila = array(
				'idcontactoproveedor' => $row['idcontactoproveedor'],
				'contacto' => $row['nombres_cp'],
				'email' => $row['email_cp'] 
			);
			$arrInfoData['contactos'][$key] = $tempFila; 
			$arrSoloCorreo[] = $row['email_cp']; 
		}
		$arrInfoData['contactos_comma'] = implode(",", $arrSoloCorreo);
		// GENERAR CUERPO 
		$arrInfoData['cuerpo_solicitud'] = '
		Estimado, '.$allInputs['proveedor']['descripcion'].' %0A
		El presente correo es para SOLICITAR cita de atención médica ambulatoria en '.$allInputs['especialidad']['especialidad'].' para el día '.
		$allInputs['fecha'].' a las '.date('H:i a',strtotime($allInputs['hora_desde_sql'])).', para nuestro afiliado '.
		$allInputs['asegurado']['asegurado'].' con DNI: '.trim($allInputs['asegurado']['num_documento']).' del plan '.
		$allInputs['plan']['descripcion'].' %0A%0A
		Me confirma el horario por favor para coordinar con el afiliado.
		%0A%0A
		Saludos cordiales.
		%0A%0A
		Atte: '; 
		
		$arrInfoData['cuerpo_confirmacion'] = '
		Estimado, '.$allInputs['proveedor']['descripcion'].' %0A
		El presente correo es para CONFIRMAR la cita de atención médica ambulatoria en '.$allInputs['especialidad']['especialidad'].' para el día '.
		$allInputs['fecha'].' a las '.date('H:i a',strtotime($allInputs['hora_desde_sql'])).', para nuestro afiliado '.$allInputs['asegurado']['asegurado'].' con DNI: '.
		trim($allInputs['asegurado']['num_documento']).' del plan '.$allInputs['plan']['descripcion'].' %0A%0A
		Comentarle también que la cita ya ha sido notificada al afiliado, en el día y hora antes mencionados.
		%0A%0A
		Saludos cordiales.
		%0A%0A
		Atte: '; 

		$arrData['datos'] = $arrInfoData;
    	$arrData['message'] = '';
    	$arrData['flag'] = 1;
		if(empty($arrInfoData)){
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
	public function ver_popup_envio_correo()
	{
		$this->load->view('cita/popup_envio_correo'); 
	}
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	$arrData['datos'] = array();
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
    	// campo estado vacio 
    	if( empty($allInputs['estado_cita']['id']) ){ 
    		$arrData['message'] = 'No se ingresó correctamente el estado de la cita. Corrija y vuelva a intentarlo. <br />';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	
    	$this->db->trans_start();
		if($this->model_cita->m_registrar($allInputs)) { // registro de cita 
			$allInputs['idcita'] = GetLastId('idcita','cita'); 
			$fCitaAtencion = $this->model_cita->m_validar_cita_en_atencion($allInputs['idcita']); 
			$arrData['message'] = 'Se registraron los datos correctamente. <br />'; 
			$arrData['flag'] = 1; 
			$arrData['datos']['estado_cita'] = $allInputs['estado_cita'];
			if($allInputs['estado_cita']['id'] == 3){ // ATENCIÓN 
				// VALIDAR QUE LA CITA NO ESTÉ ANEXADA A OTRA ATENCIÓN. 
				if( empty($fCitaAtencion['idsiniestro']) ){ 
					// SI NO TIENE HISTORIA 
					if( empty($allInputs['asegurado_cert']['idhistoria']) ){ 
						$arrHistoria = array( 
							'idasegurado' => $allInputs['asegurado_cert']['idasegurado'] 
						);
						if($this->model_historia->m_generar_historia_clinica($arrHistoria)){ 
							$arrData['message'] .= '- Se generó la historia clínica correctamente. <br />'; 
							$allInputs['asegurado_cert']['idhistoria'] = GetLastId('idhistoria','historia'); 
						}
					}
				}
				// GENERAR NUMERO DE ORDEN DE ATENCIÓN 
				$fSiniestroAnt = $this->model_siniestro->m_cargar_ultimo_siniestro(); 
				$tempOrdenAtencion = substr($fSiniestroAnt['num_orden_atencion'], 0, 6); 
				$tempOrdenAtencionParse = (int)$tempOrdenAtencion; 
				$tempOrdenAtencionParse += 1; 
				$allInputs['num_orden_atencion'] = str_pad($tempOrdenAtencionParse, 6, '0', STR_PAD_LEFT); 
				// VALIDAR QUE LA CITA NO ESTÉ ANEXADA A OTRA ATENCIÓN. 
				if( empty($fCitaAtencion['idsiniestro']) ){ 
					if($this->model_siniestro->m_aperturar_atencion($allInputs)) { 
						$arrData['message'] .= '- Se aperturó la atención correctamente. <br />'; 
						$arrData['datos']['num_orden_atencion'] = $allInputs['num_orden_atencion']; 
					}
				}else{
					$arrData['message'] .= '- Se encontró una atención asociada a la cita. <br />';
				} 
			}else{
				$fCita = $this->listar_esta_cita_calendario($allInputs['idcita']); 
				$arrData['datos']['row'] = $fCita;
			}
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
		//exit();
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
    	// campo estado vacio
    	if( empty($allInputs['estado_cita']['id']) ){
    		$arrData['message'] = 'No se ingresó correctamente el estado de la cita. Corrija y vuelva a intentarlo';
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json')
			    ->set_output(json_encode($arrData));
			return;
    	}
    	if($allInputs['idestadocitafijo'] == 3){
	    	// validar que no pueda cambiar el estado cuando esté atendido. 
	    	if( !($allInputs['estado_cita']['id'] == $allInputs['idestadocitafijo']) ){ 
	    		$arrData['message'] = 'No se puede revertir la atención. Corrija o anule y vuelva a intentarlo. <br />';
				$arrData['flag'] = 0;
				$this->output
				    ->set_content_type('application/json')
				    ->set_output(json_encode($arrData));
				return;
	    	}
	    }
    	$this->db->trans_start();
		if($this->model_cita->m_editar($allInputs)) { // edición de cita 
			$fCitaAtencion = $this->model_cita->m_validar_cita_en_atencion($allInputs['id']); // idcita 
			$arrData['message'] = 'Se editaron los datos correctamente. <br />';
			$arrData['flag'] = 1;
			$arrData['datos']['estado_cita'] = $allInputs['estado_cita'];
			if($allInputs['estado_cita']['id'] == 3){ // ATENCIÓN 
				// VALIDAR QUE LA CITA NO ESTÉ ANEXADA A OTRA ATENCIÓN. 
				$hayCitaConAtencion = FALSE;
				if( empty($fCitaAtencion['idsiniestro']) ){ 
					// SI NO TIENE HISTORIA 
					if( empty($allInputs['asegurado_cert']['idhistoria']) ){ 
						$arrHistoria = array(
							'idasegurado' => $allInputs['asegurado_cert']['idasegurado'] 
						);
						if($this->model_historia->m_generar_historia_clinica($arrHistoria)){ 
							$arrData['message'] .= '- Se generó la historia clínica correctamente. <br />';
							$allInputs['asegurado_cert']['idhistoria'] = GetLastId('idhistoria','historia'); 
						}
					}
				}else{
					$hayCitaConAtencion = TRUE;
				}
				// GENERAR NUMERO DE ORDEN DE ATENCIÓN 
				$fSiniestroAnt = $this->model_siniestro->m_cargar_ultimo_siniestro(); 
				$tempOrdenAtencion = substr($fSiniestroAnt['num_orden_atencion'], 0, 6); 
				$tempOrdenAtencionParse = (int)$tempOrdenAtencion; 
				$tempOrdenAtencionParse += 1; 
				$allInputs['num_orden_atencion'] = str_pad($tempOrdenAtencionParse, 6, '0', STR_PAD_LEFT); 
				$allInputs['idcita'] = $allInputs['id'];
				// VALIDAR QUE LA CITA NO ESTÉ ANEXADA A OTRA ATENCIÓN. 
				if( empty($fCitaAtencion['idsiniestro']) ){ 
					if($this->model_siniestro->m_aperturar_atencion($allInputs)) { 
						$arrData['message'] .= '- Se aperturó la atención correctamente. <br />'; 
						$arrData['datos']['num_orden_atencion'] = $allInputs['num_orden_atencion']; 
					}
				}else{
					$hayCitaConAtencion = TRUE; 
				} 
				if( $hayCitaConAtencion ){ 
					$arrData['message'] .= '- Se encontró una atención asociada a la cita. <br />';
				}
			}
			// APARECE VENTANA DE CORREO 
			if($allInputs['estado_cita']['id'] == 2){ // CONFIRMACION 
				$fCita = $this->listar_esta_cita_calendario($allInputs['id']); 
				$arrData['datos']['row'] = $fCita; 
			}
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