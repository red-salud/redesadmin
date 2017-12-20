app.controller('BoletajeMasivoCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'CobroServices',
  'PlanServices',
  'SerieServices',
  'TipoDocumentoMovServices',
  'ConceptoServices',
  function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  CobroServices,
  PlanServices,
  SerieServices,
  TipoDocumentoMovServices,
  ConceptoServices
  ) {
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    //$scope.mySelectionGrid = [];
    $scope.fBusqueda = {}; 

    $scope.fBusqueda.desde = $filter('date')(new Date(),'01-MM-yyyy');
    $scope.fBusqueda.desdeHora = '00';
    $scope.fBusqueda.desdeMinuto = '00';
    $scope.fBusqueda.hastaHora = 23;
    $scope.fBusqueda.hastaMinuto = 59;
    $scope.fBusqueda.hasta = $filter('date')(new Date(),'dd-MM-yyyy');
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
    }
    $scope.metodos.listaPlanes(myCallback); 
    // SERIES 
    $scope.metodos.listaSeries = function(myCallbackSeries) { 
      var myCallbackSeries = myCallbackSeries || function() { };
      SerieServices.sListarCbo().then(function(rpta) { 
        if( rpta.flag == 1){
          $scope.fArr.listaSeries = rpta.datos; 
          myCallbackSeries();
        } 
      });
    }
    // CONCEPTOS 
    $scope.metodos.listaConceptos = function(myCallbackConceptos) { 
      var myCallbackConceptos = myCallbackConceptos || function() { };
      var arrParams = { 
        'tipo_concepto': 'C' 
      }; 
      ConceptoServices.sListarCbo(arrParams).then(function(rpta) { 
        if( rpta.flag == 1){
          $scope.fArr.listaConceptos = rpta.datos; 
          $scope.fArr.listaConceptos.splice(0,0,{ id : '0', descripcion:'--Seleccione concepto--'}); 
          myCallbackConceptos();
        } 
      });
    }
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
    // TIPOS DE MONEDA 
    $scope.fArr.listaMoneda = [
      {'id' : 1, 'descripcion' : 'S/.', 'str_moneda' : 'S'},
      {'id' : 2, 'descripcion' : 'US$', 'str_moneda' : 'D'}
    ];
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
        { field: 'idcobro', name: 'cob_id', displayName: 'ID', width: 65 }, 
        { field: 'numero_doc_cont', name: 'cont_numDoc', displayName: 'N° DNI.', minWidth: 80 },
        { field: 'contratante', name: 'contratante', displayName: 'Contratante', minWidth: 200 },
        { field: 'num_certificado', name: 'cert_num', displayName: 'N° Cert.', minWidth: 100 },
        { field: 'canal_cliente', name: 'nombre_comercial_cli', displayName: 'Canal/Cliente', minWidth: 150 },
        { field: 'plan', name: 'nombre_plan', displayName: 'Plan', minWidth: 134 },
        { field: 'fecha_inicio_vig', name: 'cert_iniVig', displayName: 'F. Ini. Vig.', minWidth: 90, enableFiltering: false },
        { field: 'fecha_cobro', name: 'cob_fechCob', displayName: 'F. Cobro', minWidth: 90, enableFiltering: false, sort: { direction: uiGridConstants.DESC} },
        { field: 'vez_cobro', name: 'cob_vezCob', displayName: 'Vez Cobro', minWidth: 80 },
        { field: 'importe', name: 'cob_importe', displayName: 'Importe', minWidth: 80 } 
      ],
      onRegisterApi: function(gridApi) { 
        $scope.gridApi = gridApi;
        // gridApi.selection.on.rowSelectionChanged($scope,function(row){
        //   $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        // });
        // gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
        //   $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        // });
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
          //console.log('change pagination');
          $scope.metodos.getPaginationServerSide(true);
        });
        $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
          var grid = this.grid;
          paginationOptions.search = true; 
          paginationOptions.searchColumn = {
            'cob.cob_id' : grid.columns[0].filters[0].term,
            'cont_numDoc' : grid.columns[1].filters[0].term, 
            "CONCAT( IF( cont_nom1 IS NULL OR COALESCE(cont_nom1,'') = '', '', CONCAT(COALESCE(cont_nom1,''),' ') ), IF( cont_nom2 IS NULL OR COALESCE(cont_nom2,'') = '', '', CONCAT(COALESCE(cont_nom2,''),' ') ), IF( cont_ape1 IS NULL OR COALESCE(cont_ape1,'') = '', '', CONCAT(COALESCE(cont_ape1,''),' ') ), IF( cont_ape2 IS NULL OR COALESCE(cont_ape2,'') = '', '', CONCAT(COALESCE(cont_ape2,''),' ') ) )" : grid.columns[2].filters[0].term,
            // "CONCAT(cont_nom1, ' ', cont_nom2, ' ', cont_ape1, ' ', cont_ape2)" : grid.columns[3].filters[0].term,
            'cert.cert_num' : grid.columns[3].filters[0].term, 
            'nombre_comercial_cli' : grid.columns[4].filters[0].term, 
            'pl.nombre_plan' : grid.columns[5].filters[0].term, 
            //'pl.cob_fechCob' : grid.columns[7].filters[0].term, 
            'cob_vezCob' : grid.columns[8].filters[0].term, 
            'cob_importe' : grid.columns[9].filters[0].term
          }
          //console.log('buscar');
          $scope.metodos.getPaginationServerSide();
        });
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name; 
    $scope.metodos.getPaginationServerSide = function(loader) { 
      if( loader ){
        blockUI.start('Procesando información...');
      }
      $scope.fBusqueda.facturado = 2; // aún no esta facturado. 
      var arrParams = {
        paginate : paginationOptions,
        datos: $scope.fBusqueda 
      };
      CobroServices.sListarHistorialCobros(arrParams).then(function (rpta) { 
        if( rpta.datos.length == 0 ){
          rpta.paginate = { totalRows: 0 };
        }
        $scope.gridOptions.totalItems = rpta.paginate.totalRows; 
        $scope.gridOptions.data = rpta.datos; 
        if( loader ){
          blockUI.stop(); 
        }
      });
      //$scope.mySelectionGrid = [];
    };
    $scope.metodos.getPaginationServerSide(true); 
    $scope.convertirAComprobante = function() { 
      //blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'BoletajeMasivo/ver_popup_procesar_boletaje',
        size: 'md',
        backdrop: 'static',
        keyboard: false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          $scope.fData = {};
          $scope.titleForm = 'Proceso de Boletaje Masivo'; 
          // SERIE 
          var myCallBackSerie = function() { 
            $scope.fData.serie = $scope.fArr.listaSeries[0]; 
          } 
          $scope.metodos.listaSeries(myCallBackSerie); 
          // CONCEPTO 
          var myCallbackConceptos = function() { 
            $scope.fData.concepto = $scope.fArr.listaConceptos[0]; 
          }; 
          $scope.metodos.listaConceptos(myCallbackConceptos); 
          // TIPO DE DOCUMENTO 
          var myCallbackTDM = function() { 
            $scope.fData.tipo_documento_mov = $scope.fArr.listaTipoDocumentoMov[0]; 
          }
          $scope.metodos.listaTipoDocumentoMov(myCallbackTDM); 
          // MONEDA 
          $scope.fData.moneda = $scope.fArr.listaMoneda[0];
          $scope.convertirAComprobanteExec = function () { 
            //$scope.fBusqueda.facturado = 2; // aún no esta facturado. 
            blockUI.start('Procesando cobros. Espere por favor.'); 
            var arrParams = { 
              filters: $scope.fBusqueda, 
              datos: $scope.fData 
            }; 
            CobroServices.sProcesarCobrosBoletajeMasivo(arrParams).then(function(rpta) { 
              if(rpta.flag == 1){
                var pTitle = 'OK!';
                var pType = 'success';
                $scope.fData.fConsolidado = rpta.datos; 
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
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
        }
      });
    } 
}]); 