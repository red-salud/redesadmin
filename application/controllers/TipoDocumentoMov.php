<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TipoDocumentoMov extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        // Se le asigna a la informacion a la variable $sessionVP.
        // $this->sessionVP = @$this->session->userdata('sess_vp_'.substr(base_url(),-20,7)); 
        $this->sessionRS = @$this->session->userdata('sess_reds_'.substr(base_url(),-20,7)); 
        $this->load->helper(array('fechas','otros')); 
        $this->load->model(array('model_tipo_documento_mov','model_tipo_documento_serie','model_serie')); 
    }
    public function listar_tipo_documento_mov(){ 
		$allInputs = json_decode(trim($this->input->raw_input_stream),true); 
		$lista = $this->model_tipo_documento_mov->m_cargar_tipo_documento_grilla();
		$listaSeries = $this->model_serie->m_cargar_series();
		$arrListado = array();
		$arrGroupBy = array(); 
		foreach ($lista as $key => $row) { 
			$arrAux = array(
				'idtipodocumentomov' => $row['idtipodocumentomov'],
				'idempresa' => $row['idempresaadmin'],
				'tipo_documento' => $row['descripcion_tdm'],
				'abreviatura' => $row['abreviatura_tdm'],
				'porcentaje' => $row['porcentaje_imp']
			);
			$arrGroupBy[$row['idtipodocumentomov']] = $arrAux; 
		} 
		foreach ($arrGroupBy as $key => $row) { 
			foreach ($listaSeries as $keySe => $rowSe) {
				$arrGroupBy[$key][$rowSe['numero_serie']] = 0;
			}
		}
		foreach ($arrGroupBy as $key => $row) { 
			foreach ($lista as $keyDet => $rowDet) { 
				if( $rowDet['idtipodocumentomov'] == $row['idtipodocumentomov'] ){ 
					if( !empty($rowDet['numero_serie']) ){
						$arrGroupBy[$key][$rowDet['numero_serie']] = (int)$rowDet['correlativo_actual'];
					} 
				}
			}
		}
		$arrListado = array_values($arrGroupBy);
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
	public function ver_popup_formulario()
	{
		$this->load->view('tipo-documento-mov/mant_tipoDocumentoMov');
	}
	public function listar_tipo_documento_mov_cbo(){
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$lista = $this->model_tipo_documento_mov->m_cargar_tipo_documento_mov_cbo();
		$arrListado = array();
		foreach ($lista as $row) {
			array_push($arrListado,
				array(
					'id' => $row['idtipodocumentomov'],
					'key' => $row['key_tdm'],
					'porcentaje' => $row['porcentaje_imp'],
					'descripcion' => strtoupper($row['descripcion_tdm']) 
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
	public function registrar()
	{
		$allInputs = json_decode(trim($this->input->raw_input_stream),true);
		$arrData['message'] = 'Error al registrar los datos, inténtelo nuevamente';
    	$arrData['flag'] = 0;
    	// VALIDACIONES    	
    	$this->db->trans_start();
		if($this->model_tipo_documento_mov->m_registrar($allInputs)) { // registro
			$arrData['idtipodocumentomov'] = GetLastId('idtipodocumentomov','tipo_documento_mov');
			$listaSeries = $this->model_serie->m_cargar_series();
			foreach ($listaSeries as $key => $value) {
				$arrData['idserie'] = $value['idserie'];
				if( $this->model_tipo_documento_serie->m_registrar_tipo_documento_serie($arrData) ){ 
				} 
			}
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
		if($this->model_tipo_documento_mov->m_editar($allInputs)) { // edicion de elemento
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
		if( $this->model_tipo_documento_mov->m_anular($allInputs) ){ 
			$arrData['message'] = 'Se anularon los datos correctamente';
    		$arrData['flag'] = 1;
		}
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($arrData));
	}
}