app.controller('ProveedorCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
	'ProveedorFactory',
	'ProveedorServices',
	'TipoProveedorServices', 
	'ContactoProveedorServices', 
	'UbigeoServices',
	'UsuarioServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
	ProveedorFactory,
	ProveedorServices,
	TipoProveedorServices,
	ContactoProveedorServices,
	UbigeoServices,
	UsuarioServices
	) { 
		$scope.metodos = {}; // contiene todas las funciones 
		$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  	$scope.mySelectionGrid = [];
  	var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 10,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
  	};
  	$scope.gridOptions = {
	    rowHeight: 30,
	    paginationPageSizes: [10, 50, 100, 500, 1000],
	    paginationPageSize: 10,
	    useExternalPagination: true,
	    useExternalSorting: true,
	    useExternalFiltering : true,
	    enableGridMenu: true,
	    enableRowSelection: true,
	    enableSelectAll: true,
	    enableFiltering: false,
	    enableFullRowSelection: true,
	    multiSelect: false,
	    columnDefs: [ 
	      { field: 'idproveedor', name: 'pr.idproveedor', displayName: 'ID', width: '60',  sort: { direction: uiGridConstants.DESC} },
	      { field: 'tipo_proveedor', name: 'tpr.descripcion_tpr', type: 'object', displayName: 'Tipo de Proveedor', minWidth: 140,
	      	cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>' 
	      },
	      { field: 'tipo_documento_identidad', name: 'tdi.descripcion_tdi', type: 'object', displayName: 'Tipo de Doc.', minWidth: 120, visible: false, 
	      	cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>' 
	      },
	      { field: 'numero_documento', name: 'pr.numero_documento_pr', displayName: 'N°. Documento', minWidth: 114 },
	      { field: 'razon_social', name: 'pr.razon_social_pr', displayName: 'Razón Social', minWidth: 170 },
	      { field: 'nombre_comercial', name: 'pr.nombre_comercial_pr', displayName: 'Nombre Comercial', minWidth: 180 },
	      { field: 'direccion', name: 'pr.direccion_pr', displayName: 'Dirección', minWidth: 180 },
	      { field: 'departamento', name: 'dpto.descripcion_ubig', displayName: 'Departamento', minWidth: 100, visible: false },
	      { field: 'provincia', name: 'prov.descripcion_ubig', displayName: 'Provincia', minWidth: 100, visible: false },
	      { field: 'distrito', name: 'dist.descripcion_ubig', displayName: 'Distrito', minWidth: 100 },
	      { field: 'estado_obj', name: 'pr.estado_pr', type: 'object', displayName: ' ', minWidth: 90, enableFiltering: false, 
          cellTemplate:'<div class="ui-grid-cell-contents">' + 
            '<label style="box-shadow: 1px 1px 0 black; display: block;font-size: 12px;" class="label {{ COL_FIELD.claseLabel }} "> <i class="{{ COL_FIELD.claseIcon }}"></i> {{ COL_FIELD.labelText }}' + 
            '</label></div>' 
        }
	    ],
	    onRegisterApi: function(gridApi) { 
	      $scope.gridApi = gridApi;
	      gridApi.selection.on.rowSelectionChanged($scope,function(row){
	        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
	      });
	      gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
	        $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
	      });

	      $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
	        //console.log(sortColumns);
	        if (sortColumns.length == 0) {
	          paginationOptions.sort = null;
	          paginationOptions.sortName = null;
	        } else {
	          paginationOptions.sort = sortColumns[0].sort.direction;
	          paginationOptions.sortName = sortColumns[0].name;
	        }
	        $scope.metodos.getPaginationServerSide(true);
	      });
	      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
	        paginationOptions.pageNumber = newPage;
	        paginationOptions.pageSize = pageSize;
	        paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
	        $scope.metodos.getPaginationServerSide(true);
	      });
	      $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
	        var grid = this.grid;
	        paginationOptions.search = true; 
	        paginationOptions.searchColumn = { 
	          'pr.idproveedor' : grid.columns[1].filters[0].term, 
	          'tpr.descripcion_tpr' : grid.columns[2].filters[0].term, 
	          'tdi.descripcion_tdi' : grid.columns[3].filters[0].term, 
	          'pr.numero_documento_pr' : grid.columns[4].filters[0].term, 
	          'pr.razon_social_pr' : grid.columns[5].filters[0].term, 
	          'pr.nombre_comercial_pr' : grid.columns[6].filters[0].term, 
	          'pr.direccion_pr' : grid.columns[7].filters[0].term, 
	          'dpto.descripcion_ubig' : grid.columns[8].filters[0].term, 
	          'prov.descripcion_ubig' : grid.columns[9].filters[0].term, 
	          'dist.descripcion_ubig' : grid.columns[10].filters[0].term, 
	          'pr.estado_pr' : grid.columns[11].filters[0].term 
	        }
	        $scope.metodos.getPaginationServerSide();
	      });
	    }
		};
		paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
	  $scope.btnBuscar = function(){ 
		  $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
		  $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
		};
		$scope.metodos.listaTipoUsuario = function(myCallback) { 
      var myCallback = myCallback || function() { };
      UsuarioServices.sListarTipoUsuarioCbo().then(function(rpta) {
        $scope.fArr.listaTipoUsuario = rpta.datos; 
        myCallback();
      });
    };
		$scope.metodos.listaTipoProveedor = function(myCallback) { 
			var myCallback = myCallback || function() { };
			TipoProveedorServices.sListarCbo().then(function(rpta) {
				$scope.fArr.listaTipoProveedor = rpta.datos; 
				myCallback();
			});
		};
		$scope.metodos.getPaginationServerSide = function(loader) {
		  if( loader ){
		  	blockUI.start('Procesando información...');
		  }
		  var arrParams = {
		    paginate : paginationOptions
		  };
		  ProveedorServices.sListar(arrParams).then(function (rpta) { 
		  	if( rpta.datos.length == 0 ){
		  		rpta.paginate = { totalRows: 0 };
		  	}
		    $scope.gridOptions.totalItems = rpta.paginate.totalRows; 
		    $scope.gridOptions.data = rpta.datos; 
		    if( loader ){
		    	blockUI.stop(); 
		    }
		  });
		  $scope.mySelectionGrid = [];
		};
		$scope.metodos.getPaginationServerSide(true); 
		// MAS ACCIONES 
		$scope.btnNuevo = function() { 
			var arrParams = {
				'metodos': $scope.metodos,
				'fArr': $scope.fArr 
			}
			ProveedorFactory.regProveedorModal(arrParams); 
		}
		$scope.btnEditar = function() { 
			var arrParams = {
				'metodos': $scope.metodos,
				'mySelectionGrid': $scope.mySelectionGrid,
				'fArr': $scope.fArr,
				'fSessionCI': $scope.fSessionCI 
			}
			ProveedorFactory.editProveedorModal(arrParams); 
		}
		$scope.btnContactos = function() { 
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'Proveedor/ver_popup_contactos',
	      size: 'lg',
	      backdrop: 'static',
	      keyboard:false,
	      scope: $scope,
	      controller: function ($scope, $uibModalInstance) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.fContacto = {};
	      	$scope.editClassForm = null;
	      	$scope.tituloBloque = 'Agregar Contacto';
	      	$scope.contBotonesReg = true;
	      	$scope.contBotonesEdit = false;
	      	if( $scope.mySelectionGrid.length == 1 ){ 
	          $scope.fData = $scope.mySelectionGrid[0];
	        }else{
	          alert('Seleccione una sola fila');
	        }
	      	$scope.titleForm = 'Contactos';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	} 
	      	$scope.btnBuscarContactos = function(){
					  $scope.gridOptionsContactos.enableFiltering = !$scope.gridOptionsContactos.enableFiltering;
					  $scope.gridApiContacto.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
					};
	      	var paginationOptionsContactos = { 
			      pageNumber: 1,
			      firstRow: 0,
			      pageSize: 10,
			      sort: uiGridConstants.DESC,
			      sortName: null,
			      search: null
				  };
					$scope.gridOptionsContactos = { 
				    rowHeight: 30,
				    paginationPageSizes: [10, 50, 100, 500, 1000],
				    paginationPageSize: 10,
				    useExternalPagination: true,
				    useExternalSorting: true,
				    useExternalFiltering : true,
				    enableGridMenu: true,
				    enableRowSelection: true,
				    enableSelectAll: true,
				    enableFiltering: false,
				    enableFullRowSelection: true,
				    multiSelect: false,
				    columnDefs: [ 
				      { field: 'idcontacto', name: 'cpr.idcontactoproveedor', displayName: 'ID', visible: false, width: '50',  sort: { direction: uiGridConstants.DESC} },
				      { field: 'contacto', name: 'cpr.nombres', displayName: 'Contacto', width: 140 },
				      { field: 'cargo', name: 'cpr.cargo_cp', displayName: 'Cargo', width: 140 },
				      { field: 'telefono_fijo', name: 'cpr.telefono_fijo_cp', displayName: 'Tel. Fijo', width: 100 },
				      { field: 'anexo', name: 'cpr.anexo_cp', displayName: 'Anexo', width: 75 },
				      { field: 'telefono_movil', name: 'cpr.telefono_movil_cp', displayName: 'Tel. Movil', width: 100 },
				      { field: 'email', name: 'cpr.email_cp', displayName: 'E-mail', width: 120 } 
				    ],
				    onRegisterApi: function(gridApiContacto) { 
				      $scope.gridApiContacto = gridApiContacto;
				      gridApiContacto.selection.on.rowSelectionChanged($scope,function(row){
				        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows(); 
				        // EDICIÓN DE CONTACTO 
					      if( $scope.mySelectionGridContacto.length == 1 ){
					      	$scope.editClassForm = ' edit-form'; 
					      	$scope.tituloBloque = 'Edición de Contacto';
					      	$scope.contBotonesReg = false;
					      	$scope.contBotonesEdit = true;
					      	$scope.fContacto = $scope.mySelectionGridContacto[0];
					      }else{
					      	$scope.editClassForm = null; 
					      	$scope.tituloBloque = 'Agregar Contacto';
					      	$scope.contBotonesReg = true;
					      	$scope.contBotonesEdit = false;
					      	$scope.fContacto = {};
					      }
					      /* END */
				      });
				      gridApiContacto.selection.on.rowSelectionChangedBatch($scope,function(rows){
				        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows();
				      });

				      $scope.gridApiContacto.core.on.sortChanged($scope, function(grid, sortColumns) { 
				        if (sortColumns.length == 0) {
				          paginationOptionsContactos.sort = null;
				          paginationOptionsContactos.sortName = null;
				        } else {
				          paginationOptionsContactos.sort = sortColumns[0].sort.direction;
				          paginationOptionsContactos.sortName = sortColumns[0].name;
				        }
				        $scope.metodos.getPaginationServerSideContactos(true);
				      });
				      gridApiContacto.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
				        paginationOptionsContactos.pageNumber = newPage;
				        paginationOptionsContactos.pageSize = pageSize;
				        paginationOptionsContactos.firstRow = (paginationOptionsContactos.pageNumber - 1) * paginationOptionsContactos.pageSize;
				        $scope.metodos.getPaginationServerSideContactos(true);
				      });
				      $scope.gridApiContacto.core.on.filterChanged( $scope, function(grid, searchColumns) {
				        var grid = this.grid;
				        paginationOptionsContactos.search = true; 
				        paginationOptionsContactos.searchColumn = {
				          'cp.idcontactoproveedor' : grid.columns[1].filters[0].term,
				          'cp.nombres' : grid.columns[2].filters[0].term,
				          'cp.cargo_cp' : grid.columns[3].filters[0].term,
				          'cp.telefono_fijo_cp' : grid.columns[4].filters[0].term,
				          'cp.anexo_cp' : grid.columns[5].filters[0].term,
				          'cp.telefono_movil_cp' : grid.columns[6].filters[0].term,
				          'cp.email_cp' : grid.columns[7].filters[0].term 
				        }
				        $scope.metodos.getPaginationServerSideContactos();
				      }); 
				    }
					};
					$scope.quitarContacto = function() { 
						var pMensaje = '¿Realmente desea anular el registro?';
				      $bootbox.confirm(pMensaje, function(result) {
				        if(result){
				        	var arrParams = {
				        		idcontactoproveedor: $scope.fContacto.idcontactoproveedor 
				        	}
				        	blockUI.start('Procesando información...');
				          ContactoProveedorServices.sAnular(arrParams).then(function (rpta) {
				            if(rpta.flag == 1){
				              var pTitle = 'OK!';
				              var pType = 'success';
				              $scope.metodos.getPaginationServerSideContactos();
				              $scope.editClassForm = null; 
							      	$scope.tituloBloque = 'Agregar Contacto';
							      	$scope.contBotonesReg = true;
							      	$scope.contBotonesEdit = false;
				            }else if(rpta.flag == 0){
				              var pTitle = 'Error!';
				              var pType = 'danger';
				            }else{
				              alert('Error inesperado');
				            }
				            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
				            blockUI.stop(); 
				          });
				        }
				      });
					}
					$scope.actualizarContacto = function() { 
						console.log('click me');
						blockUI.start('Procesando información...'); 
	          ContactoProveedorServices.sEditar($scope.fContacto).then(function (rpta) {
	            if(rpta.flag == 1){
	              var pTitle = 'OK!';
	              var pType = 'success';
	              $scope.fContacto = {};
	              $scope.metodos.getPaginationServerSideContactos(true); 
	              $scope.editClassForm = null; 
				      	$scope.tituloBloque = 'Agregar Contacto';
				      	$scope.contBotonesReg = true;
				      	$scope.contBotonesEdit = false;
	            }else if(rpta.flag == 0){
	              var pTitle = 'Error!';
	              var pType = 'danger';
	            }else{
	              alert('Error inesperado');
	            }
	            blockUI.stop(); 
	            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          });
					}
					paginationOptionsContactos.sortName = $scope.gridOptionsContactos.columnDefs[0].name;
					$scope.metodos.getPaginationServerSideContactos = function(loader) {
					  if( loader ){
					  	blockUI.start('Procesando información...');
					  }
					  var arrParams = { 
					    paginate : paginationOptionsContactos,
					    datos: $scope.fData 
					  };
					  ContactoProveedorServices.sListarContactosDeEsteProveedor(arrParams).then(function (rpta) { 
					    $scope.gridOptionsContactos.totalItems = rpta.paginate.totalRows;
					    $scope.gridOptionsContactos.data = rpta.datos; 
					    if( loader ){
					    	blockUI.stop(); 
					    }
					  });
					  $scope.mySelectionGridContacto = [];
					};
					$scope.metodos.getPaginationServerSideContactos(true); 
	      	$scope.agregarContacto = function () { 
	      		blockUI.start('Procesando información...');
	      		$scope.fContacto.idproveedor = $scope.fData.idproveedor; 
	          ContactoProveedorServices.sRegistrar($scope.fContacto).then(function (rpta) {
	            if(rpta.flag == 1){
	              var pTitle = 'OK!';
	              var pType = 'success';
	              $scope.fContacto = {};
	              $scope.metodos.getPaginationServerSideContactos(true); 
	            }else if(rpta.flag == 0){
	              var pTitle = 'Error!';
	              var pType = 'danger';
	            }else{
	              alert('Error inesperado');
	            }
	            blockUI.stop(); 
	            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          });
	        } 
	      }
	    });
		} 
		$scope.btnAnular = function() { 
	    var pMensaje = '¿Realmente desea anular el registro?';
	    $bootbox.confirm(pMensaje, function(result) {
	      if(result){
	        var arrParams = {
	          idproveedor: $scope.mySelectionGrid[0].idproveedor 
	        };
	        blockUI.start('Procesando información...');
	        ProveedorServices.sAnular(arrParams).then(function (rpta) {
	          if(rpta.flag == 1){
	            var pTitle = 'OK!';
	            var pType = 'success';
	            $scope.metodos.getPaginationServerSide();
	          }else if(rpta.flag == 0){
	            var pTitle = 'Error!';
	            var pType = 'danger';
	          }else{
	            alert('Error inesperado');
	          }
	          pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          blockUI.stop(); 
	        });
	      }
	    });
	  } 
	  $scope.cambiarEstado = function(varEstado) {
	  	var pMensaje = '¿Realmente desea cambiar el estado del registro?';
	    $bootbox.confirm(pMensaje, function(result) {
	      if(result){
	        var arrParams = { 
	          idproveedor: $scope.mySelectionGrid[0].idproveedor,
	          estado: varEstado 
	        };
	        blockUI.start('Procesando información...');
	        ProveedorServices.sCambiarEstado(arrParams).then(function (rpta) {
	          if(rpta.flag == 1){ 
	            var pTitle = 'OK!';
	            var pType = 'success';
	            $scope.metodos.getPaginationServerSide();
	          }else if(rpta.flag == 0){ 
	            var pTitle = 'Error!';
	            var pType = 'danger';
	          }else{ 
	            alert('Error inesperado');
	          }
	          pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          blockUI.stop(); 
	        });
	      }
	    });
	  } 
}]);

