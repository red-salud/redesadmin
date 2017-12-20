app.controller('HistorialCobroCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'CobroServices',
    'PlanServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		CobroServices,
    PlanServices
) {
   
  $scope.metodos = {}; // contiene todas las funciones 
  $scope.fData = {}; // contiene todas las variables de formulario 
	$scope.fArr = {}; // contiene todos los arrays generados por las funciones 
  $scope.mySelectionGrid = []; 
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

  $scope.tabs = [true, false];
  $scope.tab = function(index){ 
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

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
      { field: 'fecha_cobro', name: 'cob_fechCob', displayName: 'F. Cobro', minWidth: 90, enableFiltering: false },
      { field: 'vez_cobro', name: 'cob_vezCob', displayName: 'Vez Cobro', minWidth: 80 },
      { field: 'importe', name: 'cob_importe', displayName: 'Importe', minWidth: 80 },
      { field: 'facturado', type: 'object', name: 'facturado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, 
        enableColumnMenus: false, enableColumnMenu: false, 
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
          'cob.cob_id' : grid.columns[1].filters[0].term,
          'cont_numDoc' : grid.columns[2].filters[0].term, 
          "CONCAT( IF( cont_nom1 IS NULL OR COALESCE(cont_nom1,'') = '', '', CONCAT(COALESCE(cont_nom1,''),' ') ), IF( cont_nom2 IS NULL OR COALESCE(cont_nom2,'') = '', '', CONCAT(COALESCE(cont_nom2,''),' ') ), IF( cont_ape1 IS NULL OR COALESCE(cont_ape1,'') = '', '', CONCAT(COALESCE(cont_ape1,''),' ') ), IF( cont_ape2 IS NULL OR COALESCE(cont_ape2,'') = '', '', CONCAT(COALESCE(cont_ape2,''),' ') ) )" : grid.columns[3].filters[0].term,
          // "CONCAT(cont_nom1, ' ', cont_nom2, ' ', cont_ape1, ' ', cont_ape2)" : grid.columns[3].filters[0].term,
          'cert.cert_num' : grid.columns[4].filters[0].term, 
          'nombre_comercial_cli' : grid.columns[5].filters[0].term, 
          'pl.nombre_plan' : grid.columns[6].filters[0].term, 
          'cob_vezCob' : grid.columns[9].filters[0].term, 
          'cob_importe' : grid.columns[10].filters[0].term
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
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSide(true); 
  $scope.exportarCobroExcel = function() {
    
  }
}]); 
app.service("CobroServices",function($http, $q, handleBehavior) {
    return({
        sListarHistorialCobros: sListarHistorialCobros,
        sProcesarCobrosBoletajeMasivo: sProcesarCobrosBoletajeMasivo 
    });
    function sListarHistorialCobros(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cobro/listar_historial_cobros", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sProcesarCobrosBoletajeMasivo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"BoletajeMasivo/procesar_cobros_boleta_masivo", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});