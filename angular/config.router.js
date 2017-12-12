'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          
      '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;        
      }
    ]
  )
  .config(
    [
      '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 'MODULE_CONFIG', 
      function ($stateProvider, $urlRouterProvider, JQ_CONFIG, MODULE_CONFIG) { 
        var layout = "tpl/app.html";
        if(window.location.href.indexOf("material") > 0){
          layout = "tpl/blocks/material.layout.html";
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }else{
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }
          
        $stateProvider
          .state('app', {
            abstract: true,
            url: '/app',
            templateUrl: layout
          })
          .state('app.dashboard', {
            url: '/dashboard',
            templateUrl: 'tpl/app_dashboard.html',
            resolve: load(['angular/controllers/chart.js'])
          })
          .state('app.persona-natural', {
            url: '/persona-natural',
            templateUrl: 'tpl/persona-natural.html',
            resolve: load([
              'angular/controllers/ClientePersonaCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/ColaboradorCtrl.js'
            ]) 
          })
          .state('app.persona-juridica', {
            url: '/persona-juridica',
            templateUrl: 'tpl/persona-juridica.html',
            resolve: load([
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/ContactoClienteCtrl.js',
              'angular/controllers/ColaboradorCtrl.js'
            ])
          })
          .state('app.nueva-venta', {
            url: '/nueva-venta',
            templateUrl: 'tpl/nueva-venta.html',
            resolve: load([
              'angular/controllers/NuevaVentaCtrl.js',
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/ClientePersonaCtrl.js', 
              'angular/controllers/ClienteCtrl.js',
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/ConceptoCtrl.js',
              'angular/controllers/CategoriaElementoCtrl.js',
              'angular/controllers/ElementoCtrl.js',
              'angular/controllers/TipoDocumentoIdentidadCtrl.js',
              'angular/controllers/FormaPagoCtrl.js',
              'angular/controllers/SerieCtrl.js',
              'angular/controllers/ContactoClienteCtrl.js', 
              'angular/controllers/TipoDocumentoMovCtrl.js' 
            ])
          })
          .state('app.historial-venta', { 
            url: '/historial-venta',
            templateUrl: 'tpl/historial-venta.html',
            resolve: load([
              'angular/controllers/HistorialVentaCtrl.js',
              'angular/controllers/NuevaVentaCtrl.js',
              'angular/controllers/ConceptoCtrl.js',
            ])
          })
          .state('app.comprobante-serie', { 
            url: '/comprobante-serie',
            templateUrl: 'tpl/comprobante-serie.html',
            resolve: load([ 
              'angular/controllers/TipoDocumentoMovCtrl.js',
              'angular/controllers/SerieCtrl.js'
            ])
          })
          .state('app.boletaje-masivo', {
            url: '/boletaje-masivo',
            templateUrl: 'tpl/boletaje-masivo.html',
            resolve: load([
              'angular/controllers/BoletajeMasivoCtrl.js',
              'angular/controllers/PlanCtrl.js',
              'angular/controllers/HistorialCobroCtrl.js',
              'angular/controllers/SerieCtrl.js',
              'angular/controllers/ConceptoCtrl.js',
              'angular/controllers/TipoDocumentoMovCtrl.js' 
            ])
          })
          .state('app.historial-cobro', {
            url: '/historial-cobro',
            templateUrl: 'tpl/historial-cobro.html',
            resolve: load([
              'angular/controllers/HistorialCobroCtrl.js',
              'angular/controllers/PlanCtrl.js',
            ])
          })
          .state('app.historial-siniestro', {
            url: '/historial-siniestro',
            templateUrl: 'tpl/historial-siniestro.html',
            resolve: load([
              'angular/controllers/HistorialSiniestroCtrl.js',
              'angular/controllers/PlanCtrl.js',
            ])
          })
          .state('app.historial-certificado', {
            url: '/historial-certificado/:identifyNumDoc',
            templateUrl: 'tpl/historial-certificado.html',
            resolve: load([
              'angular/controllers/HistorialCertificadoCtrl.js',
              'angular/controllers/PlanCtrl.js'
            ])
          })
          .state('app.concepto', {
            url: '/concepto',
            templateUrl: 'tpl/concepto.html',
            resolve: load([
              'angular/controllers/ConceptoCtrl.js' 
            ]) 
          })
          .state('app.unidad-medida', {
            url: '/unidad-medida',
            templateUrl: 'tpl/unidad-medida.html',
            resolve: load([
              'angular/controllers/UnidadMedidaCtrl.js'
            ]) 
          })
          .state('app.caracteristica', {
            url: '/caracteristica',
            templateUrl: 'tpl/caracteristica.html',
            resolve: load([
              'angular/controllers/CaracteristicaCtrl.js'
            ]) 
          })
          .state('app.banco', {
            url: '/banco',
            templateUrl: 'tpl/banco.html',
            resolve: load([
              'angular/controllers/BancoCtrl.js'
            ]) 
          })     
          .state('app.empresa-admin', {
            url: '/empresa-admin',
            templateUrl: 'tpl/empresa-admin.html',
            resolve: load([
              'angular/controllers/EmpresaAdminCtrl.js',
              'angular/controllers/BancoCtrl.js',
              'angular/controllers/BancoEmpresaAdminCtrl.js'
            ]) 
          }) 
          .state('app.elemento', {
            url: '/elemento',
            templateUrl: 'tpl/elemento.html',
            resolve: load([
              'angular/controllers/ElementoCtrl.js',
              'angular/controllers/CategoriaElementoCtrl.js' 
            ]) 
          })  
          .state('app.categoria-elemento', {
            url: '/categoria-elemento',
            templateUrl: 'tpl/categoria-elemento.html',
            resolve: load([
              'angular/controllers/CategoriaElementoCtrl.js'         
            ]) 
          })  
          .state('app.colaborador', {
            url: '/colaborador',
            templateUrl: 'tpl/colaborador.html',
            resolve: load([
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/UsuarioCtrl.js'         
            ]) 
          })    
          .state('app.proveedor', {
            url: '/proveedor',
            templateUrl: 'tpl/proveedor.html',
            resolve: load([
              'angular/controllers/ProveedorCtrl.js',
              'angular/controllers/TipoProveedorCtrl.js',
              'angular/controllers/ContactoProveedorCtrl.js', 
              'angular/controllers/UbigeoCtrl.js',
              'angular/controllers/UsuarioCtrl.js',
              'https://maps.googleapis.com/maps/api/js?key=AIzaSyB2FjjCqepP3ZXx6xFbxEKjijPtcNTCcXM' 
              // 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCEQA0xoHHCFZYeA3lW9vBceD5OxOQOsAo' 
            ]) 
          }) 
          .state('app.usuario', {
            url: '/usuario',
            templateUrl: 'tpl/usuario.html',
            resolve: load([
              'angular/controllers/UsuarioCtrl.js', 
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/ProveedorCtrl.js'  
            ]) 
          })    
          .state('app.contacto-cliente', {
            url: '/contacto-cliente',
            templateUrl: 'tpl/contacto-cliente.html',
            resolve: load([      
              'angular/controllers/ContactoClienteCtrl.js', 
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/ClienteCtrl.js'   
            ]) 
          })    
          .state('app.variable-car', {
            url: '/variable-car',
            templateUrl: 'tpl/variable-car.html',
            resolve: load([
              'angular/controllers/VariableCarCtrl.js'      
            ]) 
          }) 
          .state('app.forma-pago', {
            url: '/formas-pago',
            templateUrl: 'tpl/forma-pago.html',
            resolve: load([
              'angular/controllers/FormaPagoCtrl.js',
              'angular/controllers/PlazoFormaPagoCtrl.js'        
            ]) 
          })
          /* CITAS */
          .state('app.reserva-citas', { 
            url: '/reserva-citas/:identifyNumDoc/:editable',
            templateUrl: 'tpl/reserva-citas.html',
            resolve: load([ 
              // 'moment',
              'fullcalendar',
              'ui.calendar',
              'ui.select',
              'angular/controllers/ReservaCitasCtrl.js', 
              'angular/controllers/ProveedorCtrl.js',
              'angular/controllers/ProductoCtrl.js',
              'angular/controllers/HistorialCertificadoCtrl.js',
              'angular/controllers/AseguradoCtrl.js',
              'angular/controllers/PlanCtrl.js'  
            ]) 
          })
          .state('lockme', {
              url: '/lockme',
              templateUrl: 'tpl/page_lockme.html'
          })
          .state('access', {
              url: '/access',
              template: '<div ui-view class="fade-in-right-big smooth"></div>'
          })
          .state('access.login', {
              url: '/login',
              templateUrl: 'tpl/login.html',
              resolve: load( ['angular/controllers/Login.js'] )
          })
          // others
          .state('access.signup', {
              url: '/signup',
              templateUrl: 'tpl/page_signup.html',
              resolve: load( ['angular/controllers/signup.js'] )
          })
          .state('access.404', {
              url: '/404',
              templateUrl: 'tpl/page_404.html'
          })
          // mail
          .state('app.mail', {
              abstract: true,
              url: '/mail',
              templateUrl: 'tpl/mail.html',
              // use resolve to load other dependences
              resolve: load( ['angular/app/mail/mail.js','angular/app/mail/mail-service.js'/*,'moment'*/] )
          })
          .state('app.mail.list', {
              url: '/inbox/{fold}',
              templateUrl: 'tpl/mail.list.html'
          })
          .state('app.mail.detail', {
              url: '/{mailId:[0-9]{1,4}}',
              templateUrl: 'tpl/mail.detail.html'
          })
          .state('app.mail.compose', {
              url: '/compose',
              templateUrl: 'tpl/mail.new.html'
          });

        function load(srcs, callback) {
          return {
              deps: ['$ocLazyLoad', '$q',
                function( $ocLazyLoad, $q ){
                  var deferred = $q.defer();
                  var promise  = false;
                  srcs = angular.isArray(srcs) ? srcs : srcs.split(/\s+/);
                  if(!promise){
                    promise = deferred.promise;
                  }
                  angular.forEach(srcs, function(src) {
                    promise = promise.then( function(){
                      if(JQ_CONFIG[src]){
                        return $ocLazyLoad.load(JQ_CONFIG[src]);
                      }
                      angular.forEach(MODULE_CONFIG, function(module) {
                        if( module.name == src){
                          name = module.name;
                        }else{
                          name = src;
                        }
                      });
                      return $ocLazyLoad.load(name);
                    } );
                  });
                  deferred.resolve();
                  return callback ? promise.then(function(){ return callback(); }) : promise;
              }]
          }
        }
      }
    ]
  );
