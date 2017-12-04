app.service("ProductoServices",function($http, $q, handleBehavior) {
    return({ 
        sListarProductosConsultaCbo: sListarProductosConsultaCbo 
    });
    function sListarProductosConsultaCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Producto/listar_productos_tipo_consulta_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});