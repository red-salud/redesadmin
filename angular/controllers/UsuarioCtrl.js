app.controller('UsuarioCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'UsuarioFactory',
  'UsuarioServices',
  'ColaboradorFactory',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  UsuarioFactory,
  UsuarioServices,
  ColaboradorFactory
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = [];
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
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 100,
      sort: uiGridConstants.DESC,
      sortName: null,
      search: null
    };
    $scope.gridOptions = {
      rowHeight: 30,
      paginationPageSizes: [100, 500, 1000],
      paginationPageSize: 100,
      useExternalPagination: true,
      useExternalSorting: true,
      useExternalFiltering : true,
      enableGridMenu: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableRowSelection: true,
      enableFullRowSelection: true,
      multiSelect: false,
      columnDefs: [ 
        { field: 'idusuario', name: 'idusuario', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
        { field: 'tipo_usuario', name: 'tu.descripcion_tu', width: 160, displayName: 'Tipo Usuario', 
          cellTemplate:'<div class="ui-grid-cell-contents text-left ">' + '{{ COL_FIELD.descripcion }}</div>' }, 
        { field: 'username', name: 'username', displayName: 'Username', minWidth: 100 },
        { field: 'ult_inicio_sesion', name: 'ultimo_inicio_sesion', displayName: 'Ult. Actividad', minWidth: 100 } 
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
            'u.idusuario' : grid.columns[1].filters[0].term,
            'tu.descripcion_tu' : grid.columns[2].filters[0].term,
            'u.username' : grid.columns[3].filters[0].term,
            'u.ultimo_inicio_sesion' : grid.columns[4].filters[0].term
          }
          $scope.metodos.getPaginationServerSide();
        });
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
    $scope.metodos.getPaginationServerSide = function(loader) {
      if( loader ){
        blockUI.start('Procesando información...');
      }
      var arrParams = {
        paginate : paginationOptions
      };
      UsuarioServices.sListar(arrParams).then(function (rpta) { 
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
      UsuarioFactory.regUsuarioModal(arrParams); 
    }
    $scope.btnEditar = function() { 
      var arrParams = {
        'metodos': $scope.metodos,
        'mySelectionGrid': $scope.mySelectionGrid,
        'fArr': $scope.fArr,
        callback: function() {      
        }     
      }
      UsuarioFactory.editUsuarioModal(arrParams); 
    } 
    $scope.btnAnular = function() { 
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            idusuario: $scope.mySelectionGrid[0].idusuario 
          };
          blockUI.start('Procesando información...');
          UsuarioServices.sAnular(arrParams).then(function (rpta) {
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

app.service("UsuarioServices",function($http, $q, handleBehavior) {
    return({
        sListar: sListar,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,
        sListarTipoUsuarioCbo: sListarTipoUsuarioCbo
    });
    function sListar(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/listar_usuario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }  
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }   
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }       
    function sListarTipoUsuarioCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Usuario/listar_tipo_usuario_cbo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("UsuarioFactory", function($uibModal, pinesNotifications, blockUI, UsuarioServices, ProveedorServices, ColaboradorServices, uiGridConstants) { 
  var interfaz = {
    regUsuarioModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Usuario/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          console.log($scope.fData,'$scope.fData');
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          //var myCallbackProv
          $scope.accion = 'reg';
          $scope.titleForm = 'Registro de Usuario';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          var myCallBackTS = function() { 
            $scope.fArr.listaTipoUsuario.splice(0,0,{ id : '0', descripcion:'--Seleccione tipo de usuario--'}); 
            $scope.fData.tipo_usuario = $scope.fArr.listaTipoUsuario[0]; 
          }
          $scope.metodos.listaTipoUsuario(myCallBackTS); 
          
          $scope.asociarProveedor = function() {
            blockUI.start('Abriendo formulario...');
            $uibModal.open({ 
              templateUrl: angular.patchURLCI+'Usuario/ver_popup_asociar_proveedor',
              size: 'md',
              scope: $scope,
              backdrop: 'static',
              keyboard:false,
              controller: function ($scope, $uibModalInstance, arrParams) {
                blockUI.stop(); 
                $scope.fDataProveedor = {};
                // console.log($scope.fData,'$scope.fData');
                $scope.metodos = arrParams.metodos;
                $scope.fArr = arrParams.fArr;
                $scope.titleForm = 'Asociar Proveedor';
                $scope.cancel = function () {
                  $uibModalInstance.dismiss('cancel');
                } 

                var paginationOptionsProve = {
                  pageNumber: 1,
                  firstRow: 0,
                  pageSize: 100,
                  sort: uiGridConstants.DESC,
                  sortName: null,
                  search: null
                };
                $scope.gridOptionsProveedor = {
                  rowHeight: 30,
                  paginationPageSizes: [100, 500, 1000],
                  paginationPageSize: 100,
                  useExternalPagination: true,
                  useExternalSorting: true,
                  useExternalFiltering : true,
                  enableGridMenu: true,
                  enableSelectAll: true,
                  enableFiltering: true,
                  enableRowSelection: true,
                  enableFullRowSelection: true,
                  multiSelect: false,
                  columnDefs: [ 
                    { field: 'idproveedor', name: 'pr.idproveedor', displayName: 'ID', width: '60',  sort: { direction: uiGridConstants.DESC} },
                    { field: 'tipo_proveedor', name: 'tpr.descripcion_tpr', type: 'object', displayName: 'Tipo de Proveedor', minWidth: 140,
                      cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>', visible: false 
                    },
                    { field: 'tipo_documento_identidad', name: 'tdi.descripcion_tdi', type: 'object', displayName: 'Tipo de Doc.', minWidth: 120, visible: false, 
                      cellTemplate:'<div class="ui-grid-cell-contents text-center ">{{ COL_FIELD.descripcion }}</div>', visible: false 
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
                  onRegisterApi: function(gridApi) { 
                    $scope.gridApi = gridApi;
                    gridApi.selection.on.rowSelectionChanged($scope,function(row){ 
                      $scope.mySelectionGridAP = gridApi.selection.getSelectedRows();
                      // SELECCIONAR PROVEEDOR 
                      $scope.fData.proveedor = $scope.mySelectionGridAP[0].nombre_comercial; 
                      $scope.fData.idproveedor = $scope.mySelectionGridAP[0].idproveedor; 
                      $uibModalInstance.dismiss('cancel');
                    });
                    $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) { 
                      if (sortColumns.length == 0) {
                        paginationOptionsProve.sort = null;
                        paginationOptionsProve.sortName = null;
                      } else {
                        paginationOptionsProve.sort = sortColumns[0].sort.direction;
                        paginationOptionsProve.sortName = sortColumns[0].name;
                      }
                      $scope.metodos.getPaginationServerSideAP(true);
                    });
                    gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                      paginationOptionsProve.pageNumber = newPage;
                      paginationOptionsProve.pageSize = pageSize;
                      paginationOptionsProve.firstRow = (paginationOptionsProve.pageNumber - 1) * paginationOptionsProve.pageSize;
                      $scope.metodos.getPaginationServerSideAP(true);
                    });
                    $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
                      var grid = this.grid;
                      paginationOptionsProve.search = true; 
                      paginationOptionsProve.searchColumn = {
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
                      $scope.metodos.getPaginationServerSideAP();
                    });
                  }
                }
                $scope.metodos.getPaginationServerSideAP = function(loader) { 
                  if( loader ){
                    blockUI.start('Procesando información...');
                  }
                  var arrParams = {
                    paginate : paginationOptionsProve
                  };
                  ProveedorServices.sListarProveedoresSinUsuario(arrParams).then(function (rpta) { 
                    if( rpta.datos.length == 0 ){
                      rpta.paginate = { totalRows: 0 };
                    }
                    $scope.gridOptionsProveedor.totalItems = rpta.paginate.totalRows; 
                    $scope.gridOptionsProveedor.data = rpta.datos; 
                    if( loader ){
                      blockUI.stop(); 
                    }
                  });
                  $scope.mySelectionGridAP = [];
                };
                $scope.metodos.getPaginationServerSideAP(true); 
              },
              resolve: {
                arrParams: function() {
                  return arrParams;
                }
              }
            });
          }
          $scope.asociarColaborador = function() {
            blockUI.start('Abriendo formulario...');
            $uibModal.open({ 
              templateUrl: angular.patchURLCI+'Usuario/ver_popup_asociar_colaborador',
              size: 'md',
              scope: $scope,
              backdrop: 'static',
              keyboard:false,
              controller: function ($scope, $uibModalInstance, arrParams) {
                blockUI.stop(); 
                $scope.metodos = arrParams.metodos;
                $scope.fArr = arrParams.fArr;
                $scope.titleForm = 'Asociar Colaborador';
                $scope.cancel = function () {
                  $uibModalInstance.dismiss('cancel');
                } 
                var paginationOptionsCol = {
                  pageNumber: 1,
                  firstRow: 0,
                  pageSize: 100,
                  sort: uiGridConstants.DESC,
                  sortName: null,
                  search: null
                };
                $scope.gridOptionsColaborador = {
                  rowHeight: 30,
                  paginationPageSizes: [100, 500, 1000],
                  paginationPageSize: 100,
                  useExternalPagination: true,
                  useExternalSorting: true,
                  useExternalFiltering : true,
                  enableGridMenu: true,
                  enableSelectAll: true,
                  enableFiltering: true,
                  enableRowSelection: true,
                  enableFullRowSelection: true,
                  multiSelect: false,
                  columnDefs: [ 
                    { field: 'id', name: 'idcolaborador', displayName: 'ID', width: '75',  sort: { direction: uiGridConstants.DESC} },
                    { field: 'nombres', name: 'nombres_col', displayName: 'Nombres', minWidth: 140 },
                    { field: 'apellidos', name: 'apellido_paterno', displayName: 'Apellidos', minWidth: 140 },
                    { field: 'num_documento', name: 'num_documento_col', displayName: 'N° Documento', minWidth: 100 }, 
                    { field: 'celular', name: 'celular_col', displayName: 'Celular', minWidth: 120, visible:false }, 
                    { field: 'email', name: 'correo_laboral', displayName: 'Correo', minWidth: 180, visible:false }, 
                    { field: 'fecha_nacimiento', name: 'fecha_nacimiento_col', displayName: 'Fecha de Nacimiento', minWidth: 100, visible:false }, 
                    { field: 'tipo_usuario', type: 'object', name: 'tipo_usuario', displayName: 'Tipo usuario', minWidth: 100, visible:false, 
                      cellTemplate:'<div class="ui-grid-cell-contents text-center "><label class="label bg-primary block">{{ COL_FIELD.descripcion }}</label></div>' 
                    } 
                  ],
                  onRegisterApi: function(gridApi) { 
                    $scope.gridApi = gridApi;
                    gridApi.selection.on.rowSelectionChanged($scope,function(row){ 
                      $scope.mySelectionGridAC = gridApi.selection.getSelectedRows();
                      // SELECCIONAR COLABORADOR  
                      $scope.fData.colaborador = $scope.mySelectionGridAC[0].nombres + $scope.mySelectionGridAC[0].apellidos; 
                      $scope.fData.idcolaborador = $scope.mySelectionGridAC[0].id; 
                      $uibModalInstance.dismiss('cancel');
                    });
                    $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) { 
                      if (sortColumns.length == 0) {
                        paginationOptionsCol.sort = null;
                        paginationOptionsCol.sortName = null;
                      } else {
                        paginationOptionsCol.sort = sortColumns[0].sort.direction;
                        paginationOptionsCol.sortName = sortColumns[0].name;
                      }
                      $scope.metodos.getPaginationServerSideAP(true);
                    });
                    gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                      paginationOptionsCol.pageNumber = newPage;
                      paginationOptionsCol.pageSize = pageSize;
                      paginationOptionsCol.firstRow = (paginationOptionsCol.pageNumber - 1) * paginationOptionsCol.pageSize;
                      $scope.metodos.getPaginationServerSideAP(true);
                    });
                    $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) { 
                      var grid = this.grid;
                      paginationOptionsCol.search = true; 
                      paginationOptionsCol.searchColumn = { 
                        'co.idcolaborador' : grid.columns[1].filters[0].term,
                        'co.nombres' : grid.columns[2].filters[0].term,
                        'co.apellidos' : grid.columns[3].filters[0].term,
                        'co.num_documento' : grid.columns[4].filters[0].term,
                        'co.telefono' : grid.columns[5].filters[0].term,
                        'co.email' : grid.columns[6].filters[0].term,
                        'co.fecha_nacimiento' : grid.columns[7].filters[0].term,
                        'tu.descripcion_tu' : grid.columns[8].filters[0].term 
                      }
                      $scope.metodos.getPaginationServerSideAP();
                    });
                  }
                }
                $scope.metodos.getPaginationServerSideAP = function(loader) { 
                  if( loader ){
                    blockUI.start('Procesando información...');
                  }
                  var arrParams = {
                    paginate : paginationOptionsCol
                  };
                  ColaboradorServices.sListarColaboradoresSinUsuario(arrParams).then(function (rpta) { 
                    if( rpta.datos.length == 0 ){
                      rpta.paginate = { totalRows: 0 };
                    }
                    $scope.gridOptionsColaborador.totalItems = rpta.paginate.totalRows; 
                    $scope.gridOptionsColaborador.data = rpta.datos; 
                    if( loader ){
                      blockUI.stop(); 
                    }
                  });
                  $scope.mySelectionGridAC = [];
                };
                $scope.metodos.getPaginationServerSideAP(true); 
              },
              resolve: {
                arrParams: function() {
                  return arrParams;
                }
              }
            });
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            console.log('aqui');
            UsuarioServices.sRegistrar($scope.fData).then(function (rpta) {    
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
              //arrParams.callback($scope.fData,rpta);
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
    editUsuarioModal: function (arrParams) {
      console.log(arrParams,'arrParams');
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Usuario/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr; 
          $scope.accion = 'edit';
          // console.log(arrParams,'arrParams.mySelectionGrid');
          if( arrParams.mySelectionGrid.length == 1 ){ 
            $scope.fData = arrParams.mySelectionGrid[0];
            // console.log($scope.fData ,'$scope.fData ');
          }else{
            alert('Seleccione una sola fila');
          }

          $scope.titleForm = 'Edición de Usuario';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          //BINDEO TIPO USUARIO
          var myCallBackTS = function() { 
            var objIndex = $scope.fArr.listaTipoUsuario.filter(function(obj) { 

              return obj.id == $scope.fData.tipo_usuario.id;
            }).shift(); 
            $scope.fData.tipo_usuario = objIndex; 
          }
          $scope.metodos.listaTipoUsuario(myCallBackTS); 
          $scope.modoEdit = false;
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            UsuarioServices.sEditar($scope.fData).then(function (rpta) {
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
              arrParams.callback($scope.fData);
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

