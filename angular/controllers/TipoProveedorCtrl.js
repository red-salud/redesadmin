app.service("TipoProveedorServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo 
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoProveedor/listar_tipo_proveedor_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});