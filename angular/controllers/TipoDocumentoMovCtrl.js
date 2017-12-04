app.service("TipoDocumentoMovServices",function($http, $q, handleBehavior) {
    return({ 
        sListarCbo: sListarCbo
    });
    function sListarCbo(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"TipoDocumentoMov/listar_tipo_documento_mov_cbo",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});