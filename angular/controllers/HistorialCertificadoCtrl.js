app.controller('HistorialCertificadoCtrl', ['$scope', '$filter', '$state', '$stateParams', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'CertificadoServices',
    'PlanServices',
	function($scope, $filter, $state, $stateParams, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		CertificadoServices,
    PlanServices
) {
   
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  $scope.mySelectionGrid = []; 
  $scope.mySelectionGridCD = []; 
  $scope.fBusqueda = {}; 
  // historial 
  $scope.fBusqueda.desde = $filter('date')(new Date(),'01-MM-yyyy');
  $scope.fBusqueda.desdeHora = '00';
  $scope.fBusqueda.desdeMinuto = '00';
  $scope.fBusqueda.hastaHora = 23;
  $scope.fBusqueda.hastaMinuto = 59;
  $scope.fBusqueda.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
  // historial detalle 
  $scope.fBusquedaCD = {}; 
  $scope.fBusquedaCD.desde = $filter('date')(new Date(),'01-MM-yyyy');
  $scope.fBusquedaCD.desdeHora = '00';
  $scope.fBusquedaCD.desdeMinuto = '00';
  $scope.fBusquedaCD.hastaHora = 23;
  $scope.fBusquedaCD.hastaMinuto = 59;
  $scope.fBusquedaCD.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
  // busqueda unitaria 
  $scope.fBusquedaUNIT = {};
  $scope.mostrarResultados = false;
  if( $stateParams.identifyNumDoc  ){
    $scope.fBusquedaUNIT.cuadro_busqueda = $stateParams.identifyNumDoc; 
    // GET NUMERO DOCUMENTO 
    $timeout(function() { 
      $scope.buscarFichas(); 
    }, 800);
  }
  // PLANES 
  $scope.metodos.listaPlanes = function(myCallback) { 
    var myCallback = myCallback || function() { };
    PlanServices.sListarCbo().then(function(rpta) { 
      if( rpta.flag == 1){
        $scope.fArr.listaPlanes = rpta.datos; 
        myCallback();
      } 
    });
  }
  var myCallback = function() { 
    $scope.fArr.listaPlanes.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
    $scope.fBusqueda.plan = $scope.fArr.listaPlanes[0]; 
    $scope.fBusquedaCD.plan = $scope.fArr.listaPlanes[0]; 
  }
  $scope.metodos.listaPlanes(myCallback); 
  $scope.tabs = [true, false];
  $scope.tab = function(index){ 
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }
  
  // CERTIFICADOS
  $scope.btnBuscar = function(){ 
    $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
    $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
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
    paginationPageSizes: [100, 500, 1000, 10000],
    paginationPageSize: 100,
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
      { field: 'idcertificado', name: 'cert.cert_id', displayName: 'ID', width: 65, visible: false, sort: { direction: uiGridConstants.DESC} }, 
      { field: 'num_certificado', name: 'cert_num', displayName: 'N° Cert.', minWidth: 70 },
      { field: 'numero_doc_cont', name: 'cont_numDoc', displayName: 'N° DNI.', minWidth: 70 },
      { field: 'contratante', name: 'contratante', displayName: 'Contratante', minWidth: 220 },
      { field: 'fecha_inicio_vig', name: 'cert_iniVig', displayName: 'F. Ini. Vig.', minWidth: 90, enableFiltering: false },
      { field: 'fecha_fin_vig', name: 'cert_finVig', displayName: 'F. Fin Vig.', minWidth: 90, enableFiltering: false },
      { field: 'canal_cliente', name: 'nombre_comercial_cli', displayName: 'Canal/Cliente', minWidth: 150, visible: false },
      { field: 'plan', name: 'nombre_plan', displayName: 'Plan', minWidth: 134 },
      { field: 'estado_atencion', name: 'estado_atencion', displayName: 'ESTADO ATE.', minWidth: 100, 
        cellTemplate:'<div class="ui-grid-cell-contents"> {{ COL_FIELD.descripcion }} </div>'
      },
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO CERT.', width: 90, enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
        cellTemplate:'<div class="">' + 
          '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
          '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
          '</div>' 
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
          'cert.cert_id' : grid.columns[1].filters[0].term,
          'cert.cert_num' : grid.columns[2].filters[0].term, 
          'cont_numDoc' : grid.columns[3].filters[0].term, 
          "CONCAT( IF( cont_nom1 IS NULL OR COALESCE(cont_nom1,'') = '', '', CONCAT(COALESCE(cont_nom1,''),' ') ), IF( cont_nom2 IS NULL OR COALESCE(cont_nom2,'') = '', '', CONCAT(COALESCE(cont_nom2,''),' ') ), IF( cont_ape1 IS NULL OR COALESCE(cont_ape1,'') = '', '', CONCAT(COALESCE(cont_ape1,''),' ') ), IF( cont_ape2 IS NULL OR COALESCE(cont_ape2,'') = '', '', CONCAT(COALESCE(cont_ape2,''),' ') ) )" : grid.columns[4].filters[0].term,
          'nombre_comercial_cli' : grid.columns[6].filters[0].term, 
          'pl.nombre_plan' : grid.columns[7].filters[0].term 
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
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
    };
    CertificadoServices.sListarHistorialCertificados(arrParams).then(function (rpta) { 
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
  $scope.btnVerAsegurados = function() {
    blockUI.start('Abriendo formulario...');
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Certificado/ver_popup_asegurados_de_certificado',
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fData = {};
        if( $scope.mySelectionGrid.length == 1 ){ 
          $scope.fData = $scope.mySelectionGrid[0];
        }else{
          alert('Seleccione una sola fila');
        }
        $scope.titleForm = 'Asegurados del Certificado N° ' + $scope.fData.num_certificado;
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        } 
        $scope.gridOptionsAseg = { 
          rowHeight: 26,
          minRowsToShow: 6,
          paginationPageSizes: [50, 100, 500, 1000],
          paginationPageSize: 50,
          enableGridMenu: true,
          enableRowSelection: true,
          enableSelectAll: true,
          enableFiltering: false,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [ 
            { field: 'idcertificadoasegurado', displayName: 'ID', width: 65, visible: false }, 
            { field: 'consecutivo', displayName: 'N° Consecutivo.', minWidth: 70 },
            { field: 'numero_doc_aseg', displayName: 'N° DNI.', minWidth: 75 },
            { field: 'asegurado', displayName: 'Asegurado', minWidth: 240 },
            { field: 'fecha_inicio_vig', displayName: 'F. Ini. Vig.', minWidth: 85, enableFiltering: false },
            { field: 'fecha_fin_vig', displayName: 'F. Fin Vig.', minWidth: 85, enableFiltering: false },
            { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: 95, enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
              cellTemplate:'<div class="">' + 
                '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
                '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
                '</div>' 
            }  
          ],
          onRegisterApi: function(gridApiAseg) { 
            $scope.gridApiAseg = gridApiAseg;
            gridApiAseg.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridAseg = gridApiAseg.selection.getSelectedRows(); 
            });
            gridApiAseg.selection.on.rowSelectionChangedBatch($scope,function(rows){
              $scope.mySelectionGridAseg = gridApiAseg.selection.getSelectedRows();
            });
          }
        };
        $scope.metodos.listarAseguradosDeCertificado = function(loader) {
          if( loader ){
            blockUI.start('Procesando información...');
          }
          var arrParams = { 
            datos: $scope.fData 
          };
          CertificadoServices.sListarAseguradosDeCertificado(arrParams).then(function (rpta) { 
            $scope.gridOptionsAseg.data = rpta.datos; 
            if( loader ){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionGridAseg = [];
        };
        $scope.metodos.listarAseguradosDeCertificado(true); 
        
      }
    });
  }
  $scope.btnVerCobros = function() {
    blockUI.start('Abriendo formulario...');
    $uibModal.open({ 
      templateUrl: angular.patchURLCI+'Certificado/ver_popup_cobros_de_certificado',
      size: 'lg',
      backdrop: 'static',
      keyboard:false,
      scope: $scope,
      controller: function ($scope, $uibModalInstance) { 
        blockUI.stop(); 
        $scope.fData = {};
        if( $scope.mySelectionGrid.length == 1 ){ 
          $scope.fData = $scope.mySelectionGrid[0];
        }else{
          alert('Seleccione una sola fila');
        }
        $scope.titleForm = 'Cobros del Certificado N° ' + $scope.fData.num_certificado;
        $scope.cancel = function () {
          $uibModalInstance.dismiss('cancel');
        } 
        var paginationOptionsCobros = {
          pageNumber: 1,
          firstRow: 0,
          pageSize: 50,
          sort: uiGridConstants.DESC,
          sortName: null,
          search: null
        };
        $scope.gridOptionsCobro = { 
          rowHeight: 30,
          minRowsToShow: 6,
          paginationPageSizes: [50, 100, 500, 1000],
          paginationPageSize: 50,
          useExternalPagination: true,
          useExternalSorting: true,
          useExternalFiltering : true,
          enableGridMenu: true,
          enableRowSelection: true,
          enableSelectAll: true,
          enableFiltering: true,
          enableFullRowSelection: true,
          multiSelect: false,
          columnDefs: [ 
            { field: 'idcobro', name: 'cob.cob_id', displayName: 'ID', visible: false, width: 50, sort: { direction: uiGridConstants.DESC} },
            { field: 'vez_cobro', name: 'cob_vezCob', displayName: 'Vez de Cobro', minWidth: 110 },
            { field: 'fecha_cobro', name: 'cob_fechCob', displayName: 'Fecha Cobro', minWidth: 100, enableFiltering: false }, 
            { field: 'medio_pago', name: 'descripcion_mp', displayName: 'Medio de Cobro', minWidth: 110 },
            { field: 'frecuencia_cobro', name: 'cobDet_frec', displayName: 'Frec. Cobro', minWidth: 110 },
            { field: 'fecha_inicio_cobert', name: 'cob_iniCobertura', displayName: 'Inicio Cobertura', minWidth: 100, enableFiltering: false }, 
            { field: 'fecha_fin_cobert', name: 'cob_finCobertura', displayName: 'Fin Cobertura', minWidth: 100, enableFiltering: false }, 
            { field: 'importe', name: 'cob_importe', displayName: 'Importe', minWidth: 90 } 
          ],
          onRegisterApi: function(gridApiCobro) { 
            $scope.gridApiCobro = gridApiCobro;
            gridApiCobro.selection.on.rowSelectionChanged($scope,function(row){
              $scope.mySelectionGridCobro = gridApiCobro.selection.getSelectedRows(); 
            });
            gridApiCobro.selection.on.rowSelectionChangedBatch($scope,function(rows){
              $scope.mySelectionGridCobro = gridApiCobro.selection.getSelectedRows();
            });

            $scope.gridApiCobro.core.on.sortChanged($scope, function(grid, sortColumns) { 
              if (sortColumns.length == 0) {
                paginationOptionsCobros.sort = null;
                paginationOptionsCobros.sortName = null;
              } else {
                paginationOptionsCobros.sort = sortColumns[0].sort.direction;
                paginationOptionsCobros.sortName = sortColumns[0].name;
              }
              $scope.metodos.getPaginationServerSideCobros(true);
            });
            gridApiCobro.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
              paginationOptionsCobros.pageNumber = newPage;
              paginationOptionsCobros.pageSize = pageSize;
              paginationOptionsCobros.firstRow = (paginationOptionsCobros.pageNumber - 1) * paginationOptionsCobros.pageSize;
              $scope.metodos.getPaginationServerSideCobros(true);
            });
            $scope.gridApiCobro.core.on.filterChanged( $scope, function(grid, searchColumns) {
              var grid = this.grid;
              paginationOptionsCobros.search = true; 
              paginationOptionsCobros.searchColumn = {
                'cob.cob_id' : grid.columns[1].filters[0].term,
                'cob_vezCob' : grid.columns[2].filters[0].term,
                'descripcion_mp' : grid.columns[4].filters[0].term,
                'cobDet_frec' : grid.columns[5].filters[0].term,
                'cob_importe' : grid.columns[8].filters[0].term 
              }; 
              $scope.metodos.getPaginationServerSideCobros();
            }); 
          }
        };
        paginationOptionsCobros.sortName = $scope.gridOptionsCobro.columnDefs[1].name;
        $scope.metodos.getPaginationServerSideCobros = function(loader) {
          if( loader ){
            blockUI.start('Procesando información...');
          }
          var arrParams = { 
            paginate : paginationOptionsCobros,
            datos: $scope.fData 
          };
          CertificadoServices.sListarCobrosDeCertificado(arrParams).then(function (rpta) { 
            $scope.gridOptionsCobro.totalItems = rpta.paginate.totalRows;
            $scope.gridOptionsCobro.data = rpta.datos; 
            if( loader ){
              blockUI.stop(); 
            }
          });
          $scope.mySelectionGridCobro = [];
        };
        $scope.metodos.getPaginationServerSideCobros(true); 
      }
    });
  }
  $scope.btnActivacionManual = function(vista,paramIdCertificado) {
    var pMensaje = '¿Realmente desea activar el certificado?';
    $bootbox.confirm(pMensaje, function(result) {
      if(result){ 
        if( vista == 'cert' ){
          var arrParams = {
            idcertificado: $scope.mySelectionGrid[0].idcertificado  
          };
        }
        if( vista == 'certdet' ){
          var arrParams = {
            idcertificado: $scope.mySelectionGridCD[0].idcertificado  
          };
        }
        if( vista == 'busqunit' ){
          var arrParams = {
            idcertificado: paramIdCertificado 
          };
        }
        blockUI.start('Procesando información...');
        CertificadoServices.sActivarCertificadoManual(arrParams).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            if(vista == 'cert'){
              $scope.metodos.getPaginationServerSide();
            }
            if( vista == 'certdet' ){
              $scope.metodos.getPaginationServerSideCD(true);
            }
            if( vista == 'busqunit' ){
              $scope.buscarFichas();
            }
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
  $scope.btnDeshacerActivacionManual = function(vista,paramIdCertificado) { 
    var paramIdCertificado = paramIdCertificado || null; 
    var pMensaje = '¿Realmente desea deshacer la activación del certificado?';
    $bootbox.confirm(pMensaje, function(result) {
      if(result){ 
        if( vista == 'cert' ){
          var arrParams = {
            idcertificado: $scope.mySelectionGrid[0].idcertificado  
          };
        } 
        if( vista == 'certdet' ){
          var arrParams = {
            idcertificado: $scope.mySelectionGridCD[0].idcertificado  
          };
        } 
        if( vista == 'busqunit' ){
          var arrParams = {
            idcertificado: paramIdCertificado 
          };
        }
        blockUI.start('Procesando información...');
        CertificadoServices.sDeshacerActivarCertificadoManual(arrParams).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            if(vista == 'cert'){
              $scope.metodos.getPaginationServerSide();
            }
            if( vista == 'certdet' ){
              $scope.metodos.getPaginationServerSideCD(true);
            }
            if( vista == 'busqunit' ){
              $scope.buscarFichas();
            }
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
  // END CERTIFICADOS

  // CERTIFICADOS DETALLE 
  $scope.btnBuscarCD = function(){ 
    $scope.gridOptionsCD.enableFiltering = !$scope.gridOptionsCD.enableFiltering;
    $scope.gridApiCD.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
  };
  var paginationOptionsCD = {
    pageNumber: 1,
    firstRow: 0,
    pageSize: 100,
    sort: uiGridConstants.DESC,
    sortName: null,
    search: null
  };
  $scope.gridOptionsCD = {
    rowHeight: 30,
    paginationPageSizes: [100, 500, 1000, 10000],
    paginationPageSize: 100,
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
      { field: 'idcertificadoasegurado', name: 'cas.certase_id', displayName: 'ID', width: 65, visible: false, sort: { direction: uiGridConstants.DESC} }, 
      { field: 'num_certificado', name: 'cert_num', displayName: 'N° Cert.', minWidth: 70 },
      { field: 'numero_doc_cont', name: 'cont_numDoc', displayName: 'N° DNI. Cont.', minWidth: 92 },
      { field: 'contratante', name: 'contratante', displayName: 'Contratante', minWidth: 205 },
      { field: 'numero_doc_aseg', name: 'aseg_numDoc', displayName: 'N° DNI. Aseg.', minWidth: 95 },
      { field: 'asegurado', name: 'asegurado', displayName: 'Asegurado', minWidth: 210 },
      { field: 'fecha_inicio_vig', name: 'cert_iniVig', displayName: 'F. Ini. Vig.', minWidth: 90, enableFiltering: false },
      { field: 'fecha_fin_vig', name: 'cert_finVig', displayName: 'F. Fin Vig.', minWidth: 90, enableFiltering: false, visible: false },
      { field: 'canal_cliente', name: 'nombre_comercial_cli', displayName: 'Canal/Cliente', minWidth: 130, visible: false },
      { field: 'plan', name: 'nombre_plan', displayName: 'Plan', minWidth: 118 },
      { field: 'estado_atencion', name: 'estado_atencion', displayName: 'ESTADO ATE.', minWidth: 110, 
        cellTemplate:'<div class="ui-grid-cell-contents"> {{ COL_FIELD.descripcion }} </div>'
      },
      { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: 95, enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
        cellTemplate:'<div class="">' + 
          '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
          '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
          '</div>' 
      } 
    ],
    onRegisterApi: function(gridApiCD) { 
      $scope.gridApiCD = gridApiCD;
      gridApiCD.selection.on.rowSelectionChanged($scope,function(row){
        $scope.mySelectionGridCD = gridApiCD.selection.getSelectedRows();
        console.log($scope.mySelectionGridCD,'$scope.mySelectionGridCD');
      });
      gridApiCD.selection.on.rowSelectionChangedBatch($scope,function(rows){
        $scope.mySelectionGridCD = gridApiCD.selection.getSelectedRows();
      });
      $scope.gridApiCD.core.on.sortChanged($scope, function(grid, sortColumns) { 
        if (sortColumns.length == 0) {
          paginationOptionsCD.sort = null;
          paginationOptionsCD.sortName = null;
        } else {
          paginationOptionsCD.sort = sortColumns[0].sort.direction;
          paginationOptionsCD.sortName = sortColumns[0].name;
        }
        $scope.metodos.getPaginationServerSideCD(true);
      });
      gridApiCD.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptionsCD.pageNumber = newPage;
        paginationOptionsCD.pageSize = pageSize;
        paginationOptionsCD.firstRow = (paginationOptionsCD.pageNumber - 1) * paginationOptionsCD.pageSize;
        $scope.metodos.getPaginationServerSideCD(true);
      });
      $scope.gridApiCD.core.on.filterChanged( $scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptionsCD.search = true; 
        paginationOptionsCD.searchColumn = {
          'cas.certase_id' : grid.columns[1].filters[0].term,
          'cert.cert_num' : grid.columns[2].filters[0].term, 
          'cont_numDoc' : grid.columns[3].filters[0].term, 
          "CONCAT( IF( cont_nom1 IS NULL OR COALESCE(cont_nom1,'') = '', '', CONCAT(COALESCE(cont_nom1,''),' ') ), IF( cont_nom2 IS NULL OR COALESCE(cont_nom2,'') = '', '', CONCAT(COALESCE(cont_nom2,''),' ') ), IF( cont_ape1 IS NULL OR COALESCE(cont_ape1,'') = '', '', CONCAT(COALESCE(cont_ape1,''),' ') ), IF( cont_ape2 IS NULL OR COALESCE(cont_ape2,'') = '', '', CONCAT(COALESCE(cont_ape2,''),' ') ) )" : grid.columns[4].filters[0].term,
          'aseg_numDoc' : grid.columns[5].filters[0].term, 
          "CONCAT( IF( aseg_nom1 IS NULL OR COALESCE(aseg_nom1,'') = '', '', CONCAT(COALESCE(aseg_nom1,''),' ') ), IF( aseg_nom2 IS NULL OR COALESCE(aseg_nom2,'') = '', '', CONCAT(COALESCE(aseg_nom2,''),' ') ), IF( aseg_ape1 IS NULL OR COALESCE(aseg_ape1,'') = '', '', CONCAT(COALESCE(aseg_ape1,''),' ') ), IF( aseg_ape2 IS NULL OR COALESCE(aseg_ape2,'') = '', '', CONCAT(COALESCE(aseg_ape2,''),' ') ) )" : grid.columns[6].filters[0].term,
          'nombre_comercial_cli' : grid.columns[7].filters[0].term, 
          'pl.nombre_plan' : grid.columns[8].filters[0].term 
        }
        $scope.metodos.getPaginationServerSideCD();
      });
    }
  };
  paginationOptionsCD.sortName = $scope.gridOptionsCD.columnDefs[0].name; 
  $scope.metodos.getPaginationServerSideCD = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptionsCD,
      datos: $scope.fBusquedaCD 
    };
    CertificadoServices.sListarHistorialCertificadosDetalle(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsCD.totalItems = rpta.paginate.totalRows; 
      $scope.gridOptionsCD.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGridCD = [];
  };
  $scope.metodos.getPaginationServerSideCD(true); 
  // END CERTIFICADOS DETALLE

  // BUSQUEDA UNITARIA 
  $scope.oneAtATime = true;
  $scope.buscarFichas = function() { 
    var arrParams = { 
      'cuadro_busqueda': $scope.fBusquedaUNIT.cuadro_busqueda 
    }; 
    blockUI.start('Procesando información...'); 
    CertificadoServices.sBuscarFichasCertificados(arrParams).then(function (rpta) {
      if( rpta.flag == 1 ){
        $scope.fArr.listaCertificados = rpta.datos; 
        $scope.mostrarResultados = 1;
      }else{
        $scope.mostrarResultados = 2;
        pinesNotifications.notify({ title: 'Advertencia', text: rpta.message, type: 'warning', delay: 3000 }); 
      }
      blockUI.stop(); 
    });
  }
  $scope.$watch('fBusquedaUNIT.cuadro_busqueda', function(newValue,oldValue){ 
    if( oldValue == newValue ){
      return false; 
    }
    if( newValue != oldValue){ 
      $scope.mostrarResultados = null;
    }
  });
  $scope.reservarCita = function(fAseg) { 
    var url = $state.href('app.reserva-citas', {identifyNumDoc: fAseg.numero_doc_aseg }); 
    window.open(url,'_self'); 
  } 
  $scope.editarInfoAsegurado = function(fAseg) {
    var url = $state.href('app.reserva-citas', {identifyNumDoc: fAseg.numero_doc_aseg, editable: 'edit' }); 
    window.open(url,'_self'); 
  }
}]); 
app.service("CertificadoServices",function($http, $q, handleBehavior) {
    return({
        sListarHistorialCertificados: sListarHistorialCertificados, 
        sListarHistorialCertificadosDetalle: sListarHistorialCertificadosDetalle, 
        sListarAseguradosDeCertificado: sListarAseguradosDeCertificado, 
        sListarCertificadosDeAsegurados: sListarCertificadosDeAsegurados,
        sListarCobrosDeCertificado: sListarCobrosDeCertificado, 
        sBuscarFichasCertificados: sBuscarFichasCertificados, 
        sActivarCertificadoManual: sActivarCertificadoManual,
        sDeshacerActivarCertificadoManual: sDeshacerActivarCertificadoManual 
    });
    function sListarHistorialCertificados(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/listar_historial_certificados", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarHistorialCertificadosDetalle(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/listar_historial_certificados_detalle", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarAseguradosDeCertificado(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/listar_asegurados_de_certificado", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCertificadosDeAsegurados(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/listar_certificados_de_asegurados", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCobrosDeCertificado(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/listar_cobros_de_certificado", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sBuscarFichasCertificados(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/buscar_fichas_certificados", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sActivarCertificadoManual(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/activar_certificado_manual",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sDeshacerActivarCertificadoManual(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Certificado/deshacer_activar_certificado_manual",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});