app.service("ProveedorServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sListarProveedoresSinUsuario: sListarProveedoresSinUsuario,
        sListarCbo: sListarCbo,
        sBuscarEsteProveedor: sBuscarEsteProveedor,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sCambiarEstado: sCambiarEstado,
        sAnular: sAnular 
    });
    function sListar(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/listar_proveedor",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarProveedoresSinUsuario(datos) { 
    	var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/listar_proveedores_sin_usuario", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCbo(datos) {
    	var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/listar_proveedores_cbo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sBuscarEsteProveedor(datos) {
    	var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/buscar_proveedor_para_formulario", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sCambiarEstado(datos) {
    	var request = $http({ 
            method : "post",
            url : angular.patchURLCI+"Proveedor/cambiar_estado",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Proveedor/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("ProveedorFactory", function($uibModal, $timeout, $bootbox, pinesNotifications, blockUI, ProveedorServices,UbigeoServices) { 
	var interfaz = {
		regProveedorModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'Proveedor/ver_popup_formulario',
	      size: 'lg',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.accion = 'reg';
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr;
	      	$scope.titleForm = 'Registro de Proveedor';
	      	/* contactos */
	      	$scope.fContacto = {};
	      	$scope.editClassForm = null;
	      	$scope.contBotonesReg = true;
	      	$scope.contBotonesEdit = false;

	      	$scope.gridOptionsContactos = { 
				    rowHeight: 30,
				    paginationPageSizes: [100, 500, 1000],
				    paginationPageSize: 100,
				    enableGridMenu: true,
				    enableRowSelection: true,
				    enableSelectAll: true,
				    enableFiltering: false,
				    enableFullRowSelection: true,
				    multiSelect: false,
				    columnDefs: [ 
				      { field: 'idcontacto', displayName: 'ID', visible: false, width: '50' },
				      { field: 'contacto', displayName: 'Contacto', width: 140 },
				      { field: 'cargo', displayName: 'Cargo', width: 140 },
				      { field: 'telefono_fijo', displayName: 'Tel. Fijo', width: 100 },
				      { field: 'anexo', displayName: 'Anexo', width: 75 },
				      { field: 'telefono_movil', displayName: 'Tel. Movil', width: 100 },
				      { field: 'email', displayName: 'E-mail', width: 120 } 
				    ],
				    onRegisterApi: function(gridApiContacto) { 
				      $scope.gridApiContacto = gridApiContacto;
				      gridApiContacto.selection.on.rowSelectionChanged($scope,function(row){ 
				        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows(); 
				        // EDICIÓN DE CONTACTO 
					      if( $scope.mySelectionGridContacto.length == 1 ){
					      	$scope.editClassForm = ' edit-form'; 
					      	$scope.tituloBloque = 'Edición de Contacto';
					      	$scope.contBotonesReg = false;
					      	$scope.contBotonesEdit = true;
					      	$scope.fContacto = $scope.mySelectionGridContacto[0];
					      }else{
					      	$scope.editClassForm = null; 
					      	$scope.tituloBloque = 'Agregar Contacto';
					      	$scope.contBotonesReg = true;
					      	$scope.contBotonesEdit = false;
					      	$scope.fContacto = {};
					      }
					      /* END */
				      });
				      gridApiContacto.selection.on.rowSelectionChangedBatch($scope,function(rows){
				        $scope.mySelectionGridContacto = gridApiContacto.selection.getSelectedRows();
				      });
				    }
					};
					$scope.quitarContacto = function() { 
						var index = $scope.gridOptionsContactos.data.indexOf($scope.mySelectionGridContacto[0]); 
				    $scope.gridOptionsContactos.data.splice(index,1);
				    $scope.fContacto = {};
				    $scope.editClassForm = null; 
				    $scope.contBotonesReg = true;
					  $scope.contBotonesEdit = false;
					}
					$scope.actualizarContacto = function() { 
						var index = $scope.gridOptionsContactos.data.indexOf($scope.mySelectionGridContacto[0]); 
				    $scope.gridOptionsContactos.data.splice(index,1);
				    $scope.arrTemporal = { 
	      			'nombres': $scope.fContacto.nombres,
	      			'apellidos': $scope.fContacto.apellidos,
	      			'cargo': $scope.fContacto.cargo,
	      			'telefono_movil': $scope.fContacto.telefono_movil,
	      			'telefono_fijo': $scope.fContacto.telefono_fijo,
	      			'anexo': $scope.fContacto.anexo,
							'email': $scope.fContacto.email,
							'contacto': $scope.fContacto.nombres + ' ' + $scope.fContacto.apellidos 
				    };
				    if( $scope.gridOptionsContactos.data === null ){
				      $scope.gridOptionsContactos.data = [];
				    }
				    $scope.gridOptionsContactos.data.push($scope.arrTemporal);
				    $scope.fContacto = {}; 
				    $scope.editClassForm = null;
				    $scope.contBotonesReg = true;
					  $scope.contBotonesEdit = false;
					}
					$scope.agregarContacto = function () { 
						if( !($scope.fContacto.nombres) ){ 
				      //$scope.fData.temporal.precio_unitario = null;
				      //$('#temporalPrecioUnit').focus();
				      pinesNotifications.notify({ title: 'Advertencia.', text: 'No hay datos para ingresar', type: 'warning', delay: 2000 });
				      return false;
				    }
	      		$scope.arrTemporal = { 
	      			'nombres': $scope.fContacto.nombres,
	      			'apellidos': $scope.fContacto.apellidos,
	      			'cargo': $scope.fContacto.cargo,
	      			'telefono_movil': $scope.fContacto.telefono_movil,
	      			'telefono_fijo': $scope.fContacto.telefono_fijo,
	      			'anexo': $scope.fContacto.anexo,
							'email': $scope.fContacto.email,
							'contacto': $scope.fContacto.nombres + ' ' + $scope.fContacto.apellidos 
				    };
				    if( $scope.gridOptionsContactos.data === null ){
				      $scope.gridOptionsContactos.data = [];
				    }
				    $scope.contBotonesReg = true;
					  $scope.contBotonesEdit = false;
				    $scope.gridOptionsContactos.data.push($scope.arrTemporal); 
				    $scope.editClassForm = null; 
				    $scope.fContacto = {};
	        } 
	      	$scope.cancel = function () { 
	      		var pMensaje = '¿Realmente desea salir sin guardar la información?'; 
				    $bootbox.confirm(pMensaje, function(result) { 
				      if(result){ 
	      	  		$uibModalInstance.dismiss('cancel');
	      	  	}
	      	  });
	      	}
	      	// TIPO PROVEEDOR 
	      	var myCallBackTP = function() { 
	      		$scope.fArr.listaTipoProveedor.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo de proveedor--'}); 
	      		$scope.fData.tipo_proveedor = $scope.fArr.listaTipoProveedor[0]; 
	      	}
	      	$scope.metodos.listaTipoProveedor(myCallBackTP); // mapProveedor

	      	var myCallBackTS = function() { 
	      		var objIndex = $scope.fArr.listaTipoUsuario.filter(function(obj) { 
	            return obj.key_tu == 'key_proveedor';
	          }).shift(); 
	      		$scope.fData.tipo_usuario = objIndex; 
			    }
			    $scope.metodos.listaTipoUsuario(myCallBackTS); 

	      	// UBIGEO - NUEVO
          //=============================================================
            $scope.getDepartamentoAutocomplete = function (value) {
              var params = {
                search: value,
                sensor: false
              }
              return UbigeoServices.sListarDepartamentoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLD = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLD = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getProvinciaAutocomplete = function (value) {
              var params = {
                search: value,
                id: $scope.fData.iddepartamento,
                sensor: false
              }
              return UbigeoServices.sListarProvinciaPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLP = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLP = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getDistritoAutocomplete = function (value) {
              console.log($scope.fData.idprovincia);
              var params = {
                search: value,
                id_dpto: $scope.fData.iddepartamento,
                id_prov: $scope.fData.idprovincia,
                sensor: false
              }
              return UbigeoServices.sListarDistritoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLDis = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLDis = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getSelectedDepartamento = function ($item, $model, $label) {
                $scope.fData.iddepartamento = $item.id;
                $scope.fData.idprovincia = null;
                $scope.fData.provincia = null;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedProvincia = function ($item, $model, $label) {
                $scope.fData.idprovincia = $item.id;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedDistrito = function ($item, $model, $label) {
              $scope.fData.iddistrito = $item.id;
            };
            $scope.obtenerDepartamentoPorCodigo = function () {
              if( $scope.fData.iddepartamento ){
                var arrData = {
                  'codigo': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddepartamento = rpta.datos.id;
                    $scope.fData.departamento = rpta.datos.descripcion;
                    $('#fDatadepartamento').focus();
                  }
                });

              }
            }
            $scope.obtenerProvinciaPorCodigo = function () {
              if( $scope.fData.idprovincia ){
                var arrData = {
                  'codigo': $scope.fData.idprovincia,
                  'iddepartamento': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarProvinciaDeDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.idprovincia = rpta.datos.id;
                    $scope.fData.provincia = rpta.datos.descripcion;
                    $('#fDataprovincia').focus();
                  }
                });

              }
            }
            $scope.obtenerDistritoPorCodigo = function () {
              if( $scope.fData.iddistrito ){
                var arrData = {
                  'codigo': $scope.fData.iddistrito,
                  'iddepartamento': $scope.fData.iddepartamento,
                  'idprovincia': $scope.fData.idprovincia
                }
                UbigeoServices.sListarDistritosDeProvinciaPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddistrito = rpta.datos.id;
                    $scope.fData.distrito = rpta.datos.descripcion;
                    $('#fDatadistrito').focus();
                  }
                });
              }
            }
            $scope.limpiaDpto = function(){
              $scope.fData.departamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDpto = function(){
              $scope.fData.iddepartamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaProv = function(){
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdProv = function(){
              $scope.fData.idprovincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaDist = function(){
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDist = function(){
              $scope.fData.iddistrito = null;
            }
          $scope.generateMap = function(lat,lng) { 
          	if( $scope.fData.ubicacion ){ 
          		if( $scope.fData.ubicacion.length > 3 ){ 
          			$scope.fData.keyGoogleMap = 'AIzaSyDwJmRpFI43RP39yXUQ3yyNOHrt8bK0AC8';
          			//$scope.fData.keyGoogleMap = 'AIzaSyB2FjjCqepP3ZXx6xFbxEKjijPtcNTCcXM';

		          	UbigeoServices.sListarDireccionCoordenadas($scope.fData).then(function(rpta) { 
		      				if(rpta.status == 'OK'){
		      					// console.log(rpta.results,'rpta.results'); 
		      					$scope.fData.lat = rpta.results[0].geometry.location.lat; 
		      					$scope.fData.lng = rpta.results[0].geometry.location.lng; 
		      					//$scope.fData.
		      					$scope.generateMapExec($scope.fData.lat,$scope.fData.lng,16); 
		      				}
		      			});
		      		}
		      	}else{ 
		      		$scope.generateMapExec(lat,lng);
		      	}
          }
          $scope.generateMapExec = function(lat,lng,zoom) { 
          	var lat = parseFloat(lat) || -12.0463667;
          	var lng = parseFloat(lng) || -77.0427891;
          	var zoom = zoom || 9; 
          	var pointCenter = {lat: lat, lng: lng }; 
		        var mapProveedor = new google.maps.Map(document.getElementById('mapProveedor'), { 
		          zoom: zoom,
		          center: pointCenter
		        });
		        var markerProveedor = new google.maps.Marker({
		          position: pointCenter,
		          map: mapProveedor
		        });
          }
          $timeout(function() { 
          	$scope.generateMap();
          }, 1000);
          $scope.changeTextUbicacion = function() {
          	$scope.fData.lat = '';
          	$scope.fData.lng = '';
          }
	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	      		$scope.fData.contactos = $scope.gridOptionsContactos.data;
	          ProveedorServices.sRegistrar($scope.fData).then(function (rpta) {
	            if(rpta.flag == 1){
	              var pTitle = 'OK!';
	              var pType = 'success';
	              $uibModalInstance.dismiss('cancel');
	              if(typeof $scope.metodos.getPaginationServerSide == 'function'){ 
									$scope.metodos.getPaginationServerSide(true);
	              }
	            }else if(rpta.flag == 0){
	              var pTitle = 'Error!';
	              var pType = 'danger';
	            }else{
	              alert('Error inesperado');
	            }
	            blockUI.stop(); 
	            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          });
	        } 
	      },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
	    });
		},
		editProveedorModal: function (arrParams) {
			blockUI.start('Abriendo formulario...');
			$uibModal.open({ 
	      templateUrl: angular.patchURLCI+'Proveedor/ver_popup_formulario',
	      size: 'lg',
	      backdrop: 'static',
	      keyboard:false,
	      controller: function ($scope, $uibModalInstance, arrParams) { 
	      	blockUI.stop(); 
	      	$scope.fData = {};
	      	$scope.accion = 'edit';
	      	$scope.metodos = arrParams.metodos;
	      	$scope.fArr = arrParams.fArr;
	      	$scope.disabledVendedor = false;
	      	if( arrParams.mySelectionGrid.length == 1 ){ 
	          $scope.fData = arrParams.mySelectionGrid[0];
	        }else{
	          alert('Seleccione una sola fila');
	        }
	      	$scope.titleForm = 'Edición de Proveedor';
	      	$scope.cancel = function () {
	      	  $uibModalInstance.dismiss('cancel');
	      	}
	      	
	      	var myCallBackTP = function() { 
	      		var objIndex = $scope.fArr.listaTipoProveedor.filter(function(obj) { 
	            return obj.id == $scope.fData.tipo_proveedor.id;
	          }).shift(); 
	      		$scope.fData.tipo_proveedor = objIndex; 
	      	}
	      	$scope.metodos.listaTipoProveedor(myCallBackTP); 
	      	// UBIGEO - EDIT 
          //=============================================================
            $scope.getDepartamentoAutocomplete = function (value) {
              var params = {
                search: value,
                sensor: false
              }
              return UbigeoServices.sListarDepartamentoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLD = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLD = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getProvinciaAutocomplete = function (value) {
              var params = {
                search: value,
                id: $scope.fData.iddepartamento,
                sensor: false
              }
              return UbigeoServices.sListarProvinciaPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLP = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLP = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getDistritoAutocomplete = function (value) {
              console.log($scope.fData.idprovincia);
              var params = {
                search: value,
                id_dpto: $scope.fData.iddepartamento,
                id_prov: $scope.fData.idprovincia,
                sensor: false
              }
              return UbigeoServices.sListarDistritoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLDis = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLDis = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getSelectedDepartamento = function ($item, $model, $label) {
                $scope.fData.iddepartamento = $item.id;
                $scope.fData.idprovincia = null;
                $scope.fData.provincia = null;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedProvincia = function ($item, $model, $label) {
                $scope.fData.idprovincia = $item.id;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedDistrito = function ($item, $model, $label) {
              $scope.fData.iddistrito = $item.id;
            };
            $scope.obtenerDepartamentoPorCodigo = function () {
              if( $scope.fData.iddepartamento ){
                var arrData = {
                  'codigo': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddepartamento = rpta.datos.id;
                    $scope.fData.departamento = rpta.datos.descripcion;
                    $('#fDatadepartamento').focus();
                  }
                });

              }
            }
            $scope.obtenerProvinciaPorCodigo = function () {
              if( $scope.fData.idprovincia ){
                var arrData = {
                  'codigo': $scope.fData.idprovincia,
                  'iddepartamento': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarProvinciaDeDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.idprovincia = rpta.datos.id;
                    $scope.fData.provincia = rpta.datos.descripcion;
                    $('#fDataprovincia').focus();
                  }
                });

              }
            }
            $scope.obtenerDistritoPorCodigo = function () {
              if( $scope.fData.iddistrito ){
                var arrData = {
                  'codigo': $scope.fData.iddistrito,
                  'iddepartamento': $scope.fData.iddepartamento,
                  'idprovincia': $scope.fData.idprovincia
                }
                UbigeoServices.sListarDistritosDeProvinciaPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddistrito = rpta.datos.id;
                    $scope.fData.distrito = rpta.datos.descripcion;
                    $('#fDatadistrito').focus();
                  }
                });
              }
            }
            $scope.limpiaDpto = function(){
              $scope.fData.departamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDpto = function(){
              $scope.fData.iddepartamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaProv = function(){
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdProv = function(){
              $scope.fData.idprovincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaDist = function(){
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDist = function(){
              $scope.fData.iddistrito = null;
            }
          // UBIGEO - NUEVO
          //=============================================================
            $scope.getDepartamentoAutocomplete = function (value) {
              var params = {
                search: value,
                sensor: false
              }
              return UbigeoServices.sListarDepartamentoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLD = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLD = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getProvinciaAutocomplete = function (value) {
              var params = {
                search: value,
                id: $scope.fData.iddepartamento,
                sensor: false
              }
              return UbigeoServices.sListarProvinciaPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLP = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLP = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getDistritoAutocomplete = function (value) {
              console.log($scope.fData.idprovincia);
              var params = {
                search: value,
                id_dpto: $scope.fData.iddepartamento,
                id_prov: $scope.fData.idprovincia,
                sensor: false
              }
              return UbigeoServices.sListarDistritoPorAutocompletado(params).then(function(rpta) { 
                $scope.noResultsLDis = false;
                if( rpta.flag === 0 ){
                  $scope.noResultsLDis = true;
                }
                return rpta.datos; 
              });
            }
            $scope.getSelectedDepartamento = function ($item, $model, $label) {
                $scope.fData.iddepartamento = $item.id;
                $scope.fData.idprovincia = null;
                $scope.fData.provincia = null;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedProvincia = function ($item, $model, $label) {
                $scope.fData.idprovincia = $item.id;
                $scope.fData.iddistrito = null;
                $scope.fData.distrito = null;
            };
            $scope.getSelectedDistrito = function ($item, $model, $label) {
              $scope.fData.iddistrito = $item.id;
            };
            $scope.obtenerDepartamentoPorCodigo = function () {
              if( $scope.fData.iddepartamento ){
                var arrData = {
                  'codigo': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddepartamento = rpta.datos.id;
                    $scope.fData.departamento = rpta.datos.descripcion;
                    $('#fDatadepartamento').focus();
                  }
                });

              }
            }
            $scope.obtenerProvinciaPorCodigo = function () {
              if( $scope.fData.idprovincia ){
                var arrData = {
                  'codigo': $scope.fData.idprovincia,
                  'iddepartamento': $scope.fData.iddepartamento
                }
                UbigeoServices.sListarProvinciaDeDepartamentoPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.idprovincia = rpta.datos.id;
                    $scope.fData.provincia = rpta.datos.descripcion;
                    $('#fDataprovincia').focus();
                  }
                });

              }
            }
            $scope.obtenerDistritoPorCodigo = function () {
              if( $scope.fData.iddistrito ){
                var arrData = {
                  'codigo': $scope.fData.iddistrito,
                  'iddepartamento': $scope.fData.iddepartamento,
                  'idprovincia': $scope.fData.idprovincia
                }
                UbigeoServices.sListarDistritosDeProvinciaPorCodigo(arrData).then(function (rpta) {
                  if( rpta.flag == 1){
                    $scope.fData.iddistrito = rpta.datos.id;
                    $scope.fData.distrito = rpta.datos.descripcion;
                    $('#fDatadistrito').focus();
                  }
                });
              }
            }
            $scope.limpiaDpto = function(){
              $scope.fData.departamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDpto = function(){
              $scope.fData.iddepartamento = null;
              $scope.fData.idprovincia = null;
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaProv = function(){
              $scope.fData.provincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdProv = function(){
              $scope.fData.idprovincia = null;
              $scope.fData.iddistrito = null;
              $scope.fData.distrito = null;
            }
            $scope.limpiaDist = function(){
              $scope.fData.distrito = null;
            }
            $scope.limpiaIdDist = function(){
              $scope.fData.iddistrito = null;
            }
          $scope.generateMap = function(lat,lng) { 
          	if( $scope.fData.ubicacion ){ 
          		if( $scope.fData.ubicacion.length > 3 ){ 
          			$scope.fData.keyGoogleMap = 'AIzaSyDwJmRpFI43RP39yXUQ3yyNOHrt8bK0AC8';
          			//$scope.fData.keyGoogleMap = 'AIzaSyB2FjjCqepP3ZXx6xFbxEKjijPtcNTCcXM';

		          	UbigeoServices.sListarDireccionCoordenadas($scope.fData).then(function(rpta) { 
		      				if(rpta.status == 'OK'){
		      					// console.log(rpta.results,'rpta.results'); 
		      					$scope.fData.lat = rpta.results[0].geometry.location.lat; 
		      					$scope.fData.lng = rpta.results[0].geometry.location.lng; 
		      					//$scope.fData.
		      					$scope.generateMapExec($scope.fData.lat,$scope.fData.lng,16); 
		      				}
		      			});
		      		}
		      	}else{ 
		      		$scope.generateMapExec(lat,lng);
		      	}
          }
          $scope.generateMapExec = function(lat,lng,zoom) { 
          	var lat = parseFloat(lat) || -12.0463667;
          	var lng = parseFloat(lng) || -77.0427891;
          	var zoom = zoom || 9; 
          	var pointCenter = {lat: lat, lng: lng }; 
		        var mapProveedor = new google.maps.Map(document.getElementById('mapProveedor'), { 
		          zoom: zoom,
		          center: pointCenter
		        });
		        var markerProveedor = new google.maps.Marker({
		          position: pointCenter,
		          map: mapProveedor
		        });
          }
          $timeout(function() { 
          	$scope.generateMap($scope.fData.lat,$scope.fData.lng,16);
          }, 1000);
          $scope.changeTextUbicacion = function() {
          	$scope.fData.lat = '';
          	$scope.fData.lng = '';
          }
	      	$scope.aceptar = function () { 
	      		blockUI.start('Procesando información...');
	          ProveedorServices.sEditar($scope.fData).then(function (rpta) { 
	            if(rpta.flag == 1){
	              var pTitle = 'OK!';
	              var pType = 'success';
	              $uibModalInstance.dismiss('cancel');
	              if(typeof $scope.metodos.getPaginationServerSide == 'function'){
									$scope.metodos.getPaginationServerSide(true);
	              }
	            }else if(rpta.flag == 0){
	              var pTitle = 'Error!';
	              var pType = 'danger';
	            }else{
	              alert('Error inesperado');
	            }
	            blockUI.stop(); 
	            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
	          });
	        } 
	      },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
	    });
		}
	}
	return interfaz;
})