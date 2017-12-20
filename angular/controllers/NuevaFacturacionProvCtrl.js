app.controller('NuevaFacturacionProvCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ProveedorFactory',
    'ConceptoFactory',
    'MathFactory',
    'ElementoFactory',
		'FacturacionProvServices',
    'ProveedorServices',
		'ColaboradorServices',
    'TipoDocumentoIdentidadServices',
    'TipoDocumentoMovServices',
    'TipoProveedorServices',
    'ConceptoServices',
    'CategoriaElementoServices',
    'ElementoServices',
    'UbigeoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ProveedorFactory,
    ConceptoFactory,
    MathFactory,
    ElementoFactory,
		FacturacionProvServices,
    ProveedorServices,
		ColaboradorServices,
    TipoDocumentoIdentidadServices,
    TipoDocumentoMovServices,
    TipoProveedorServices,
    ConceptoServices,
    CategoriaElementoServices, 
    ElementoServices,
    UbigeoServices 
) {

  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  
  $scope.fData.fecha_registro = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  $scope.fData.fecha_emision = $filter('date')(moment().toDate(),'dd-MM-yyyy'); 
  //$scope.fData.num_facturacion = '[ ............... ]'; 

  // recargar fConfigSys si no se encuentra 
  if( angular.isUndefined($scope.fConfigSys.precio_incluye_igv_prov) ){ 
    $scope.$parent.getConfiguracionSys();
  }

  $timeout(function() { 
    $scope.fData.modo_igv = parseInt($scope.fConfigSys.precio_incluye_igv_prov); // INCLUYE IGV dinamico 
  },500); 
  
  $scope.fData.idfactprovanterior = null;
  $scope.fData.isRegisterSuccess = false;
  $scope.fData.temporal = {};
  $scope.fData.temporal.cantidad = 1;
  // $scope.metodos.listaCategoriasCliente = function(myCallback) {
  //   var myCallback = myCallback || function() { };
  //   ProveedoriaClienteServices.sListarCbo().then(function(rpta) {
  //     $scope.fArr.listaCategoriaCliente = rpta.datos; 
  //     myCallback();
  //   });
  // };
  // $scope.metodos.listaColaboradores = function(myCallback) {
  //   var myCallback = myCallback || function() { };
  //   ColaboradorServices.sListarCbo().then(function(rpta) {
  //     $scope.fArr.listaColaboradores = rpta.datos; 
  //     myCallback();
  //   });
  // };
  // sexos 
  // $scope.fArr.listaSexo = [ 
  //   { id:'M', descripcion:'MASCULINO' },
  //   { id:'F', descripcion:'FEMENINO' }
  // ]; 
  $scope.mySelectionGrid = [];
  $scope.fData.proveedor = {};

  // TIPOS DE MONEDA 
  // $scope.fArr.listaMoneda = [
  //   {'id' : 1, 'descripcion' : 'S/.', 'str_moneda' : 'S'},
  //   {'id' : 2, 'descripcion' : 'US$', 'str_moneda' : 'D'}
  // ];
  // $scope.fData.moneda = $scope.fArr.listaMoneda[0];
  // TIPO PROVEEDOR 
  $scope.metodos.listaTipoProveedor = function(myCallback) { 
    var myCallback = myCallback || function() { };
    TipoProveedorServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaTipoProveedor = rpta.datos; 
      myCallback();
    });
  };
  // ELEMENTOS
  $scope.metodos.listaElementosProv = function(myCallbackElementos) { 
    var myCallbackElementos = myCallbackElementos || function() { }; 
    ElementoServices.sListarCboProveedores().then(function(rpta) { 
      if( rpta.flag == 1){ 
        $scope.fArr.listaElementosProv = rpta.datos; 
        $scope.fArr.listaElementosProv.splice(0,0,{ id : '0', descripcion:'--Seleccione elemento--'}); 
        myCallbackElementos(); 
      } 
    });
  }
  var myCallbackElementos = function() { 
    $scope.fData.temporal.elemento = $scope.fArr.listaElementosProv[0]; 
  }; 
  $scope.metodos.listaElementosProv(myCallbackElementos);

  // TIPOS DE DOCUMENTO IDENTIDAD
  $scope.metodos.listaTiposDocumentoIdentidad = function(myCallback) { 
    var myCallback = myCallback || function() { };
    var arrParams = {
      'destino': 1 // proveedor empresa 
    }
    TipoDocumentoIdentidadServices.sListarCbo(arrParams).then(function(rpta) { 
      if( rpta.flag == 1){ 
        $scope.fArr.listaTiposDocumentoIdentidad = rpta.datos; 
        myCallback();
      } 
    });
  }
  var myCallback = function() { 
    $scope.fData.tipo_documento_identidad = $scope.fArr.listaTiposDocumentoIdentidad[0];
  }
  $scope.metodos.listaTiposDocumentoIdentidad(myCallback); 

  // CONCEPTOS 
  $scope.metodos.listaConceptos = function(myCallbackConceptos) { 
    var myCallbackConceptos = myCallbackConceptos || function() { }; 
    var arrParams = { 
      'tipo_concepto': 'P' 
    }; 
    ConceptoServices.sListarCbo(arrParams).then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaConceptos = rpta.datos; 
        $scope.fArr.listaConceptos.splice(0,0,{ id : '0', descripcion:'--Seleccione concepto--'}); 
        myCallbackConceptos();
      } 
    });
  }
  var myCallbackConceptos = function() { 
    $scope.fData.concepto = $scope.fArr.listaConceptos[0]; 
  }; 
  $scope.metodos.listaConceptos(myCallbackConceptos);

  // TIPO DE DOCUMENTO 
  $scope.metodos.listaTipoDocumentoMov = function(myCallbackTDM) { 
    var myCallbackTDM = myCallbackTDM || function() { };
    TipoDocumentoMovServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaTipoDocumentoMov = rpta.datos; 
        myCallbackTDM();
      } 
    });
  } 
  var myCallbackTDM = function() { 
    $scope.fData.tipo_documento_mov = $scope.fArr.listaTipoDocumentoMov[0];  
  }
  $scope.metodos.listaTipoDocumentoMov(myCallbackTDM); 

  // FORMAS DE PAGO 
  // $scope.metodos.listaFormaPago = function(myCallback) { 
  //   var myCallback = myCallback || function() { };
  //   FormaPagoServices.sListarCbo().then(function(rpta) { 
  //     if( rpta.flag == 1){
  //       $scope.fArr.listaFormaPago = rpta.datos; 
  //       myCallback();
  //     } 
  //   });
  // } 
  // var myCallback = function() { 
  //   $scope.fData.forma_pago = $scope.fArr.listaFormaPago[0]; 
  // }
  // $scope.metodos.listaFormaPago(myCallback); 

  // TIPOS DE ELEMENTO 
  $scope.fArr.listaTipoElemento = [ 
    {'id' : 'P', 'descripcion' : 'PRODUCTO'},
    {'id' : 'S', 'descripcion' : 'SERVICIO'}
  ]; 
  // CATEGORIAS DE ELEMENTOS 
  $scope.metodos.listaCategoriasElemento = function(myCallback) {
    var myCallback = myCallback || function() { };
    CategoriaElementoServices.sListarCbo().then(function(rpta) {
      $scope.fArr.listaCategoriasElemento = rpta.datos; 
      myCallback();
    });
  };
  //WATCHERS 
  $scope.$watch('fData.num_documento', function(newValue,oldValue){ 
    if( oldValue == newValue ){
      return false; 
    }
    if( !(newValue) ){
      $scope.fData.proveedor = {};
      $scope.fData.classEditProveedor = 'disabled';
    }
  }, true);

  // GENERACION DE NUMERO DE FACTURACION  
  // $scope.metodos.generarNumeroFacturacion = function(loader) { 
  //   if(loader){
  //     blockUI.start('Generando numero de venta...'); 
  //   }
  //   var arrParams = {
  //     'sede': $scope.fData.sede 
  //   }; 
  //   FacturacionProvServices.sGenerarNumeroVenta(arrParams).then(function(rpta) { 
  //     $scope.fData.num_facturacion = '[ ............... ]'; 
  //     if( rpta.flag == 1){ 
  //       $scope.fData.num_facturacion = rpta.datos.num_facturacion; 
  //     }
  //     if(loader){
  //       blockUI.stop(); 
  //     }
  //   });
  // }
  // GENERACIÓN DE NUMERO DE SERIE Y CORRELATIVO 
  // $scope.metodos.generarNumeroSerieCorrelativo = function (loader) {
  //   if(loader){
  //     blockUI.start('Generando numero de serie y correlativo...'); 
  //   }
  //   var arrParams = {
  //     'tipo_documento_mov': $scope.fData.tipo_documento_mov,
  //     'serie': $scope.fData.serie 
  //   }; 
  //   FacturacionProvServices.sGenerarNumeroSerieCorrelativo(arrParams).then(function(rpta) { 
  //     $scope.fData.num_serie = '[ ............... ]';
  //     if(rpta.flag == 1){
  //       var pTitle = 'OK!';
  //       var pType = 'success';
  //       $scope.fData.num_serie = rpta.datos.num_serie; 
  //       $scope.fData.num_correlativo = rpta.datos.num_correlativo;
  //       $scope.fData.num_solo_correlativo = rpta.datos.num_solo_correlativo;
  //       $scope.fData.serie_correlativo = rpta.datos.num_serie + '-'+rpta.datos.num_correlativo; 
  //     }else if(rpta.flag == 0){
  //       var pTitle = 'Error!';
  //       var pType = 'danger';
  //       pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
  //     }else{
  //       alert('Error inesperado');
  //     }
  //     if(loader){
  //       blockUI.stop(); 
  //     }
  //     // if( rpta.flag == 1){ 
  //     //   $scope.fData.num_serie = rpta.datos.num_serie; 
  //     //   $scope.fData.num_correlativo = rpta.datos.num_correlativo; 
  //     // }
  //     // if(loader){
  //     //   blockUI.stop(); 
  //     // }
  //   });
  // }

  // OBTENER DATOS DE PROVEEDOR 
  $scope.obtenerDatosProveedor = function() { 
    if( !($scope.fData.num_documento) ){
      $scope.btnBusquedaProveedor();
      return; 
    }
    blockUI.start('Procesando información...'); 
    $scope.fData.proveedor = {};
    var arrParams = {
      'tipo_documento': $scope.fData.tipo_documento_identidad, 
      'num_documento': $scope.fData.num_documento 
    }; 
    ProveedorServices.sBuscarEsteProveedor(arrParams).then(function(rpta) { 
      if( rpta.flag == 1 ){
        $scope.fData.proveedor = rpta.datos.proveedor; 
        $scope.fData.classEditProveedor = '';
        pinesNotifications.notify({ title: 'OK!', text: rpta.message, type: 'success', delay: 3000 });
      }else{
        $scope.fData.proveedor = {}; 
        // ABRIMOS EL MODAL DE BUSQUEDA DE PROVEEDOR  
        pinesNotifications.notify({ title: 'Advertencia', text: rpta.message, type: 'warning', delay: 3000 });
        $scope.fData.classEditProveedor = 'disabled';
        $scope.btnBusquedaProveedor();
      }
      blockUI.stop(); 
    }); 
  }
  // BUSCAR PROVEEDOR  
  $scope.btnBusquedaProveedor = function() { 
    blockUI.start('Procesando información...'); 
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Proveedor/ver_popup_busqueda_proveedores',
      size: 'md',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fBusqueda = {};
        if($scope.fData.tipo_documento_identidad.destino == 1){ // empresa 
          $scope.fBusqueda.tipo_proveedor = 'pe'; 
        }
        if($scope.fData.tipo_documento_identidad.destino == 2){ // persona 
          $scope.fBusqueda.tipo_proveedor = 'pp'; 
        }
        $scope.titleForm = 'Búsqueda de Proveedores'; 
        var paginationOptionsBP = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 100,
          sort: uiGridConstants.ASC,
          sortName: null,
          search: null
        };
        $scope.mySelectionGridBP = [];
        $scope.fArr.gridOptionsBP = { 
          paginationPageSizes: [100, 500, 1000, 10000],
          paginationPageSize: 100,
          useExternalPagination: true,
          useExternalSorting: true,
          enableGridMenu: true,
          enableRowSelection: true,
          enableSelectAll: false,
          enableFiltering: true,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [
            { field: 'idproveedor', name: 'pr.idproveedor', displayName: 'ID', width: '60',  sort: { direction: uiGridConstants.DESC} },
            { field: 'tipo_proveedor', name: 'tpr.descripcion_tpr', type: 'object', displayName: 'Tipo de Proveedor', minWidth: 140,
              cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>', visible: false 
            },
            { field: 'tipo_documento_identidad', name: 'tdi.descripcion_tdi', type: 'object', displayName: 'Tipo de Doc.', minWidth: 120, visible: false, 
              cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>' 
            },
            { field: 'numero_documento', name: 'pr.numero_documento_pr', displayName: 'N°. Documento', minWidth: 114 },
            { field: 'razon_social', name: 'pr.razon_social_pr', displayName: 'Razón Social', minWidth: 170 },
            { field: 'nombre_comercial', name: 'pr.nombre_comercial_pr', displayName: 'Nombre Comercial', minWidth: 180 },
            { field: 'direccion', name: 'pr.direccion_pr', displayName: 'Dirección', minWidth: 180, visible: false },
            { field: 'departamento', name: 'dpto.descripcion_ubig', displayName: 'Departamento', minWidth: 100, visible: false },
            { field: 'provincia', name: 'prov.descripcion_ubig', displayName: 'Provincia', minWidth: 100, visible: false },
            { field: 'distrito', name: 'dist.descripcion_ubig', displayName: 'Distrito', minWidth: 100, visible: false },
            { field: 'estado_obj', name: 'pr.estado_pr', type: 'object', displayName: ' ', minWidth: 90, enableFiltering: false, 
              cellTemplate:'<div class="ui-grid-cell-contents">' + 
                '<label style="box-shadow: 1px 1px 0 black; display: block;font-size: 12px;" class="label {{ COL_FIELD.claseLabel }} "> <i class="{{ COL_FIELD.claseIcon }}"></i> {{ COL_FIELD.labelText }}' + 
                '</label></div>' 
            }
          ],
          onRegisterApi: function(gridApi) { // gridComboOptions
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope,function(row){ 
              $scope.mySelectionGridBP = gridApi.selection.getSelectedRows();
              if( !($scope.mySelectionGridBP[0].razon_social) ){ 
                pinesNotifications.notify({ title: 'Advertencia.', type: 'warning', delay: 3000, text: 'Falta configurar los datos de los proveedores desde el mantenimiento de proveedores.' });
                return false;
              }
              
              $scope.fData.proveedor = $scope.mySelectionGridBP[0]; //console.log($scope.fData.Proveedor);
              $scope.fData.num_documento = $scope.mySelectionGridBP[0].numero_documento; 
              $scope.fData.classEditProveedor = '';
              $uibModalInstance.dismiss('cancel');
            });
            $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
              if (sortColumns.length == 0) {
                paginationOptionsBP.sort = null;
                paginationOptionsBP.sortName = null;
              } else {
                paginationOptionsBP.sort = sortColumns[0].sort.direction;
                paginationOptionsBP.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideBP(true);
            });
            gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptionsBP.pageNumber = newPage;
              paginationOptionsBP.pageSize = pageSize;
              paginationOptionsBP.firstRow = (paginationOptionsBP.pageNumber - 1) * paginationOptionsBP.pageSize;
              $scope.metodos.getPaginationServerSideBP(true);
            });
            $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptionsBP.search = true;
              paginationOptionsBP.searchColumn = { 
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
              }; 
              $scope.metodos.getPaginationServerSideBP();
            });
          }
        }; 
        $scope.metodos.getPaginationServerSideBP = function(loader) { 
          if(loader){
            blockUI.start('Procesando información...'); 
          }
          var arrParams = {
            paginate : paginationOptionsBP 
            //datos: $scope.fBusqueda 
          };
          ProveedorServices.sListar(arrParams).then(function (rpta) {
            $scope.fArr.gridOptionsBP.totalItems = rpta.paginate.totalRows;
            $scope.fArr.gridOptionsBP.data = rpta.datos;
            if(loader){
              blockUI.stop(); 
            }
          });
          // $scope.mySelectionClienteGrid = [];  
          // cambiamos documento de cliente si se cambia el radio 
          // var objIndex = $scope.fArr.listaTiposDocumentoIdentidad.filter(function(obj) { 
          //   return obj.destino_str == $scope.fBusqueda.tipo_cliente;
          // }).shift(); 
          // $scope.fData.tipo_documento_identidad = objIndex; 
        }
        $scope.metodos.getPaginationServerSideBP(true); 
        $scope.cancel = function () { 
          $uibModalInstance.dismiss('cancel');
        }
        
      }
    });
  }
  // NUEVO PROVEEDOR 
  $scope.btnNuevoProveedor = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr 
      }
      ProveedorFactory.regProveedorModal(arrParams); 
  }
  // EDITAR PROVEEDOR 
  $scope.btnEditarProveedor = function() {
    if($scope.fData.classEditProveedor == 'disabled'){ 
      return; 
    };
    var arrParams = {
      'metodos': $scope.metodos,
      'mySelectionGrid': [$scope.fData.proveedor],
      'fArr': $scope.fArr,
      'fSessionCI': $scope.fSessionCI 
    }; 
    ProveedorFactory.editProveedorModal(arrParams); 
  }

  // NUEVO PRODUCTO 
  // $scope.btnNuevoProducto = function() {
  //     var arrParams = { 
  //       'metodos': $scope.metodos,
  //       'fArr': $scope.fArr 
  //     }
  //     ProductoFactory.regProductoModal(arrParams); 
  // }
  // NUEVO ELEMENTO  
  // $scope.btnNuevoElemento = function() { 
  //     var arrParams = { 
  //       'metodos': $scope.metodos,
  //       'fArr': $scope.fArr 
  //     }
  //     ElementoFactory.regElementoModal(arrParams); 
  // }
  // $scope.getElementoAutocomplete = function (value) { 
  //   var params = {
  //     searchText: value, 
  //     searchColumn: "descripcion_ele",
  //     sensor: false
  //   }
  //   return ElementoServices.sListarElementosAutoComplete(params).then(function(rpta) {
  //     //console.log('Datos: ',rpta.datos);
  //     $scope.noResultsELE = false;
  //     // $scope.fData.classValid = ' input-success-border';
  //     if( rpta.flag === 0 ){
  //       $scope.noResultsELE = true;
  //       // $scope.fData.classValid = ' input-danger-border';
  //     }
  //     return rpta.datos;
  //   });
  // } 
  // $scope.getSelectedElemento = function (item, model) { 
  //   console.log(item, model, 'item, model');
  //   $scope.fData.temporal.precio_unitario = model.precio_referencial;
  //   if( angular.isObject( $scope.fData.temporal.elemento ) ){
  //     $scope.fData.classValid = ' input-success-border';
  //   }else{
  //     $scope.fData.classValid = ' input-danger-border';
  //   }
  //   $timeout(function() {
  //     $scope.calcularImporte();
  //   },100);
  // }
  // $scope.validateElemento = function() { 
  //   if( angular.isObject( $scope.fData.temporal.elemento ) ){
  //     $scope.fData.classValid = ' input-success-border';
  //     $scope.noResultsELE = false;
  //   }else{
  //     if( $scope.fData.temporal.elemento ){
  //       $scope.fData.classValid = ' input-danger-border';
  //       $scope.noResultsELE = true;
  //     }else{
  //       $scope.fData.classValid = ' input-normal-border';
  //       $scope.noResultsELE = false;
  //     }
      
  //   }
  // }
  // BUSCAR ELEMENTOS  
  // $scope.btnBusquedaElemento = function() { 
  //   blockUI.start('Procesando información...'); 
  //   $uibModal.open({ 
  //     templateUrl: angular.patchURLCI+'Elemento/ver_popup_busqueda_elementos',
  //     size: 'md',
  //     backdrop: 'static',
  //     keyboard:false,
  //     scope: $scope,
  //     controller: function ($scope, $uibModalInstance) { 
  //       blockUI.stop(); 
  //       $scope.fBusqueda = {};
  //       if($scope.fData.tipo_documento_identidad.destino == 1){ // empresa
  //         $scope.fBusqueda.tipo_proveedor = 'pe'; 
  //       }
  //       if($scope.fData.tipo_documento_identidad.destino == 2){ // persona 
  //         $scope.fBusqueda.tipo_proveedor = 'pp'; 
  //       }
  //       $scope.titleForm = 'Búsqueda de Elementos'; 
  //       var paginationOptionsBELE = {
  //         pageNumber: 1,
  //         firstRow: 0,
  //         pageSize: 100,
  //         sort: uiGridConstants.ASC,
  //         sortName: null,
  //         search: null
  //       };
  //       $scope.mySelectionGridBELE = [];
  //       $scope.gridOptionsBELE = {
  //         //rowHeight: 36,
  //         paginationPageSizes: [100, 500, 1000, 10000],
  //         paginationPageSize: 100,
  //         useExternalPagination: true,
  //         useExternalSorting: true,
  //         enableGridMenu: true,
  //         enableSelectAll: false,
  //         enableFiltering: true,
  //         enableRowSelection: true,
  //         enableFullRowSelection: true,
  //         multiSelect: false,
  //         columnDefs: [],
  //         onRegisterApi: function(gridApi) { // gridComboOptions
  //           $scope.gridApi = gridApi;
  //           gridApi.selection.on.rowSelectionChanged($scope,function(row){
  //             $scope.mySelectionGridBELE = gridApi.selection.getSelectedRows();
  //             $scope.fData.temporal.elemento = $scope.mySelectionGridBELE[0]; 
  //             $scope.fData.temporal.precio_unitario = $scope.mySelectionGridBELE[0].precio_referencial; 
  //             $timeout(function() {
  //               $scope.calcularImporte();
  //               $uibModalInstance.dismiss('cancel');
  //             },100);
              
  //             // $timeout(function() {
  //             //   $('#temporalElemento').focus(); //console.log('focus me',$('#temporalElemento'));
  //             // }, 1000);
  //           });
  //           $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
  //             if (sortColumns.length == 0) {
  //               paginationOptionsBELE.sort = null;
  //               paginationOptionsBELE.sortName = null;
  //             } else {
  //               paginationOptionsBELE.sort = sortColumns[0].sort.direction;
  //               paginationOptionsBELE.sortName = sortColumns[0].name;
  //             }
  //             $scope.metodos.getPaginationServerSideBELE(true);
  //           });
  //           gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
  //             paginationOptionsBELE.pageNumber = newPage;
  //             paginationOptionsBELE.pageSize = pageSize;
  //             paginationOptionsBELE.firstRow = (paginationOptionsBELE.pageNumber - 1) * paginationOptionsBELE.pageSize;
  //             $scope.metodos.getPaginationServerSideBELE(true);
  //           });
  //           $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
  //             var grid = this.grid;
  //             paginationOptionsBELE.search = true;
  //             paginationOptionsBELE.searchColumn = { 
  //               'el.idelemento' : grid.columns[1].filters[0].term,
  //               'el.descripcion_ele' : grid.columns[3].filters[0].term,
  //               'um.descripcion_um' : grid.columns[4].filters[0].term,
  //               'el.precio_referencial' : grid.columns[5].filters[0].term,
  //               'cael.descripcion_cael' : grid.columns[6].filters[0].term 
  //             }; 
  //             $scope.metodos.getPaginationServerSideBELE();
  //           });
  //         }
  //       }; 
  //       $scope.metodos.cambioColumnas = function() { 
  //         $scope.gridOptionsBELE.columnDefs = [ 
  //           { field: 'id', name: 'el.idelemento', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
  //           { field: 'tipo_elemento', type: 'object', name: 'el.tipo_elemento', displayName: 'TIPO', minWidth: 70,
  //             cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
  //                 '{{ COL_FIELD.descripcion }}</div>',
  //             enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false 
  //           },
  //           { field: 'descripcion_ele', name: 'el.descripcion_ele', displayName: 'Elemento', minWidth: 160 },
  //           { field: 'unidad_medida', type: 'object', name: 'um.descripcion_um', displayName: 'Unidad Medida Ref.', minWidth: 100, visible: false, 
  //             cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
  //                 '{{ COL_FIELD.descripcion }}</div>' 
  //           },
  //           { field: 'precio_referencial', name: 'el.precio_referencial', displayName: 'Precio Ref.', minWidth: 100, visible: false },
  //           { field: 'categoria_elemento', type: 'object', name: 'cael.descripcion_cael', displayName: 'Categoria', minWidth: 70, enableColumnMenus: false, enableColumnMenu: false, 
  //               cellTemplate:'<div class="ui-grid-cell-contents text-center ">'+ 
  //                 '<label class="label bg-primary block" style="background-color:{{COL_FIELD.color}}">{{ COL_FIELD.descripcion }}</label></div>' 
  //           }
  //         ]; 
  //         paginationOptionsBELE.sortName = $scope.gridOptionsBELE.columnDefs[0].name;
  //       }
  //       $scope.metodos.cambioColumnas(); 
  //       $scope.metodos.getPaginationServerSideBELE = function(loader) { 
  //         if(loader){
  //           blockUI.start('Procesando información...'); 
  //         }
  //         var arrParams = {
  //           paginate : paginationOptionsBELE 
  //         };
  //         ElementoServices.sListarElementosBusqueda(arrParams).then(function (rpta) {
  //           $scope.gridOptionsBELE.totalItems = rpta.paginate.totalRows;
  //           $scope.gridOptionsBELE.data = rpta.datos;
  //           if(loader){
  //             blockUI.stop(); 
  //           }
  //         });
  //         $scope.mySelectionClienteGrid = [];  
  //       }
  //       $scope.metodos.getPaginationServerSideBELE(true); 
  //       $scope.cancel = function () {
  //         $uibModalInstance.dismiss('cancel');
  //       }
        
  //     }
  //   });
  // }

  // NUEVO CONTACTO 
  // $scope.btnNuevoContacto = function() { 
  //   var arrParams = {
  //       'metodos': $scope.metodos,
  //       'fArr': $scope.fArr 
  //   }
  //   ContactoClienteFactory.regContactoModal(arrParams); 
  // }

  // $scope.getContactoAutocomplete = function (value) { 
  //   var params = {
  //     searchText: value, 
  //     searchColumn: "contacto",
  //     sensor: false,
  //     datos: $scope.fData
  //   }
  //   return ProveedoroClienteServices.sListarContactoAutoComplete(params).then(function(rpta) { 
  //     $scope.noResultsCT = false;
  //     if( rpta.flag === 0 ){
  //       $scope.noResultsCT = true;
  //     }
  //     return rpta.datos;
  //   });
  // } 

  // $scope.getSelectedContacto = function (item, model) { 
  //   $scope.fData.num_documento = model.ruc;
  //   $scope.fData.proveedor.id = model.idclienteempresa;
  //   $scope.fData.proveedor.razon_social = model.razon_social;
  //   $scope.fData.proveedor.representante_legal = model.representante_legal;
  //   $scope.fData.proveedor.dni_representante_legal = model.dni_representante_legal; 

  //   $scope.fData.proveedor.telefono_contacto = model.telefono_fijo; 
  //   $scope.fData.proveedor.anexo_contacto = model.anexo; 
  // }

  // $scope.validateContacto = function() { 
  //   if( angular.isObject( $scope.fData.contacto ) ){
  //     $scope.fData.classValid = ' input-success-border';
  //     $scope.noResultsCT = false;
  //   }else{
  //     if( $scope.fData.temporal.elemento ){
  //       $scope.fData.classValid = ' input-danger-border';
  //       $scope.noResultsCT = true;
  //     }else{
  //       $scope.fData.classValid = ' input-normal-border';
  //       $scope.noResultsCT = false;
  //     }      
  //   }
  // }  

  // BUSCAR Contactos  
  // $scope.btnBusquedaContacto = function() { 
  //   blockUI.start('Procesando información...'); 
  //   $uibModal.open({ 
  //     templateUrl: angular.patchURLCI+'ContactoProveedor/ver_popup_busqueda_contacto',
  //     size: 'md',
  //     backdrop: 'static',
  //     keyboard:false,
  //     scope: $scope,
  //     controller: function ($scope, $uibModalInstance) { 
  //       blockUI.stop(); 
  //       $scope.fBusqueda = {};

  //       $scope.titleForm = 'Búsqueda de Contactos'; 
  //       var paginationOptionsCO = {
  //         pageNumber: 1,
  //         firstRow: 0,
  //         pageSize: 100,
  //         sort: uiGridConstants.ASC,
  //         sortName: null,
  //         search: null
  //       };
  //       $scope.mySelectionGridCO = [];
  //       $scope.gridOptionsCO = {
  //         paginationPageSizes: [100, 500, 1000, 10000],
  //         paginationPageSize: 100,
  //         useExternalPagination: true,
  //         useExternalSorting: true,
  //         enableGridMenu: true,
  //         enableRowSelection: true,
  //         enableSelectAll: false,
  //         enableFiltering: true,
  //         enableFullRowSelection: true,
  //         multiSelect: false,
  //         columnDefs: [ 
  //           { field: 'id', name: 'idcontacto', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
  //           { field: 'nombres', name: 'nombres', displayName: 'Nombre', minWidth: 120 },
  //           { field: 'apellidos', name: 'apellidos', displayName: 'Apellidos', minWidth: 120 },
  //           { field: 'razon_social', name: 'razon_social', displayName: 'Empresa', minWidth: 140 },
  //           { field: 'ruc', name: 'ruc', displayName: 'RUC', minWidth: 80 } 
  //         ],
  //         onRegisterApi: function(gridApi) { // gridComboOptions
  //           $scope.gridApi = gridApi;
  //           gridApi.selection.on.rowSelectionChanged($scope,function(row){
  //             $scope.mySelectionGridCO = gridApi.selection.getSelectedRows();
  //             $scope.fData.contacto = $scope.mySelectionGridCO[0];
  //             $scope.fData.proveedor = $scope.mySelectionGridCO[0].cliente_empresa; 
  //             $scope.fData.num_documento = $scope.mySelectionGridCO[0].cliente_empresa.ruc; 
  //             $scope.fData.classEditProveedor = '';
  //             $uibModalInstance.dismiss('cancel');
  //           });
  //           $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
  //             if (sortColumns.length == 0) {
  //               paginationOptionsCO.sort = null;
  //               paginationOptionsCO.sortName = null;
  //             } else {
  //               paginationOptionsCO.sort = sortColumns[0].sort.direction;
  //               paginationOptionsCO.sortName = sortColumns[0].name;
  //             }
  //             $scope.metodos.getPaginationServerSideCO(true);
  //           });
  //           gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
  //             paginationOptionsCO.pageNumber = newPage;
  //             paginationOptionsCO.pageSize = pageSize;
  //             paginationOptionsCO.firstRow = (paginationOptionsCO.pageNumber - 1) * paginationOptionsCO.pageSize;
  //             $scope.metodos.getPaginationServerSideCO(true);
  //           });
  //           $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
  //             var grid = this.grid;
  //             paginationOptionsCO.search = true;
  //             paginationOptionsCO.searchColumn = { 
  //               'co.idcontacto' : grid.columns[1].filters[0].term,
  //               'co.nombres' : grid.columns[2].filters[0].term,
  //               'co.apellidos' : grid.columns[3].filters[0].term,
  //               'ce.razon_social' : grid.columns[4].filters[0].term,
  //               'ce.ruc' : grid.columns[5].filters[0].term
  //             }; 
  //             $scope.metodos.getPaginationServerSideCO();
  //           });
  //         }
  //       }; 

  //       $scope.metodos.getPaginationServerSideCO = function(loader) { 
  //         if(loader){
  //           blockUI.start('Procesando información...'); 
  //         }
  //         var arrParams = {
  //           paginate : paginationOptionsCO,
  //           datos: $scope.fData 
  //         }; 
  //         ProveedoroClienteServices.sListarContactoBusqueda(arrParams).then(function (rpta) {
  //           $scope.gridOptionsCO.totalItems = rpta.paginate.totalRows;
  //           $scope.gridOptionsCO.data = rpta.datos;
  //           if(loader){
  //             blockUI.stop(); 
  //           }
  //         });
  //         $scope.mySelectionClienteGrid = [];  
  //       }
  //       $scope.metodos.getPaginationServerSideCO(true); 
  //       $scope.cancel = function () {
  //         $uibModalInstance.dismiss('cancel');
  //       }
        
  //     }
  //   });  
  // }

  // CESTA DE ELEMENTOS 
  $scope.mySelectionGrid = [];
  $scope.gridOptions = { 
    paginationPageSize: 50,
    enableRowSelection: true,
    enableSelectAll: false,
    enableFiltering: false,
    enableFullRowSelection: false,
    data: null,
    rowHeight: 26,
    enableCellEditOnFocus: true,
    multiSelect: false,
    columnDefs: [
      { field: 'idelemento', displayName: 'COD.', width: 50, enableCellEdit: false, enableSorting: false },
      { field: 'descripcion', displayName: 'DESCRIPCION', minWidth: 170, enableCellEdit: false, enableSorting: false,
        cellTemplate:'<div class="ui-grid-cell-contents "> <a class="text-info block" href="">'+ '{{ COL_FIELD }}</a></div>', 
        cellTooltip: function( row, col ) {
          return row.entity.descripcion;
        }
      },
      { field: 'cantidad', displayName: 'CANT.', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-center' },
      { field: 'precio_unitario', displayName: 'P. UNIT', width: 80, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell text-right' },
      { field: 'importe_sin_igv', displayName: 'IMPORTE SIN IGV', width: 120, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible: true },
      { field: 'igv', displayName: 'IGV', width: 80, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible:true },
      { field: 'importe_con_igv', displayName: 'IMPORTE', width: 120, enableCellEdit: false, enableSorting: false, cellClass:'text-right', visible:true },
      { field: 'excluye_igv', displayName: 'INAFECTO', width: 90, enableCellEdit: true, enableSorting: false, cellClass:'ui-editCell',
        editableCellTemplate: 'ui-grid/dropdownEditor',cellFilter: 'mapInafecto', editDropdownValueLabel: 'inafecto', editDropdownOptionsArray: [
          { id: 1, inafecto: 'SI' },
          { id: 2, inafecto: 'NO' }
        ],cellTemplate: '<div class="text-center ui-grid-cell-contents" ng-if="COL_FIELD == 1"> SI </div><div class="text-center" ng-if="COL_FIELD == 2"> NO </div>'
      },
      { field: 'accion', displayName: 'ACCIÓN', width: 110, enableCellEdit: false, enableSorting: false, 
        cellTemplate:'<div class="m-xxs text-center">'+ 
          '<button uib-tooltip="Quitar" tooltip-placement="left" type="button" class="btn btn-xs btn-danger" ng-click="grid.appScope.btnQuitarDeLaCesta(row)"> <i class="fa fa-trash"></i> </button>' + 
          '</div>' 
      } 
    ]
    ,onRegisterApi: function(gridApi) { 
      $scope.gridApi = gridApi;
      gridApi.edit.on.afterCellEdit($scope,function (rowEntity, colDef, newValue, oldValue){ 
        rowEntity.column = colDef.field;
        //console.log(oldValue,newValue,'oldValue,newValue');
        if(rowEntity.column == 'cantidad'){
          if( !(rowEntity.cantidad >= 1) ){
            var pTitle = 'Advertencia!';
            var pType = 'warning';
            rowEntity.cantidad = oldValue;
            pinesNotifications.notify({ title: pTitle, text: 'La cantidad debe ser mayor o igual a 1', type: pType, delay: 3500 });
            return false;
          }
        }
        if(rowEntity.column == 'precio_unitario'){
          if( !(rowEntity.precio_unitario >= 0) ){
            var pTitle = 'Advertencia!';
            var pType = 'warning';
            rowEntity.precio_unitario = oldValue;
            pinesNotifications.notify({ title: pTitle, text: 'El Precio debe ser mayor o igual a 0', type: pType, delay: 3500 });
            return false;
          }
        }
        if( $scope.fData.modo_igv == 2 ){ 
          console.log('Calculando modo NO INCLUYE IGV');
          rowEntity.importe_sin_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed($scope.fConfigSys.num_decimal_total_key);
          if(rowEntity.excluye_igv == 1){
           rowEntity.igv = 0.00;
          }else{
           rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed($scope.fConfigSys.num_decimal_total_key);
          }
          rowEntity.importe_con_igv = (parseFloat(rowEntity.importe_sin_igv) + parseFloat(rowEntity.igv)).toFixed($scope.fConfigSys.num_decimal_total_key);
        }
        if( $scope.fData.modo_igv == 1 ){ 
          console.log('Calculando modo INCLUYE IGV');
          rowEntity.importe_con_igv = (parseFloat(rowEntity.precio_unitario) * parseFloat(rowEntity.cantidad)).toFixed($scope.fConfigSys.num_decimal_total_key);
          if(rowEntity.excluye_igv == 1){
            rowEntity.importe_sin_igv = rowEntity.importe_con_igv;
            rowEntity.igv = 0.00;
          }else{
            rowEntity.importe_sin_igv = (rowEntity.importe_con_igv / 1.18).toFixed($scope.fConfigSys.num_decimal_total_key);
            rowEntity.igv = (0.18 * rowEntity.importe_sin_igv).toFixed($scope.fConfigSys.num_decimal_total_key);
          }
        }
        $scope.calcularTotales();
        $scope.$apply();
      });
    }
  };
  $scope.getTableHeight = function() {
     var rowHeight = 26; // your row height 
     var headerHeight = 25; // your header height 
     return {
        // height: ($scope.gridOptions.data.length * rowHeight + headerHeight + 40) + "px"
        height: (4 * rowHeight + headerHeight + 20) + "px"
     };
  };
  $scope.agregarItem = function () {
    $('#temporalElemento').focus();
    if( !angular.isObject($scope.fData.temporal.elemento) ){ 
      $scope.fData.temporal = {
        cantidad: 1,
        importe_con_igv: null,
        importe_sin_igv: null,
        elemento: $scope.fArr.listaElementosProv[0],
        excluye_igv: 2,
      };
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No se ha seleccionado el elemento', type: 'warning', delay: 2000 });
      return false;
    }
    if( !($scope.fData.temporal.precio_unitario >= 0) || !($scope.fData.temporal.precio_unitario) ){
      $scope.fData.temporal.precio_unitario = null;
      $('#temporalPrecioUnit').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'Ingrese un precio válido', type: 'warning', delay: 2000 });
      return false;
    }
    if( !($scope.fData.temporal.cantidad >= 1) ){
      $scope.fData.temporal.cantidad = null;
      $('#temporalCantidad').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'Ingrese una cantidad válida', type: 'warning', delay: 2000 });
      return false;
    }
    $scope.arrTemporal = { 
      'id' : $scope.fData.temporal.elemento.id,
      'descripcion' : $scope.fData.temporal.elemento.descripcion,
      'cantidad' : $scope.fData.temporal.cantidad,
      'precio_unitario' : $scope.fData.temporal.precio_unitario,
      'importe_sin_igv' : $scope.fData.temporal.importe_sin_igv,
      'igv' : $scope.fData.temporal.igv,
      'importe_con_igv' : $scope.fData.temporal.importe_con_igv,
      'excluye_igv' : 2
    };
    if( $scope.gridOptions.data === null ){
      $scope.gridOptions.data = [];
    }
    $scope.gridOptions.data.push($scope.arrTemporal);
    $scope.calcularTotales(); 
    $scope.fData.temporal = {
      cantidad: 1,
      importe_con_igv: null,
      elemento: $scope.fArr.listaElementosProv[0],
      excluye_igv: 2
    };
    $scope.fData.classValid = ' input-normal-border'; 
    // $scope.fData.temporal.caracteristicas = null;
    // console.log($scope.fData.classValid,'$scope.fData.classValid');
  }
  $scope.btnQuitarDeLaCesta = function (row) { 
    var index = $scope.gridOptions.data.indexOf(row.entity); 
    $scope.gridOptions.data.splice(index,1);
    $scope.calcularTotales(); 
  }
  $scope.cambiarModo = function(){ // 
    if( $scope.fData.modo_igv == 2){
      console.log('Calculando modo NO INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) { 
        $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].igv = 0.00;
        }else{
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        }
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv) + parseFloat($scope.gridOptions.data[key].igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        
      });
    }
    if( $scope.fData.modo_igv == 1 ){ 
      console.log('Calculando modo INCLUYE IGV');
      angular.forEach($scope.gridOptions.data,function (value, key) {
        $scope.gridOptions.data[key].importe_con_igv = (parseFloat($scope.gridOptions.data[key].precio_unitario) * parseFloat($scope.gridOptions.data[key].cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        if( $scope.gridOptions.data[key].excluye_igv == 1 ){
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
          $scope.gridOptions.data[key].igv = 0.00;
        } else{
          $scope.gridOptions.data[key].importe_sin_igv = (parseFloat($scope.gridOptions.data[key].importe_con_igv) / 1.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
          $scope.gridOptions.data[key].igv = (parseFloat($scope.gridOptions.data[key].importe_sin_igv)*0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        } 
      });
    }
    $scope.calcularTotales();
  };
  $scope.calcularTotales = function () { 
    var subtotal = 0;
    var igv = 0;
    var total = 0;
    angular.forEach($scope.gridOptions.data,function (value, key) { 
      total += parseFloat($scope.gridOptions.data[key].importe_con_igv);
      igv += parseFloat($scope.gridOptions.data[key].igv);
      subtotal += parseFloat($scope.gridOptions.data[key].importe_sin_igv);
    });
    //$scope.fData.subtotal_temp = (total / 1.18);
    $scope.fData.subtotal = MathFactory.redondear(subtotal).toFixed($scope.fConfigSys.num_decimal_total_key);
    $scope.fData.igv = MathFactory.redondear(igv).toFixed($scope.fConfigSys.num_decimal_total_key);
    $scope.fData.total = MathFactory.redondear(total).toFixed($scope.fConfigSys.num_decimal_total_key);
  }
  $scope.calcularImporte = function (){
    if(/*$scope.fData.temporal.precio_unitario != '' && $scope.fData.temporal.cantidad != '' &&*/ angular.isObject($scope.fData.temporal.elemento) ){ 
      if( $scope.fData.modo_igv == 2 ){ 
        console.log('Calculando modo NO INCLUYE IGV');
        $scope.fData.temporal.importe_sin_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.igv = ($scope.fData.temporal.importe_sin_igv * 0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.importe_sin_igv) + parseFloat($scope.fData.temporal.igv)).toFixed($scope.fConfigSys.num_decimal_precio_key);
      }
      if( $scope.fData.modo_igv == 1 ){ 
        console.log('Calculando modo INCLUYE IGV');
        $scope.fData.temporal.importe_con_igv = (parseFloat($scope.fData.temporal.precio_unitario) * parseFloat($scope.fData.temporal.cantidad)).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.importe_sin_igv = ($scope.fData.temporal.importe_con_igv / 1.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
        $scope.fData.temporal.igv =($scope.fData.temporal.importe_sin_igv * 0.18).toFixed($scope.fConfigSys.num_decimal_precio_key);
      }
    }else{
      $scope.fData.temporal.importe_sin_igv = null;
      $scope.fData.temporal.importe_con_igv = null;
      $scope.fData.temporal.elemento = null;
    }
  } 
  $scope.mismoProveedor = function() { 
    $scope.fData.temporal = {
      cantidad: 1,
      importe_con_igv: null,
      importe_sin_igv: null,
      elemento: $scope.fArr.listaElementosProv[0],
      excluye_igv: 2
    };
    // console.log('mismo proveedor');
    $scope.gridOptions.data = [];
    $scope.fData.subtotal = null;
    $scope.fData.igv = null;
    $scope.fData.total = null;
    $scope.fData.isRegisterSuccess = false; 
    // $scope.metodos.generarNumeroFacturacion(); 
    // $scope.metodos.generarNumeroSerieCorrelativo(); 
    $('#temporalElemento').focus();
  }
  $scope.grabar = function() { 
    if($scope.fData.isRegisterSuccess){ 
      pinesNotifications.notify({ title: 'Advertencia.', text: 'La factura del proveedor ya fue registrada', type: 'warning', delay: 3000 });
      return false;
    }
    //if( $scope.fData.tipo_documento_identidad.destino == 1 ){ // empresa 
    if( $scope.fData.proveedor.razon_social == '' || $scope.fData.proveedor.razon_social == null || $scope.fData.proveedor.razon_social == undefined ){
      $scope.fData.num_documento = null;
      $('#numDocumento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No ha ingresado un proveedor', type: 'warning', delay: 3000 });
      return false;
    }
    //}
    $scope.fData.detalle = angular.copy($scope.gridOptions.data);
    if( $scope.fData.detalle.length < 1 ){ 
      $('#temporalElemento').focus();
      pinesNotifications.notify({ title: 'Advertencia.', text: 'No se ha agregado ningún elemento', type: 'warning', delay: 3000 }); 
      return false; 
    }
    blockUI.start('Ejecutando proceso...');
    FacturacionProvServices.sRegistrar($scope.fData).then(function (rpta) { 
      blockUI.stop();
      if(rpta.flag == 1){
        pTitle = 'OK!';
        pType = 'success'; 
        $scope.fData.isRegisterSuccess = true;
        $scope.fData.idfactprovanterior = rpta.idventa;
      }else if(rpta.flag == 0){
        var pTitle = 'Advertencia!';
        var pType = 'warning';
      }else{
        alert('Algo salió mal...');
      }
      pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
    });
  }
}]);

app.service("FacturacionProvServices",function($http, $q, handleBehavior) {
    return({ 
        sListarHistorialFactProveedor: sListarHistorialFactProveedor,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular
    });
    function sListarHistorialFactProveedor(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FacturacionProveedor/lista_ventas_historial",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FacturacionProveedor/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FacturacionProveedor/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"FacturacionProveedor/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.filter('mapInafecto', function() { 
  var inafectoHash = { 
    1: 'SI',
    2: 'NO'
  };
  return function(input) {
    if (!input){
      return '';
    } else {
      return inafectoHash[input];
    }
  };
});