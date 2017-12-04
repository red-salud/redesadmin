app.service("PlanServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo, 
        sListarCondicionesPlan: sListarCondicionesPlan 
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Plan/listar_plan_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarCondicionesPlan(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Plan/listar_condiciones_de_este_plan", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error)); 
    }
});
app.factory("PlanFactory", function($uibModal, pinesNotifications, blockUI, PlanServices) { 
  var interfaz = { 
    verCondicionesPlanModal: function(arrParams) { 
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Plan/ver_popup_condiciones_plan',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Condiciones del Plan'; 

          // $scope.gridOptionsCP = { 
          //   rowHeight: 30,
          //   paginationPageSizes: [100, 500, 1000],
          //   paginationPageSize: 100,
          //   enableGridMenu: true,
          //   enableRowSelection: true,
          //   enableSelectAll: true,
          //   enableFiltering: false,
          //   enableFullRowSelection: true,
          //   multiSelect: false,
          //   columnDefs: [ 
          //     { field: 'idplandetalle', name: 'idplandetalle', displayName: 'ID', width: 50 },
          //     { field: 'nombre_var', name: 'nombre_var', displayName: 'CONDICIÓN', minWidth: 140 },
          //     { field: 'texto_web', name: 'texto_web', displayName: 'DESCRIPCIÓN', minWidth: 300 } 
          //   ],
          //   onRegisterApi: function(gridApiCP) { 
          //     $scope.gridApiCP = gridApiCP; 
          //   }
          // };
          $scope.metodos.getListaCondicionesPlan = function(loader) {
            if( loader ){
              blockUI.start('Procesando información...');
            }
            PlanServices.sListarCondicionesPlan(arrParams.fAsegurado).then(function (rpta) { 
              $scope.fArr.listaCondicionesPlan = rpta.datos; 
              if( loader ){
                blockUI.stop(); 
              }
            });
          };
          $scope.metodos.getListaCondicionesPlan(true); 
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          // $scope.aceptar = function () { 
          //   blockUI.start('Procesando información...');
          //   FormaPagoServices.sRegistrar($scope.fData).then(function (rpta) {
          //     if(rpta.flag == 1){
          //       var pTitle = 'OK!';
          //       var pType = 'success';
          //       $uibModalInstance.dismiss('cancel');
          //       if(typeof $scope.metodos.getPaginationServerSide == 'function'){ 
          //         $scope.metodos.getPaginationServerSide(true);
          //       }
          //     }else if(rpta.flag == 0){
          //       var pTitle = 'Error!';
          //       var pType = 'danger';
          //     }else{
          //       alert('Error inesperado');
          //     }
          //     blockUI.stop(); 
          //     pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });
          //   });
          // } 
        },
        resolve: {
          arrParams: function() {
            return arrParams;
          }
        }
      });
    },
    regPlanModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'FormaPago/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr;
          $scope.titleForm = 'Registro de Forma Pago';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }

          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            FormaPagoServices.sRegistrar($scope.fData).then(function (rpta) {
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
    editPlanModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'FormaPago/ver_popup_formulario',
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr; 
          if( arrParams.mySelectionGrid.length == 1 ){ 
            $scope.fData = arrParams.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de Forma Pago';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            blockUI.start('Procesando información...');
            FormaPagoServices.sEditar($scope.fData).then(function (rpta) {
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
});