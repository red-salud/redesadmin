app.controller('HistorialVentaCtrl', ['$scope', '$filter', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
    'ModalReporteFactory',
		'VentaServices',
    'ConceptoServices',
	function($scope, $filter, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
    ModalReporteFactory,
		VentaServices,
    SedeServices
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
    
  // SEDE 
  // $scope.metodos.listaSedes = function(myCallback) { 
  //   var myCallback = myCallback || function() { };
  //   SedeServices.sListarCbo().then(function(rpta) { 
  //     if( rpta.flag == 1){
  //       $scope.fArr.listaSedes = rpta.datos; 
  //       myCallback();
  //     } 
  //   });
  // }
  // var myCallback = function() { 
  //   $scope.fArr.listaSedes.splice(0,0,{ id : 'ALL', descripcion:'--TODOS--'}); 
  //   $scope.fBusqueda.sede = $scope.fArr.listaSedes[0]; 
  // }
  // $scope.metodos.listaSedes(myCallback); 


  $scope.tabs = [true, false];
  $scope.tab = function(index){ 
    angular.forEach($scope.tabs, function(i, v) {
      $scope.tabs[v] = false;
    });
    $scope.tabs[index] = true;
  }

  $scope.btnBuscar = function(){ 
    $scope.gridOptionsVen.enableFiltering = !$scope.gridOptionsVen.enableFiltering;
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
  $scope.gridOptionsVen = {
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
      { field: 'idmovimiento', name: 'mov.idmovimiento', displayName: 'ID', width: '65', visible: false },
      { field: 'cliente', name: 'cliente_persona_empresa', displayName: 'Cliente', minWidth: 180 },
      { field: 'numero_serie', name: 'mov.numero_serie', displayName: 'SERIE', width: '70' },
      { field: 'numero_correlativo', name: 'mov.numero_correlativo', displayName: 'CORRELATIVO', width: '100' },
      { field: 'fecha_emision', name: 'mov.fecha_emision', displayName: 'F. Emisión', minWidth: 100, enableFiltering: false,  sort: { direction: uiGridConstants.DESC} },
      { field: 'fecha_registro', name: 'mov.fecha_registro', displayName: 'F. Registro', minWidth: 100, enableFiltering: false, visible: false }, 
      { field: 'colaborador', name: 'colaborador', displayName: 'Usuario', minWidth: 160, visible: false },
      { field: 'concepto', name: 'cp.descripcion_con', displayName: 'Concepto', minWidth: 160 },
      { field: 'forma_pago', name: 'fp.descripcion_fp', displayName: 'Forma de Pago', minWidth: 120 },
      { field: 'moneda', name: 'mov.moneda', displayName: 'Moneda', minWidth: 76, enableFiltering: false },
      { field: 'subtotal', name: 'mov.subtotal', displayName: 'Subtotal', minWidth: 90 },
      { field: 'igv', name: 'mov.igv', displayName: 'IGV', minWidth: 80 },
      { field: 'total', name: 'mov.total', displayName: 'Total', minWidth: 80 } 
      // { field: 'estado', type: 'object', name: 'estado', displayName: 'ESTADO', width: '95', enableFiltering: false, enableSorting: false, enableColumnMenus: false, enableColumnMenu: false, 
      //     cellTemplate:'<div class="">' + 
      //       '<label tooltip-placement="left" tooltip="{{ COL_FIELD.labelText }}" class="label {{ COL_FIELD.claseLabel }} ml-xs">'+ 
      //       '<i class="fa {{ COL_FIELD.claseIcon }}"></i> {{COL_FIELD.labelText}} </label>'+ 
      //       '</div>' 
      // }
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
        console.log('ordenar changed');
        $scope.metodos.getPaginationServerSide(true);
      });
      gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
        paginationOptions.pageNumber = newPage;
        paginationOptions.pageSize = pageSize;
        paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
        console.log('paginate changed');
        $scope.metodos.getPaginationServerSide(true);
      });
      $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
        var grid = this.grid;
        paginationOptions.search = true; 
        paginationOptions.searchColumn = {
          'mov.idmovimiento' : grid.columns[1].filters[0].term,
          "CONCAT(COALESCE(cp.nombres_cli,''), ' ', COALESCE(cp.ap_paterno_cli,''), ' ', COALESCE(cp.ap_materno_cli,''), ' ', COALESCE(ce.razon_social_cli,''))" : grid.columns[2].filters[0].term,
          'mov.numero_serie' : grid.columns[3].filters[0].term, 
          'mov.numero_correlativo' : grid.columns[4].filters[0].term, 
          "CONCAT(col.nombres, ' ', col.apellidos)" : grid.columns[7].filters[0].term,
          'cp.descripcion_con' : grid.columns[8].filters[0].term, 
          'fp.descripcion_fp' : grid.columns[9].filters[0].term, 
          'mov.moneda' : grid.columns[10].filters[0].term,
          'mov.subtotal' : grid.columns[11].filters[0].term,
          'mov.igv' : grid.columns[12].filters[0].term,
          'mov.total' : grid.columns[13].filters[0].term 
        }
        console.log('filter changed');
        $scope.metodos.getPaginationServerSide();
      });
    }
  };
  paginationOptions.sortName = $scope.gridOptionsVen.columnDefs[2].name; 
  $scope.metodos.getPaginationServerSide = function(loader) { 
    if( loader ){
      blockUI.start('Procesando información...');
    }
    var arrParams = {
      paginate : paginationOptions,
      datos: $scope.fBusqueda 
    };
    VentaServices.sListarHistorialVentas(arrParams).then(function (rpta) { 
      if( rpta.datos.length == 0 ){
        rpta.paginate = { totalRows: 0 };
      }
      $scope.gridOptionsVen.totalItems = rpta.paginate.totalRows; 
      $scope.gridOptionsVen.data = rpta.datos; 
      if( loader ){
        blockUI.stop(); 
      }
    });
    $scope.mySelectionGrid = [];
  };
  $scope.metodos.getPaginationServerSide(true); 
}]); 