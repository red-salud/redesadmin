<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="" name="formProveedor"> 
		<div class="nav-tabs-alt" >
		    <ul class="nav nav-tabs nav-justified">
		      <li ng-hide="accion == 'edit'" class="active">
		        <a data-target="#proveedor" role="tab" data-toggle="tab" class="mb-sm p-n bg-celeste-tab"> 
		        	<span class="text-left">1.-</span> REGISTRO DE PROVEEDOR </a>
		      </li>
		      <li ng-hide="accion == 'edit'">
		        <a data-target="#usuario" role="tab" data-toggle="tab" class="mb-sm p-n bg-celeste-tab" > 
		        	<span class="text-left">2.-</span> GENERAR USUARIO </a>
		      </li>
		      <li ng-hide="accion == 'edit'">
		        <a data-target="#contacto" role="tab" data-toggle="tab" class="mb-sm p-n bg-celeste-tab"> 
		        	<span class="text-left">3.-</span> AGREGAR CONTACTOS </a>
		      </li>
		    </ul>
		</div>
		<div class="row-row">
	    <div class="cell scrollable hover">
	      <div class="cell-inner">
	        <div class="tab-content">
	          <div class="tab-pane active" id="proveedor"> 
	          	<div class="wrapper-nn"> 
								<div class="row">
									<div class="form-group col-md-3 mb-md ">
										<label class="control-label mb-n"> Tipo de Proveedor: <small class="text-danger">(*)</small> </label>
							            <select class="form-control input-sm" ng-model="fData.tipo_proveedor" ng-options="item as item.descripcion for item in fArr.listaTipoProveedor" required tabindex="10" ></select> 
									</div>
									<div class="form-group col-md-3 mb-md">
										<label class="control-label mb-n"> RUC: <small class="text-danger">(*)</small> </label>
										<input type="text" class="form-control input-sm" ng-model="fData.numero_documento" placeholder="Ingrese RUC" required tabindex="20" maxlength="40" />
									</div> 
									<div class="form-group col-md-3 mb-md">
										<label class="control-label mb-n"> Cod. SUNASA: </label>
										<input type="text" class="form-control input-sm" ng-model="fData.cod_sunasa" placeholder="Cod. SUNASA" tabindex="30" />
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-3 mb-md">
										<label class="control-label mb-n"> Razón Social: <small class="text-danger">(*)</small> </label>
										<input type="text" class="form-control input-sm" ng-model="fData.razon_social" placeholder="Ingrese razón social" required tabindex="40" />
									</div>

									<div class="form-group col-md-3 mb-md">
										<label class="control-label mb-n"> Nombre Comercial: <small class="text-danger">(*)</small> </label>
										<input type="text" class="form-control input-sm" ng-model="fData.nombre_comercial" placeholder="Ingrese nombre comercial" required tabindex="60" />
									</div>
									<div class="form-group col-md-6 mb-md">
										<label class="control-label mb-n"> Dirección: <small class="text-danger">(*)</small> </label>
										<input type="text" class="form-control input-sm" required ng-model="fData.direccion" placeholder="Ingrese direción legal" tabindex="80" />
									</div>
									
									<div class="col-sm-6"> <!-- -->
										<div class="row">
											<div class="form-group col-md-12 mb-md">
												<label class="control-label mb-n"> Referencia de ubicación: </label>
												<input type="text" class="form-control input-sm" ng-model="fData.referencia" placeholder="Ingrese alguna referencia" tabindex="90" />
											</div>
											<div class="form-group mb-md col-md-12 mb-md" >
												<label class="control-label mb-xs"> Departamento <small class="text-danger">(*)</small> </label>
												<div class="input-group">
													<span class="input-group-btn">
														<input type="text" class="form-control input-sm" style="width:40px;margin-right:4px;" ng-model="fData.iddepartamento" placeholder="ID" tabindex="100" ng-change="obtenerDepartamentoPorCodigo(); $event.preventDefault();limpiaDpto();" min-length="2" required/>
													</span>
													<input id="fDatadepartamento" type="text" class="form-control input-sm" ng-model="fData.departamento" 
														placeholder="Ingrese el Departamento" typeahead-loading="loadingLocationsDpto" 
														uib-typeahead="item as item.descripcion for item in getDepartamentoAutocomplete($viewValue)" 
														typeahead-on-select="getSelectedDepartamento($item, $model, $label)" 
														typeahead-min-length="2" ng-change="limpiaIdDpto();" tabindex="110" autocomplete="off" required /> 
												</div>
												<i ng-show="loadingLocationsDpto" class="fa fa-refresh"></i>
									            <div ng-show="noResultsLD">
									              <i class="fa fa-remove"></i> No se encontró resultados 
									            </div>
											</div>
											<div class="form-group mb-md col-md-12 mb-md" >
												<label class="control-label mb-xs"> Provincia <small class="text-danger">(*)</small> </label>
												<div class="input-group">
													<span class="input-group-btn ">
														<input type="text" class="form-control input-sm" style="width:40px;margin-right:4px;" ng-model="fData.idprovincia" placeholder="ID" tabindex="120" ng-change="obtenerProvinciaPorCodigo(); $event.preventDefault();limpiaProv();" min-length="2" required/>
													</span>
													<input id="fDataprovincia" type="text" class="form-control input-sm" ng-model="fData.provincia" 
														placeholder="Ingrese la Provincia" typeahead-loading="loadingLocationsProv" 
									              		uib-typeahead="item as item.descripcion for item in getProvinciaAutocomplete($viewValue)" 
									              		typeahead-on-select="getSelectedProvincia($item, $model, $label)" typeahead-min-length="2" 
									              		ng-change="limpiaIdProv();" tabindex="130" autocomplete="off" required/>
												</div>
												<i ng-show="loadingLocationsProv" class="fa fa-refresh"></i>
									            <div ng-show="noResultsLP">
									              <i class="fa fa-remove"></i> No se encontró resultados 
									            </div>
											</div>
											<div class="form-group mb-md col-md-12 mb-md" >
												<label class="control-label mb-xs"> Distrito <small class="text-danger">(*)</small> </label>
												<div class="input-group">
													<span class="input-group-btn ">
														<input type="text" class="form-control input-sm" style="width:40px;margin-right:4px;" ng-model="fData.iddistrito" placeholder="ID" tabindex="140" ng-change="obtenerDistritoPorCodigo(); $event.preventDefault();limpiaDist();" min-length="2" required />
													</span>
													<input id="fDatadistrito" type="text" class="form-control input-sm" ng-model="fData.distrito" 
														placeholder="Ingrese el Distrito"  typeahead-loading="loadingLocationsDistr" uib-typeahead="item as item.descripcion for item in getDistritoAutocomplete($viewValue)" 
														typeahead-on-select="getSelectedDistrito($item, $model, $label)" typeahead-min-length="2" 
														ng-change="limpiaIdDist();" tabindex="150" autocomplete="off" required/> 
												</div>
												<i ng-show="loadingLocationsDistr" class="fa fa-refresh"></i>
									            <div ng-show="noResultsLDis">
									              <i class="fa fa-remove"></i> No se encontró resultados 
									            </div>
											</div>
										</div> 
									</div> 
									<div class="col-sm-6"> 
										<div class="input-group mb-xs" style="width: 100%;">
											<input type="text" class="form-control input-xs" ng-change="changeTextUbicacion();" ng-model="fData.ubicacion" placeholder="Busque ubicación" style="width: 50%;">
										    <input type="text" class="form-control input-xs" placeholder="Latitud" ng-model="fData.lat" style="width: 15%;">
										    <input type="text" class="form-control input-xs" placeholder="Longitud" ng-model="fData.lng" style="width: 15%;">
										    <button type="button" class="btn btn-xs btn-success pull-right" ng-click="generateMap(fData.lat,fData.lng);"> BUSCAR </button>
										</div>
										<div id="mapProveedor" style="height: 230px;"></div> 
									</div> 
								</div> 
	          	</div>
	          </div>
	          <div class="tab-pane" id="usuario" ng-hide="accion == 'edit'">
	            <div class="wrapper-nn">
	          		<div class="form-group col-md-6 mb-md">
									<label class="control-label mb-n"> Usuario: </label> 
									<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.username" placeholder="Ingrese usuario" tabindex="100" />
								</div>
						    <div class="form-group col-md-6 mb-md">
									<label class="control-label mb-n"> Tipo Usuario: </label>
						 			<select class="form-control input-sm" ng-model="fData.tipo_usuario" disabled ng-options="item as item.descripcion for item in fArr.listaTipoUsuario" tabindex="110" ></select> 
								</div>
						    <div class="form-group col-md-6 mb-md">
									<label class="control-label mb-n"> Ingrese Contraseña: </label>
									<input type="password" class="form-control input-sm" ng-model="fData.password_view" placeholder="Registre contraseña" tabindex="120" />
								</div>
						    <div class="form-group col-md-6 mb-md">
									<label class="control-label mb-n"> Repita la Contraseña: </label>
									<input type="password" class="form-control input-sm" ng-model="fData.password" placeholder="Repita contraseña" tabindex="130" />
								</div>
	            </div>
	          </div>
	          <div class="tab-pane" id="contacto" ng-hide="accion == 'edit'">
	            <div class="wrapper-nn">
		            <div class="row">
		            	<div class="col-sm-4 col-xs-12" class=""> 
										<fieldset class="fieldset-sm {{ editClassForm }}">
											
											<div class="form-group">
												<label class="control-label"> Nombres: </label>
									      <input type="text" class="form-control input-sm" ng-model="fContacto.nombres" placeholder="Ingrese nombres" tabindex="20" /> 
											</div>
											<div class="form-group">
												<label class="control-label"> Apellidos: </label>
									      <input type="text" class="form-control input-sm" ng-model="fContacto.apellidos" placeholder="Ingrese apellidos" tabindex="30" /> 
											</div>
											<div class="form-group"> 
												<div class="inline">
									        <label class="control-label"> Cargo/Area: </label>
									        <input type="text" class="form-control input-sm" ng-model="fContacto.cargo" placeholder="Ingrese cargo o área" tabindex="40" /> 
									      </div>
												<div class="inline"> 
													<label class="control-label text-ellipsis"> Teléfono Movil: </label>
									        <input type="tel" class="form-control input-sm" ng-model="fContacto.telefono_movil" placeholder="Ingrese tel. movil" tabindex="50" /> 
												</div>
											</div> 
											<div class="form-group">
												<div class="inline mr-sm">
													<label class="control-label"> Teléfono Fijo: </label>
										      <input type="tel" class="form-control input-sm" ng-model="fContacto.telefono_fijo" placeholder="Ingrese tel. fijo" tabindex="60" /> 
										    </div>
										    <div class="inline">
										      <label class="control-label"> Anexo: </label>
									        <input type="text" class="form-control input-sm" ng-model="fContacto.anexo" placeholder="Anexo" tabindex="70" /> 
										    </div>
											</div>
											<div class="form-group">
												<label class="control-label"> E-mail: </label> 
									      <input type="email" class="form-control input-sm" ng-model="fContacto.email" placeholder="Ingrese correo electrónico" tabindex="80" /> 
											</div> 
											<div class="form-group" ng-if="contBotonesReg">
												<button type="button" ng-click="agregarContacto(); $event.preventDefault();" ng-disabled="formContactoProveedor.$invalid" tabindex="100" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR CONTACTO </button>
											</div> 

											<div class="form-group" ng-if="contBotonesEdit">
												<button type="button" ng-click="actualizarContacto(); $event.preventDefault();" tabindex="110" ng-disabled="formContactoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR CONTACTO </button>
												<button type="button" ng-click="quitarContacto(); $event.preventDefault();" tabindex="120" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR CONTACTO </button>
											</div> 
										</fieldset>	
									</div>
									<div class="col-sm-8 col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<div  ui-grid="gridOptionsContactos" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
											</div>
										</div>
									</div>
		            </div>
	            </div>
	          </div>
	        </div>
	    	</div>
			</div>
		</div> 
	</form> 
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" 
    	ng-disabled="formProveedor.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 