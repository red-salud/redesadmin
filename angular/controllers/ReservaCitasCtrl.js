app.controller('ReservaCitasCtrl', ['$scope', '$filter', '$state', '$stateParams', '$uibModal', '$bootbox', '$log', '$timeout', 'pinesNotifications', 'uiGridConstants', 'blockUI', 
  'ReservaCitasFactory',
  'PlanFactory',
  'ReservaCitasServices',
  'ProveedorServices', 
  'ProductoServices',
  'CertificadoServices',
  'AseguradoServices',
  function($scope, $filter, $state, $stateParams, $uibModal, $bootbox, $log, $timeout, pinesNotifications, uiGridConstants, blockUI, 
  ReservaCitasFactory,
  PlanFactory,
  ReservaCitasServices,
  ProveedorServices,
  ProductoServices,
  CertificadoServices,
  AseguradoServices
  ) { 
    $scope.metodos = {}; // contiene todas las funciones 
    $scope.fArr = {}; // contiene todos los arrays generados por las funciones 
    $scope.mySelectionGrid = []; 
    $scope.fBusqueda = {}; 
    $scope.fPrimerDato = {}; 
    // contenedores de html, clases, manejo de vistas. 
    $scope.fCont = {
      'classLabels': 'visible', 
      'classInputs': 'hidden' 
    };
    moment.tz.add('America/Lima|LMT -05 -04|58.A 50 40|0121212121212121|-2tyGP.o 1bDzP.o zX0 1aN0 1cL0 1cN0 1cL0 1PrB0 zX0 1O10 zX0 6Gp0 zX0 98p0 zX0|11e6'); 
    $scope.fArr.listaSexo = [
      {'id':'', 'descripcion':'--SELECCIONE SEXO--'},
      {'id':'M', 'descripcion':'MASCULINO'},
      {'id':'F', 'descripcion':'FEMENINO'}
    ]; 
    $scope.fArr.listaEstadosCita = [ 
      {'id':1, 'descripcion':'CITA POR CONFIRMAR'},
      {'id':2, 'descripcion':'CITA CONFIRMADA'},
      {'id':3, 'descripcion':'ATENCIÓN'}
    ];
    $scope.fArr.listaCertificadosSeleccion = [];
    if( $stateParams.identifyNumDoc ){ 
      $scope.fPrimerDato.numero_documento = $stateParams.identifyNumDoc; 
      // GET NUMERO DOCUMENTO 
      $timeout(function() { 
        $scope.btnConsultarAseguradoDNI(); 
      }, 800); 
      if( $stateParams.editable ){
        $scope.fCont = { 
          'classLabels': ' hidden', 
          'classInputs': ' visible' 
        };
      }
    }

    // PROVEEDORES
    $scope.metodos.listaProveedores = function(myCallback) {
      var myCallback = myCallback || function() { };
      ProveedorServices.sListarCbo().then(function(rpta) { 
        $scope.fArr.listaProveedores = rpta.datos; 
        myCallback();
      });
    };

    // PRODUCTOS 
    $scope.metodos.listaProductos = function(myCallback) {
      var myCallback = myCallback || function() { };
      ProductoServices.sListarProductosConsultaCbo().then(function(rpta) { 
        $scope.fArr.listaProductos = rpta.datos; 
        myCallback();
      });
    };
    // BUSQUEDA POR DNI 
    $scope.btnConsultarAseguradoDNI = function() { 
      blockUI.start('Procesando información...'); 
      var arrParams = { 
        'numero_documento': $scope.fPrimerDato.numero_documento 
      }; 
      CertificadoServices.sListarCertificadosDeAsegurados(arrParams).then(function(rpta) { 
        $scope.fPrimerDato.asegurado_cert = [];
        if( rpta.flag == 1 ){ // solo un registro 
          $scope.fPrimerDato.asegurado_cert = rpta.datos[0]; 
          $scope.fPrimerDato.asegurado_cert.flag = 1; 
        } 
        if( rpta.flag == 2 ){ // mas de un registro 
          alert('El asegurado tiene mas de un certificado. Se mostrará una ventana en el cual elegirá el certificado.'); 
          $scope.fPrimerDato.asegurado_cert.flag = 2; 
          // elegir un item 
          blockUI.start('Abriendo formulario...');
          $uibModal.open({ 
            templateUrl: angular.patchURLCI+'Certificado/ver_popup_eleccion_certificado',
            size: 'md',
            backdrop: 'static',
            keyboard:false,
            scope: $scope,
            controller: function ($scope, $uibModalInstance) { 
              blockUI.stop(); 
              $scope.titleForm = 'Selección de Certificado'; 
              $scope.fArr.listaCertificadosSeleccion = rpta.datos; 
              $scope.seleccionarCertificado = function(cert) { 
                $scope.fPrimerDato.asegurado_cert = cert; 
                $scope.fPrimerDato.asegurado_cert.flag = 1; 
                $uibModalInstance.dismiss('cancel'); 
              }
              $scope.cancel = function () { 
                $uibModalInstance.dismiss('cancel'); 
              } 
            }
          });
        }
        if( rpta.flag == 0 ){ 
          $scope.fPrimerDato.asegurado_cert.flag = 'none'; 
          pinesNotifications.notify({ title: 'Error!', text: rpta.message, type: 'warning', delay: 3000 }); 
        } 
        blockUI.stop(); 
      });
    }
    $scope.$watch('fPrimerDato.numero_documento', function(newValue,oldValue){ 
      if( oldValue == newValue ){
        return false; 
      }
      if( newValue != oldValue ){ 
        $scope.fPrimerDato.asegurado_cert = {};
        $scope.fPrimerDato.asegurado_cert.flag = null;
      }
    });
    $scope.btnConsultarExternoDNI = function() {
      var url = $state.href('app.historial-certificado', {identifyNumDoc: $scope.fPrimerDato.numero_documento });
      window.open(url,'_blank'); 
    } 
    $scope.btnEditarInformacion = function() {
      $scope.fCont = { 
        'classLabels': ' hidden', 
        'classInputs': ' visible' 
      };
    }
    $scope.btnEditarInformacionExec = function() {
      $scope.fCont = { 
        'classLabels': 'visible', 
        'classInputs': ' hidden' 
      }; 
      blockUI.start('Procesando información...'); 
      var arrParams = { 
        'asegurado': $scope.fPrimerDato.asegurado_cert 
      }; 
      AseguradoServices.sEditarAseguradoInline(arrParams).then(function(rpta) { 
        if(rpta.flag == 1){ 
          var pTitle = 'OK!';
          var pType = 'success';
          angular.element('.calendar').fullCalendar( 'refetchEvents' ); 

        }else if(rpta.flag == 0){
          var pTitle = 'Error!';
          var pType = 'danger';
        }else{
          alert('Error inesperado');
        } 
        $scope.btnConsultarAseguradoDNI(); 
        blockUI.stop(); 
        pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 });  
      }); 
    } 
    // CONDICIONES DEL PLAN 
    $scope.btnVerCondicionesPlan = function() {
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr,
        'fAsegurado': $scope.fPrimerDato.asegurado_cert 
      }; 
      PlanFactory.verCondicionesPlanModal(arrParams); 
    }
    /* ACCIONES */
    $scope.btnAgregarCita = function(start,end) { 
      // console.log($scope.fPrimerDato.asegurado_cert, !($scope.fPrimerDato.asegurado_cert), '$scope.fPrimerDato.asegurado_cert'); 
      if( !($scope.fPrimerDato.asegurado_cert) ){
        pinesNotifications.notify({ title: 'Advertencia', text: 'No se ha elegido a ningún asegurado. Busque un asegurado e inténtelo nuevamente.', type: 'warning', delay: 3000 }); 
        return false; 
      } 
      if( !($scope.fPrimerDato.asegurado_cert.flag) ){
        pinesNotifications.notify({ title: 'Advertencia', text: 'No se ha elegido a ningún asegurado. Busque un asegurado e inténtelo nuevamente.', type: 'warning', delay: 3000 }); 
        return false; 
      } 
      if( $scope.fPrimerDato.asegurado_cert.flag == 'none' ){
        pinesNotifications.notify({ title: 'Advertencia', text: 'No se ha elegido a ningún asegurado. Busque un asegurado e inténtelo nuevamente.', type: 'warning', delay: 3000 }); 
        return false; 
      }
      var arrParams = { 
        'metodos': $scope.metodos,
        'fArr': $scope.fArr,
        'fPrimerDato': $scope.fPrimerDato,
        'start': start || null,
        'end': end || null
      }; 
      ReservaCitasFactory.agregarCitaModal(arrParams); 
    }
    $scope.btnEditarCita = function(row) {
      var arrParams = {
        'metodos': $scope.metodos,
        'fArr': $scope.fArr,
        'fPrimerDato': $scope.fPrimerDato,
        'start': null,
        'end': null,
        'row': row 
      };
      ReservaCitasFactory.editarCitaModal(arrParams); 
    }
    $scope.btnAnularCita = function(row) {
      var pMensaje = '¿Realmente desea anular el registro?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          var arrParams = {
            id: row.id 
          };
          blockUI.start('Procesando información...');
          ReservaCitasServices.sAnular(arrParams).then(function (rpta) {
            if(rpta.flag == 1){
              var pTitle = 'OK!';
              var pType = 'success';
              angular.element('.calendar').fullCalendar( 'refetchEvents' ); 
            }else if(rpta.flag == 0){
              var pTitle = 'Error!';
              var pType = 'danger';
            }else{
              alert('Error inesperado');
            }
            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2500 }); 
            blockUI.stop(); 
          });
        }
      });
    }
    $scope.metodos.btnEnviarCorreoCita = function(row) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Cita/ver_popup_envio_correo', 
        size: 'md',
        backdrop: 'static',
        keyboard:false,
        scope: $scope,
        controller: function ($scope, $uibModalInstance) { 
          blockUI.stop(); 
          $scope.titleForm = 'Envío de Correo'; 
          $scope.fData = row; 
          $scope.fData.active = 0;
          $scope.fCorreo = { 
            'solicitud': {},
            'confirmacion': {}
          }; 
          // cargar información por defecto 
          $scope.getInfoCorreoCitas = function() { 
            blockUI.start('Obteniendo información...');
            ReservaCitasServices.sObtenerConfiguracioCorreoCita(row).then(function (rpta) {
              if(rpta.flag == 1){ 
                $scope.fCorreo.solicitud = { 
                  'remitente': rpta.datos.correo_laboral,
                  'remitente_copia': rpta.datos.correo_laboral, 
                  'destinatario': rpta.datos.contactos_comma, 
                  'titulo': rpta.datos.titulo_solicitud,
                  'cuerpo': rpta.datos.cuerpo_solicitud 
                }; 
                $scope.fCorreo.confirmacion = { 
                  'remitente': rpta.datos.correo_laboral,
                  'remitente_copia': rpta.datos.correo_laboral,
                  'destinatario': rpta.datos.contactos_comma,
                  'titulo': rpta.datos.titulo_confirmacion,
                  'cuerpo': rpta.datos.cuerpo_confirmacion 
                }; 
                $scope.changeDestinatario = function(argument) {
                  $scope.fCorreo.solicitud.destinatario = rpta.datos.contactos_comma; 
                  $scope.fCorreo.confirmacion.destinatario = rpta.datos.contactos_comma;
                  $('.tg-destinatario').tagsinput('destroy');
                  $('.tg-destinatario').tagsinput('refresh');
                }
              }
              blockUI.stop();
            }); 
          }
          $scope.getInfoCorreoCitas(); 
          if($scope.fData.estado_cita.id == 1){ // por confirmar
            $scope.fData.active = 0;
          }
          if($scope.fData.estado_cita.id == 2){ // confirmado
            $scope.fData.active = 1;
          }
          $scope.cancel = function () { 
            $scope.fCorreo = { 
              'solicitud': {},
              'confirmacion': {}
            }; 
            $('.tg-destinatario').tagsinput('destroy');
            $uibModalInstance.dismiss('cancel'); 
          } 
        }
      });
    }
    /* EVENTOS */
    $scope.menu = angular.element('.menu-dropdown');
    $scope.alertOnClick =function(event, jsEvent, view) {
      $scope.event = event;
      //console.log(event,jsEvent,'event,jsEvent');
      $scope.menu.addClass('open');
      $scope.menu.removeClass('left right');
      var wrap = angular.element(jsEvent.target).closest('.fc-event');
      var cal = wrap.closest('.calendar');
      var left = wrap.offset().left - cal.offset().left;
      var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
      if( right > $scope.menu.width() ) {
        $scope.menu.addClass('left');        
      } else if ( left > $scope.menu.width() ) {
        $scope.menu.addClass('right');
      }

     /* console.log('cal.offset().bottom',cal.offset().bottom);
      console.log('cal.offset().top',cal.offset().top);
      console.log('$scope.menu.height()',$scope.menu.height());*/

      $scope.event.posX = jsEvent.pageX - cal.offset().left;
      if($scope.event.posX < 140){
        $scope.event.posX = 140;
      }

      $scope.event.posY = jsEvent.pageY - cal.offset().top;
      if($scope.event.posY > 620){
        $scope.event.posY = 620;
      }
    }
    $scope.alertOnResize = function(event, delta){ 
      angular.element('.calendar').fullCalendar( 'refetchEvents' );
    };
    $scope.selectCell = function(date, end, jsEvent, view) { 
      console.log(angular.element('.calendar').fullCalendar('getView'),'angular.element(.calendar).fullCalendar(getView)');
      var typeView = angular.element('.calendar').fullCalendar('getView');      
      if(typeView.type == 'month'){        
        angular.element('.calendar').fullCalendar( 'gotoDate', date );
        angular.element('.calendar').fullCalendar('changeView', 'agendaDay');
      }else{
        $scope.btnAgregarCita(date, end);
      }
    }
    $scope.alertOnDrop = function(event, delta){      
      blockUI.start('Actualizando calendario...');
      var datos = {
        event: event,
        delta: delta,
      };
      ReservaCitasServices.sMoverCita(datos).then(function(rpta){ 
        if(rpta.flag == 1){         
          var pTitle = 'OK!';
          var pType = 'success';
        }else if( rpta.flag == 0 ){
          var pTitle = 'Advertencia!';
          var pType = 'warning';  
        } 
        angular.element('.calendar').fullCalendar( 'refetchEvents' );
        pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 3000 });
        blockUI.stop();
      });
    };
    $scope.overlay = $('.fc-overlay');
    $scope.alertOnMouseOver = function( event, jsEvent, view ){
      $scope.event = event;
      $scope.overlay.removeClass('left right top').find('.arrow').removeClass('left right top pull-up');
      var wrap = $(jsEvent.target).closest('.fc-event');
      var cal = wrap.closest('.calendar');
      var left = wrap.offset().left - cal.offset().left;
      var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
      var top = cal.height() - (wrap.offset().top - cal.offset().top + wrap.height());
      if( right > $scope.overlay.width() ) { 
        $scope.overlay.addClass('left').find('.arrow').addClass('left pull-up')
      }else if ( left > $scope.overlay.width() ) {
        $scope.overlay.addClass('right').find('.arrow').addClass('right pull-up');
      }else{
        $scope.overlay.find('.arrow').addClass('top');
      }
      if( top < $scope.overlay.height() ) { 
        $scope.overlay.addClass('top').find('.arrow').removeClass('pull-up').addClass('pull-down')
      }
      (wrap.find('.fc-overlay').length == 0) && wrap.append( $scope.overlay );
    }
    $scope.actualizarCalendario = function(block){
      blockUI.start('Actualizando calendario...');
      angular.element('.calendar').fullCalendar( 'refetchEvents' );
      blockUI.stop();
    }
    $scope.closeMenu = function(){
      $scope.menu.removeClass('open');
    }
    /* CARGA DE DATOS */ 
    $scope.eventsF = function (start, end, timezone, callback) {
      var events = []; 
      blockUI.start('Actualizando calendario...');
      //console.log(start, end,'start, end');
      //console.log(start.toLocaleTimeString(), end.toLocaleTimeString(),'start.toLocaleTimeString(), end.toLocaleTimeString()'); 
      //console.log(start,end,'moment(start).tz("America/Lima").format(YYYY-MM-DD)'); 
      $scope.fBusqueda.desde = moment(start).tz('America/Lima').format('YYYY-MM-DD');
      $scope.fBusqueda.hasta = moment(end).tz('America/Lima').format('YYYY-MM-DD');
      ReservaCitasServices.sListarCitaCalendario($scope.fBusqueda).then(function (rpta) { 
        if(rpta.flag == 1){ 
          angular.forEach(rpta.datos, function(row, key) { 
              row.start =  moment(row.start);
              row.end =  moment(row.end);
          });
          events = rpta.datos; 
          callback(events); 
        } 
        blockUI.stop();
      });
    } 
    $scope.eventSources = [$scope.eventsF];
    /* Change View */
    $scope.changeView = function(view, calendar) {
      $('.calendar').fullCalendar('changeView', view);
    };
    $scope.today = function(calendar) {
      $('.calendar').fullCalendar('today');
    };

    /* event sources array*/
    //$scope.eventSources = [$scope.events]; 

    /* config object CalendarCitas */
    $scope.uiConfig = {
      calendarCitas:{ 
        allDaySlot: false,
        height: 450,
        contentHeight: 510,
        editable: true,
        selectable: true,
        defaultView: 'agendaWeek',
        dayNames: ["Domingo", "Lunes ", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
        dayNamesShort : ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames : ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"],
        monthNamesShort : ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"],
        header:{
          left: 'prev',
          center: 'title',
          right: 'next'
        },
        select: $scope.selectCell,
        eventDrop: $scope.alertOnDrop,
        eventResize: $scope.alertOnResize,
        eventClick: $scope.alertOnClick,
        eventMouseover: function (data, event, view) { 
          // console.log( event, $('.tooltip-event') ); 
          var tooltip = '<div class="tooltip-event"' +
                        'style="">' 
                          + 'Afiliado: ' + data.asegurado.asegurado + '<br />' 
                          + 'Lugar: ' + data.proveedor.descripcion + '<br />' 
                          + 'Especialidad: ' + data.especialidad.especialidad + '<br />' 
                          + 'Plan: ' + data.plan.descripcion 
                  + '</div>';
            $("body").append(tooltip);
            $(this).mouseover(function (e) {
                $(this).css('z-index', 10000);
                $('.tooltip-event').fadeIn('500');
                $('.tooltip-event').fadeTo('10', 1.9);
            }).mousemove(function (e) {
                $('.tooltip-event').css('top', e.pageY + 10);
                $('.tooltip-event').css('left', e.pageX + 20);
            });
        },
        eventMouseout: function (data, event, view) {
          $(this).css('z-index', 8);
          $('.tooltip-event').remove();
        },
        minTime: '07:00:00',
        maxTime: '20:00:00',
        displayEventTime: false,
        views: {
          week: {
            titleFormat: 'D MMMM YYYY',
            titleRangeSeparator: ' - '
          },
          day: {
            titleFormat: 'ddd DD-MM'
          }
        }
      }
    };
}]);

