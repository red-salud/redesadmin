app.service("SeguimientoCitasServices",function($http, $q, handleBehavior) {
    return({
      sListarSeguimientoCita: sListarSeguimientoCita,
      sRegistrar: sRegistrar,
      sAnular: sAnular 
    });
    function sListarSeguimientoCita(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CitaSeguimiento/listar_seguimiento_citas", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CitaSeguimiento/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
    function sAnular(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CitaSeguimiento/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});