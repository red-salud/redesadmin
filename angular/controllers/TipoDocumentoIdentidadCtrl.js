app.service("TipoDocumentoIdentidadServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoIdentidad/listar_tipo_documento_identidad_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});