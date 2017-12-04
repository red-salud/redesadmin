app.service("UbigeoServices",function($http, $q, handleBehavior) {
    return({
        sListarDepartamentos: sListarDepartamentos,
        sListarDepartamentoPorCodigo: sListarDepartamentoPorCodigo,
        sListarProvinciasDeDepartamento: sListarProvinciasDeDepartamento,
        sListarProvinciaDeDepartamentoPorCodigo: sListarProvinciaDeDepartamentoPorCodigo,
        sListarDistritosDeProvincia: sListarDistritosDeProvincia,
        sListarDistritosDeProvinciaPorCodigo: sListarDistritosDeProvinciaPorCodigo,
        sListarDepartamentoPorAutocompletado: sListarDepartamentoPorAutocompletado,
        sListarProvinciaPorAutocompletado: sListarProvinciaPorAutocompletado,
        sListarDistritoPorAutocompletado: sListarDistritoPorAutocompletado,
        sListarDireccionCoordenadas: sListarDireccionCoordenadas
    });

    function sListarDepartamentos(pDatos) { 
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_departamentos", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDepartamentoPorCodigo (pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_departamento_por_codigo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarProvinciasDeDepartamento(pDatos) { 
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_provincias", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarProvinciaDeDepartamentoPorCodigo (pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_provincia_departamento_por_codigo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDistritosDeProvincia (pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_distritos", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDistritosDeProvinciaPorCodigo (pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_distrito_provincia_por_codigo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDepartamentoPorAutocompletado (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_dptos_por_autocompletado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarProvinciaPorAutocompletado (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_prov_por_autocompletado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDistritoPorAutocompletado (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Ubigeo/lista_distr_por_autocompletado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarDireccionCoordenadas(datos) { 
      // &components=country:ES&key= 
      console.log('https://maps.google.com/maps/api/geocode/json?sensor=false&address='+datos.ubicacion+'&components=country:PE&key='+datos.keyGoogleMap);  
      var request = $http({
            method : "post",
            url : 'https://maps.google.com/maps/api/geocode/json?sensor=false&address='+datos.ubicacion+'&components=country:PE&key='+datos.keyGoogleMap, 
            data : datos,
            headers : {
              'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
            }
      });
      return (request.then( handleSuccess,handleError ));
    }
});