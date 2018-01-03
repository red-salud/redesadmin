<?php
class Model_contacto_proveedor extends CI_Model {
	public function __construct()
	{
		parent::__construct();
	}

	public function m_cargar_contacto_proveedor($paramPaginate,$paramDatos=FALSE){
		$this->db->select("cpr.idcontactoproveedor, cpr.nombres_cp, cpr.apellidos_cp, cpr.telefono_fijo_cp, cpr.telefono_movil_cp, cpr.email_cp, cpr.anexo_cp, cpr.envio_correo_cita ,cco.idcargocontacto, cco.descripcion_ctc");
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr, tpr.idtipoproveedor, tpr.descripcion_tpr');
		$this->db->from('contacto_proveedor cpr');
		$this->db->join('proveedor pr', 'cpr.idproveedor = pr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor','left');
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		
		if(!empty($paramDatos['proveedor'])){ 
			$this->db->where('pr.idproveedor', $paramDatos['proveedor']['id']);
		}
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}

		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}

	public function m_count_contacto_proveedor($paramPaginate,$paramDatos=FALSE){ 
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('contacto_proveedor cpr');
		$this->db->join('proveedor pr', 'cpr.idproveedor = pr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor','left');
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		
		if(!empty($paramDatos['proveedor'])){ 
			$this->db->where('pr.idproveedor', $paramDatos['proveedor']['id']);
		} 
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key ,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}

	public function m_cargar_contacto_este_proveedor($paramPaginate,$paramDatos){
		$this->db->select("cpr.idcontactoproveedor, cpr.nombres_cp, cpr.apellidos_cp, cpr.telefono_fijo_cp, cpr.telefono_movil_cp, cpr.email_cp, cpr.anexo_cp, cpr.envio_correo_cita, cco.idcargocontacto, cco.descripcion_ctc");
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr');
		$this->db->from('proveedor pr');
		$this->db->join('contacto_proveedor cpr', 'pr.idproveedor = cpr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where('pr.idproveedor', $paramDatos['idproveedor']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
				}
			}
		}
		if( $paramPaginate['sortName'] ){
			$this->db->order_by($paramPaginate['sortName'], $paramPaginate['sort']);
		}
		if( $paramPaginate['firstRow'] || $paramPaginate['pageSize'] ){
			$this->db->limit($paramPaginate['pageSize'],$paramPaginate['firstRow'] );
		}
		return $this->db->get()->result_array();
	}

	public function m_count_contacto_este_proveedor($paramPaginate,$paramDatos){
		$this->db->select('COUNT(*) AS contador');
		$this->db->from('proveedor pr');
		$this->db->join('contacto_proveedor cpr', 'pr.idproveedor = cpr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where('pr.idproveedor', $paramDatos['idproveedor']);
		if( isset($paramPaginate['search'] ) && $paramPaginate['search'] ){
			foreach ($paramPaginate['searchColumn'] as $key => $value) {
				if(! empty($value)){
					$this->db->like($key,strtoupper_total($value) ,FALSE);
				}
			}
		}
		$fData = $this->db->get()->row_array();
		return $fData;
	}
	public function m_cargar_contactos_para_correo_de_este_proveedor($datos) 
	{
		$this->db->select('CONCAT(cpr.nombres_cp, " ", cpr.apellidos_cp) AS contacto',FALSE);
		$this->db->select("cpr.idcontactoproveedor, cpr.nombres_cp, cpr.apellidos_cp, cpr.telefono_fijo_cp, cpr.telefono_movil_cp, cpr.email_cp, cpr.anexo_cp, cpr.envio_correo_cita, cco.idcargocontacto, cco.descripcion_ctc");
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr');
		$this->db->from('contacto_proveedor cpr');
		$this->db->join('proveedor pr', 'cpr.idproveedor = pr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where('pr.idproveedor', $datos['idproveedor']);
		$this->db->where('cpr.envio_correo_cita', 1); // solo para correo 
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		$this->db->order_by('cpr.apellidos_cp');
		return $this->db->get()->result_array();
	}
	public function m_cargar_contacto_proveedor_limite($datos)
	{
		$this->db->select('CONCAT(cpr.nombres_cp, " ", cpr.apellidos_cp) AS contacto',FALSE);
		$this->db->select("cpr.idcontactoproveedor, cpr.nombres_cp, cpr.apellidos_cp, cpr.telefono_fijo_cp, cpr.telefono_movil_cp, cpr.email_cp, cpr.anexo_cp, cpr.envio_correo_cita, cco.idcargocontacto, cco.descripcion_ctc");
		$this->db->select('pr.idproveedor, pr.nombre_comercial_pr, pr.razon_social_pr, pr.numero_documento_pr, pr.direccion_pr, tpr.idtipoproveedor, tpr.descripcion_tpr');
		$this->db->from('contacto_proveedor cpr');
		$this->db->join('proveedor pr', 'cpr.idproveedor = pr.idproveedor');
		$this->db->join('cargo_contacto cco', 'cpr.idcargocontacto = cco.idcargocontacto','left');
		$this->db->join('tipo_proveedor tpr', 'pr.idtipoproveedor = tpr.idtipoproveedor','left');
		$this->db->where('cpr.estado_cp', 1);
		$this->db->where_in('pr.estado_pr', array(1,2,3));
		
		$this->db->like('CONCAT(cpr.nombres_cp, " ", cpr.apellidos_cp)', $datos['searchText']);
		$this->db->order_by('cpr.apellidos_cp');
		$this->db->limit($datos['limite']);
		return $this->db->get()->result_array();
	}
	// VALIDACIONES 
	
	// CRUD 
	public function m_registrar($datos)
	{
		$data = array( 
			'idproveedor' => $datos['idproveedor'], 
			'nombres_cp' => $datos['nombres'], 
			'apellidos_cp' => empty($datos['apellidos']) ? NULL : $datos['apellidos'], 
			'idcargocontacto' => empty($datos['cargo_contacto']['id']) ? NULL : $datos['cargo_contacto']['id'], 
			'telefono_fijo_cp' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'], 
			'telefono_movil_cp' => empty($datos['telefono_movil']) ? NULL : $datos['telefono_movil'], 
			'email_cp' => empty($datos['email']) ? NULL : $datos['email'], 
			'anexo_cp' => empty($datos['anexo']) ? NULL : $datos['anexo'], 
			'envio_correo_cita' => empty($datos['chk_envio_correo']) ? 2 : 1, 
			'createdat' => date('Y-m-d H:i:s'), 
			'updatedat' => date('Y-m-d H:i:s') 
		);
		return $this->db->insert('contacto_proveedor', $data);
	}	
	public function m_editar($datos){ 
		$data = array( 
			'nombres_cp' => $datos['nombres'],
			'apellidos_cp' => empty($datos['apellidos']) ? NULL : $datos['apellidos'],
			'idcargocontacto' => empty($datos['cargo_contacto']['id']) ? NULL : $datos['cargo_contacto']['id'],
			'telefono_fijo_cp' => empty($datos['telefono_fijo']) ? NULL : $datos['telefono_fijo'],
			'telefono_movil_cp' => empty($datos['telefono_movil']) ? NULL : $datos['telefono_movil'], 
			'email_cp' => empty($datos['email']) ? NULL : $datos['email'], 
			'anexo_cp' => empty($datos['anexo']) ? NULL : $datos['anexo'], 
			'envio_correo_cita' => empty($datos['chk_envio_correo']) ? 2 : 1, 
			'updatedat' => date('Y-m-d H:i:s')
		);
		$this->db->where('idcontactoproveedor',$datos['idcontactoproveedor']);
		return $this->db->update('contacto_proveedor', $data);
	} 
	public function m_anular($datos)
	{
		$data = array( 
			'estado_cp' => 0,
			'updatedat' => date('Y-m-d H:i:s') 
		); 
		$this->db->where('idcontactoproveedor',$datos['idcontactoproveedor']);
		return $this->db->update('contacto_proveedor', $data);
	} 
}
?>