app.service("CargoContactoServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo 
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"CargoContacto/listar_cargo_contacto_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});