app.service("ReservaCitasServices",function($http, $q, handleBehavior) {
    return({
        sListarCitaCalendario: sListarCitaCalendario,
        sListarElementosAutoComplete: sListarElementosAutoComplete,
        sListarElementosBusqueda: sListarElementosBusqueda,
        sObtenerConfiguracioCorreoCita: sObtenerConfiguracioCorreoCita,
        sRegistrarCita: sRegistrarCita,
        sMoverCita: sMoverCita,
        sEditarCita: sEditarCita,
        sAnular: sAnular
    });
    function sListarCitaCalendario(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/listar_citas_en_calendario",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarElementosAutoComplete(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/listar_elementos_autocomplete",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
    function sListarElementosBusqueda(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/buscar_elemento_para_lista",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    } 
    function sObtenerConfiguracioCorreoCita(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/obtener_configuracion_correo_cita",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sRegistrarCita (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/registrar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sMoverCita(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/mover_cita",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sEditarCita (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/editar",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Cita/anular",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
});

app.factory("ReservaCitasFactory",function($uibModal, pinesNotifications, blockUI, $timeout, ReservaCitasServices, CertificadoServices) { 
  var interfaz = {
    agregarCitaModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Cita/ver_popup_form_cita',
        size: 'lg',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = {};
          $scope.fData.accion ='reg';
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr; 
          $scope.fPrimerDato = arrParams.fPrimerDato; 
          $scope.titleForm = 'Registro de Cita';
          $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
          }

          // BINDEO PROVEEDORES 
          $scope.metodos.listaProveedores(); 

          // BINDEO DE PRODUCTOS 
          var myCallBackPROD = function() { 
            $scope.fArr.listaProductos.splice(0,0,{ id : '0', descripcion:'--Seleccione producto/servicio--'}); 
            $scope.fData.producto = $scope.fArr.listaProductos[0]; 
          }; 
          $scope.metodos.listaProductos(myCallBackPROD); 

          // BINDEO ESTADOS DE CITA
          $scope.fData.estado_cita = $scope.fArr.listaEstadosCita[0]; 
          /* DATEPICKERS */
          $scope.configDP = {};
          $scope.configDP.today = function() {
            if(arrParams.start){
              console.log('arrParams.start',arrParams.start);
              $scope.fData.fecha = arrParams.start.toDate();
            }else{
              $scope.fData.fecha = new Date();
            }
          };
          $scope.configDP.today();

          $scope.configDP.clear = function() {
            $scope.fData.fecha = null;
          };

          $scope.configDP.dateOptions = {
            formatYear: 'yy',
            maxDate: new Date(2020, 5, 22),
            minDate: new Date(),
            startingDay: 1
          };

          $scope.configDP.open = function() {
            $scope.configDP.popup.opened = true;
          };

          $scope.configDP.formats = ['dd-MM-yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
          $scope.configDP.format = $scope.configDP.formats[0];
          $scope.configDP.altInputFormats = ['M!/d!/yyyy'];

          $scope.configDP.popup = {
            opened: false
          }; 
          /* END DATEPICKERS */

          /* TIMEPICKERS */ 
          $scope.configTP = {};
          $scope.configTP.tpHoraInicio = {};
          $scope.configTP.tpHoraInicio.hstep = 1;
          $scope.configTP.tpHoraInicio.mstep = 30;
          $scope.configTP.tpHoraInicio.ismeridian = true;
          $scope.configTP.tpHoraInicio.toggleMode = function() {
            $scope.configTP.tpHoraInicio.ismeridian = ! $scope.configTP.tpHoraInicio.ismeridian;
          };
          $scope.configTP.tpHoraFin = angular.copy($scope.configTP.tpHoraInicio); 
          if(arrParams.start){ 
            // console.log('arrParams.start.a',arrParams.start.format('a')); 
            var partes_hora1 = arrParams.start.format('hh:mm').split(':');
            // console.log('partes_hora1',partes_hora1);
            var d = new Date();
            if(arrParams.start.format('a') == 'pm' && parseInt(partes_hora1[0]) != 12){
              d.setHours( parseInt(partes_hora1[0]) +12 );
            }else{
              d.setHours( parseInt(partes_hora1[0]) );
            }
            
            d.setMinutes( parseInt(partes_hora1[1]) );
            $scope.fData.hora_desde = d;

            if(arrParams.end){
              var partes_hora2= arrParams.end.format('hh:mm').split(':');
            }else{
              var partes_hora2= arrParams.start.add(30, 'minutes').format('hh:mm').split(':');
            }
            var c = new Date();            
            if(arrParams.start.format('a') == 'pm' && parseInt(partes_hora2[0]) != 12){
              c.setHours( parseInt(partes_hora2[0]) + 12 );
            }else{
              c.setHours( parseInt(partes_hora2[0]) );
            }
            c.setMinutes( parseInt(partes_hora2[1]) );
            $scope.fData.hora_hasta = c;
          } else{
            $scope.fData.hora_desde = new Date();
            $scope.fData.hora_hasta = new Date();
          }  
          $scope.actualizarHoraFin = function(){
            $scope.fData.hora_hasta = moment($scope.fData.hora_desde).add(30,'m').toDate();
          } 
          /* END TIMEPICKERS */

          $scope.aceptar = function () { 
            if($scope.fData.hora_desde){
              $scope.fData.hora_desde_str = $scope.fData.hora_desde.toLocaleTimeString(); 
            }
            if($scope.fData.hora_hasta){
              $scope.fData.hora_hasta_str = $scope.fData.hora_hasta.toLocaleTimeString(); 
            }
            blockUI.start('Procesando información...');
            $scope.fData.asegurado_cert = $scope.fPrimerDato.asegurado_cert; 
            ReservaCitasServices.sRegistrarCita($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){ 
                var pTitle = 'OK!';
                var pType = 'success';
                $uibModalInstance.dismiss('cancel');
                angular.element('.calendar').fullCalendar('refetchEvents'); 
                //console.log(rpta.datos.row,rpta.datos.estado_cita.id,'rpta.datos.row,rpta.datos.estado_cita.id');
                if( rpta.datos.row && !(rpta.datos.estado_cita.id == 3) ){ 
                  $timeout(function() { 
                    $scope.metodos.btnEnviarCorreoCita(rpta.datos.row); 
                  },1000); 
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
    editarCitaModal: function (arrParams) {
      blockUI.start('Abriendo formulario...');
      $uibModal.open({ 
        templateUrl: angular.patchURLCI+'Cita/ver_popup_form_cita',
        size: 'lg',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $uibModalInstance, arrParams) { 
          blockUI.stop(); 
          $scope.fData = arrParams.row; 
          $scope.fData.accion ='edit';
          $scope.metodos = arrParams.metodos;
          $scope.fArr = arrParams.fArr; 
          $scope.fPrimerDato = arrParams.fPrimerDato; 
          $scope.titleForm = 'Edición de la Cita';
          console.log(arrParams.row,'arrParams.row');
          /* DATEPICKERS */
          $scope.configDP = {};
          // $scope.configDP.today = function() {
          //   if(arrParams.start){
          //     console.log('arrParams.start',arrParams.start);
          //     $scope.fData.fecha = arrParams.start.toDate();
          //   }else{
          //     $scope.fData.fecha = new Date();
          //   }
          // };
          // $scope.configDP.today();

          $scope.configDP.clear = function() {
            $scope.fData.fecha = null;
          };

          $scope.configDP.dateOptions = {
            formatYear: 'yy',
            maxDate: new Date(2020, 5, 22),
            minDate: new Date(),
            startingDay: 1
          };

          $scope.configDP.open = function() {
            $scope.configDP.popup.opened = true;
          };

          $scope.configDP.formats = ['dd-MM-yyyy', 'dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
          $scope.configDP.format = $scope.configDP.formats[0];
          $scope.configDP.altInputFormats = ['M!/d!/yyyy'];

          $scope.configDP.popup = {
            opened: false
          }; 
          $scope.fData.fecha = $scope.fData.start.toDate();
          /* END DATEPICKERS */

          /* TIMEPICKERS */ 
          $scope.configTP = {};
          $scope.configTP.tpHoraInicio = {};
          $scope.configTP.tpHoraInicio.hstep = 1;
          $scope.configTP.tpHoraInicio.mstep = 15;
          $scope.configTP.tpHoraInicio.ismeridian = true;
          $scope.configTP.tpHoraInicio.toggleMode = function() {
            $scope.configTP.tpHoraInicio.ismeridian = ! $scope.configTP.ismeridian;
          };
          $scope.configTP.tpHoraFin = angular.copy($scope.configTP.tpHoraInicio); 

          var partes_hora1 = $scope.fData.hora_desde_sql.split(':');
          //console.log(partes_hora1);
          var d = new Date();
          d.setHours( parseInt(partes_hora1[0]) );
          d.setMinutes( parseInt(partes_hora1[1]) );
          $scope.fData.hora_desde = d;

          var partes_hora2 = $scope.fData.hora_hasta_sql.split(':');
          //console.log(partes_hora2);
          var c = new Date();
          c.setHours( parseInt(partes_hora2[0]) );
          c.setMinutes( parseInt(partes_hora2[1]) );
          $scope.fData.hora_hasta = c; 

          $scope.actualizarHoraFin = function(){ 
            $scope.fData.hora_hasta = moment($scope.fData.hora_desde).add(30,'m').toDate();
          } 

          // BINDEO DE PRODUCTO 
          var myCallBackPROD = function() { 
            var objIndex = $scope.fArr.listaProductos.filter(function(obj) { 
              return obj.id == $scope.fData.producto.id;
            }).shift(); 
            $scope.fData.producto = objIndex; 
          }
          $scope.metodos.listaProductos(myCallBackPROD); 

          // BINDEO DE PROVEEDOR 
          var myCallBackPR = function() { 
            var objIndex = $scope.fArr.listaProveedores.filter(function(obj) { 
              return obj.id == $scope.fData.proveedor.id;
            }).shift(); 
            $scope.fData.proveedor = objIndex; 
          }
          $scope.metodos.listaProveedores(myCallBackPR); 

          // BINDEO ESTADO DE CITA 
          var objIndex = $scope.fArr.listaEstadosCita.filter(function(obj) { 
            return obj.id == $scope.fData.estado_cita.id;
          }).shift(); 
          $scope.fData.estado_cita = objIndex; 

          $scope.cancel = function () { 
            $uibModalInstance.dismiss('cancel');
          };

          $scope.aceptar = function () { 
            if($scope.fData.hora_desde){
              $scope.fData.hora_desde_str = $scope.fData.hora_desde.toLocaleTimeString(); 
            }
            if($scope.fData.hora_hasta){
              $scope.fData.hora_hasta_str = $scope.fData.hora_hasta.toLocaleTimeString(); 
            }
            blockUI.start('Procesando información...'); 

            ReservaCitasServices.sEditarCita($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){ 
                var pTitle = 'OK!';
                var pType = 'success';
                $uibModalInstance.dismiss('cancel');
                angular.element('.calendar').fullCalendar( 'refetchEvents' ); 
                if( rpta.datos.row && rpta.datos.estado_cita.id == 2 ){ // confirmado 
                  $timeout(function() { 
                    $scope.metodos.btnEnviarCorreoCita(rpta.datos.row); 
                  },1000); 
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