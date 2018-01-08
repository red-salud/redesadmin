<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CitaSeguimiento extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('security','imagen_helper','otros_helper','fechas_helper','config'));
		$this->load->model(array('model_cita_seguimiento','model_configuracion'));
		//cache
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7));
		date_default_timezone_set("America/Lima");
		//if(!@$this->user) redirect ('inicio/login');
		//$permisos = cargar_permisos_del_usuario($this->user->idusuario);
	}
	public function listar_seguimiento_citas() 
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_cita_seguimiento->m_cargar_seguimiento_de_cita($allInputs); 
		$arrListado = array();
		foreach ($lista as $row) { 
			$arrEstadoSeg = array();
			if( $row['estado_cs'] == 1 ){ // habilitado 
				$arrEstadoSeg['valor'] = $row['estado_cs'];
				$arrEstadoSeg['clase'] = 'success';
			}
			if( $row['estado_cs'] == 0 ){ // anulado 
				$arrEstadoSeg['valor'] = $row['estado_cs'];
				$arrEstadoSeg['clase'] = 'danger';
			}
			$boolPuedoEliminar = FALSE;
			if( $row['idusuario'] == $this->sessionRS['idusuario'] ){ 
				$boolPuedoEliminar = TRUE;
			}
			array_push($arrListado,
				array( 
					'idcita' => $row['idcita'],
					'idcitaseguimiento' => $row['idcitaseguimiento'],
					'fecha' => formatoFechaReporte($row['fecha_registro']),
					'contenido' => $row['contenido'],
					'nombre_empleado' => strtoupper($row['nombres_col']),
					'empleado' => strtoupper($row['nombres_col'].' '.$row['ap_paterno_col'].' '.$row['ap_materno_col']),
					'estado_seguimiento' => $arrEstadoSeg,
					'puedo_eliminar' => $boolPuedoEliminar  
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
	private function envio_correo_cita_seguimiento($fData)
	{ 
		// MANDAR CORREO A LOS INVOLUCRADOS 
		$this->load->library('My_PHPMailer'); 
		$fConfig = obtener_parametros_configuracion();
		$hoydate = date("Y-m-d H:i:s"); 
		date_default_timezone_set('UTC'); 
		define('SMTP_HOST',$fConfig['smtp_host_notif']); // smtpout.secureserver.net 
		define('SMTP_PORT',$fConfig['smtp_port_notif']); // 465
		define('SMTP_USERNAME',$fConfig['email_notif']); // lluna@red-salud.com
		define('SMTP_PASSWORD',$fConfig['clave_notif']); // 
		define('SMTP_SECURE',$fConfig['smtp_secure_notif']); // SSL 
		$setFromAleas = 'NOTIFICACIONES - RED SALUD'; 
		$mail = new PHPMailer(); 
		$mail->IsSMTP(true);
		$mail->SMTPDebug = 2;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = SMTP_SECURE;
		$mail->Host = SMTP_HOST;
		$mail->Port = SMTP_PORT;
		$mail->Username =  SMTP_USERNAME;
		$mail->Password = SMTP_PASSWORD;
		$mail->SetFrom(SMTP_USERNAME,$setFromAleas);
		$mail->AddReplyTo(SMTP_USERNAME,$setFromAleas);
		$mail->Subject = $this->sessionRS['nombres_col'].' '.$this->sessionRS['ap_paterno_col'].' TIENE ALGO QUE DECIRTE';

		$cuerpo = '<html> 
			<style>

			</style>
			<head>
			  <title> NUEVO COMENTARIO SOBRE LA CITA DEL AFILIADO: '.$fData['asegurado'].' </title> 
			</head>
			<body style="font-family: sans-serif;padding: 10px 40px;" > 
			<div style="text-align: right;">
				<img style="width: 160px;" alt="Red Salud" src="'.base_url('assets/dinamic/empresa/'.$fConfig['logo_general_empresa']).'"> 
			</div> <br />';
		$cuerpo .= '<h2> SEGUIMIENTO AL AFILIADO '.$fData['asegurado'].' </h2> <br />'; 
		$cuerpo .= '<div style="font-size:16px;"> 
				Estimado(a): <br /> <br />'; 
		$cuerpo .= 'Se ha agregado un nuevo comentario en la cita del afiliado <u>'.strtoupper($fData['asegurado']).'.</u> <br /> <br />'; 
		$cuerpo .= '<div style="background-color: #e8e8e8;font-style: italic;margin-top: 20px;padding: 16px;"> "'.$fData['contenido'].'" </div>'; 
		$cuerpo .= '<a target="_blank" href="'.base_url().'"> RESPONDER AL COMENTARIO </a>'; 
		$cuerpo .= '<br /> <br />  
			<span style="font-size: 12px; color: #9c9c9c;float:right; ">ATTE: <br /> 
						ENVÍO AUTOMÁTICO DE CORREO GENERADO POR EL AREA DE TECNOLOGÍAS DE LA INFORMACIÓN.  </span>
		</div>';
		// $cuerpo .= '<div style="width: 100%; display: block; font-size: 14px; text-align: right; line-height: 5; color: #a9b9c1;"> Atte: Área de Sistemas y Desarrollo </div>';
		$cuerpo .= '</body></html>';
		$mail->AltBody = $cuerpo;
		$mail->MsgHTML($cuerpo);
		$mail->AddAddress( 'lluna@red-salud.com', ' NOTIFICACIONES' ); 
		// $mail->AddAddress( 'pvasquez@red-salud.com', ' NOTIFICACIONES' ); 
		// $mail->AddAddress( 'aluna@red-salud.com', ' NOTIFICACIONES' ); 
		// $mail->AddAddress( 'atord@red-salud.com', ' NOTIFICACIONES' ); 
		// Activo condificación utf-8
		$mail->CharSet = 'UTF-8';
		// echo $cuerpo; 
		if( $mail->Send() ){ 
			return true;
		}else{
			return false;
		}
	}
	public function registrar()
	{ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente'; 
    	$arrData['flag'] = 0; 
    	if( empty($allInputs['contenido']) ){ 
    		$arrData['message'] = 'No se ingresó los datos requeridos. Corrija y vuelva a intentarlo';  // me quede en la validacion, falta probar. 
			$arrData['flag'] = 0;
			$this->output
			    ->set_content_type('application/json') 
			    ->set_output(json_encode($arrData));
			return;
    	} 
		$this->db->trans_start(); 
		if($this->model_cita_seguimiento->m_registrar($allInputs)) { 
			$arrData['message'] = '- Se registraron los datos correctamente. <br />'; 
			$arrData['flag'] = 1; 
			$arrParams = array( 
				'idcitaseguimiento' => GetLastId('idcitaseguimiento','cita_seguimiento') 
			);
			$fCitaSeguimiento = $this->model_cita_seguimiento->m_obtener_esta_cita_seguimiento($arrParams); 
			if( $this->envio_correo_cita_seguimiento($fCitaSeguimiento) ){ 
				$arrData['message'] .= '- Se envió una alerta a los correos electrónicos registrados. <br />'; 
			}else{
				$arrData['message'] .= '- No se pudo mandar el correo. Ver la configuración. <br />'; 
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
		if( $this->model_cita_seguimiento->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}