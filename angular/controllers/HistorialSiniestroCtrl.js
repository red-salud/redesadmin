app.controller('HistorialSiniestroCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'SiniestroServices',
    'PlanServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		SiniestroServices,
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
      { field: 'idsiniestro', name: 'idsiniestro', displayName: 'ID', width: 55, enableFiltering: false, visible:false }, 
      { field: 'num_orden_atencion', name: 'num_orden_atencion', displayName: 'N° Orden', width: 80 }, 
      { field: 'mes_atencion', name: 'fecha_atencion', displayName: 'Mes', minWidth: 80, enableFiltering: false }, 
      { field: 'fecha_atencion', name: 'fecha_atencion', displayName: 'Fecha de Atención', minWidth: 120, enableFiltering: false }, 
      { field: 'asegurado', name: 'asegurado', displayName: 'Afiliado', minWidth: 200 }, 
      { field: 'aseg_num_doc', name: 'aseg_numDoc', displayName: 'N° Doc. Afiliado', minWidth: 110 }, 
      { field: 'aseg_telefono', name: 'aseg_telf', displayName: 'Tel. Afiliado', minWidth: 100 }, 
      { field: 'especialidad', name: 'nombre_esp', displayName: 'Especialidad', minWidth: 130 }, 
      { field: 'proveedor', name: 'nombre_comercial_pr', displayName: 'Establecimiento', minWidth: 150 },
      { field: 'cliente', name: 'nombre_comercial_cli', displayName: 'Cliente', minWidth: 120 },
      { field: 'estado_obj', type: 'object', name: 'estado_obj', displayName: 'ESTADO', width: 120, enableFiltering: false, enableSorting: false, 
        enableColumnMenus: false, enableColumnMenu: false, cellTemplate:'<div class="">' + 
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
          'sin.idsiniestro' : grid.columns[1].filters[0].term,
          'sin.num_orden_atencion' : grid.columns[2].filters[0].term, 
          "CONCAT( IF( aseg_nom1 IS NULL OR COALESCE(aseg_nom1,'') = '', '', CONCAT(COALESCE(aseg_nom1,''),' ') ), IF( aseg_nom2 IS NULL OR COALESCE(aseg_nom2,'') = '', '', CONCAT(COALESCE(aseg_nom2,''),' ') ), IF( aseg_ape1 IS NULL OR COALESCE(aseg_ape1,'') = '', '', CONCAT(COALESCE(aseg_ape1,''),' ') ), IF( aseg_ape2 IS NULL OR COALESCE(aseg_ape2,'') = '', '', CONCAT(COALESCE(aseg_ape2,''),' ') ) )" : grid.columns[4].filters[0].term,
          'ase.aseg_numDoc' : grid.columns[5].filters[0].term, 
          'ase.aseg_telf' : grid.columns[6].filters[0].term, 
          'esp.nombre_esp' : grid.columns[7].filters[0].term, 
          'nombre_comercial_pr' : grid.columns[8].filters[0].term 
          //'nombre_comercial_cli' : grid.columns[11].filters[0].term 
        }; 
        //console.log(grid.columns,'grid.columns');
        //console.log(paginationOptions.searchColumn,'paginationOptions.searchColumn'); 
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
    SiniestroServices.sListarHistorialSiniestros(arrParams).then(function (rpta) { 
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
}]); 
app.service("SiniestroServices",function($http, $q, handleBehavior) {
    return({
        sListarHistorialSiniestros: sListarHistorialSiniestros
    });
    function sListarHistorialSiniestros(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Siniestro/listar_historial_siniestros", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});