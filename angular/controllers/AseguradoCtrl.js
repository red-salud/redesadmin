app.service("AseguradoServices",function($http, $q, handleBehavior) {
    return({ 
        sEditarAseguradoInline: sEditarAseguradoInline  
    });
    function sEditarAseguradoInline(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Asegurado/editar_asegurado_inline",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});