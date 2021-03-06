<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="form-group block col-sm-4 mb-sm">
			<label class="control-label"> Razón Social: </label>
            <p class="text-bold text-ellipsis"> {{ fData.razon_social }} </p> 
		</div>
		<div class="form-group block col-sm-4 mb-sm">
			<label class="control-label"> Nombre Comercial: </label>
            <p class="text-bold text-ellipsis"> {{ fData.nombre_comercial }} </p> 
		</div>
		<div class="form-group block col-sm-4 mb-sm">
			<label class="control-label"> RUC: </label>
            <p class="text-bold"> {{ fData.numero_documento }} </p> 
		</div>
		<div class="col-sm-4 col-xs-12"> 
			<form name="formContactoEmpresa" class="pt-n {{ editClassForm }} "> 
				<fieldset class="fieldset-sm">
					<legend class="mb-sm "> {{ tituloBloque }} </legend> 
					<div class="form-group">
						<label class="control-label"> Nombres: <small class="text-danger">(*)</small> </label>
			            <input type="text" class="form-control input-sm" ng-model="fContacto.nombres" placeholder="Ingrese nombres" required tabindex="20" /> 
					</div>
					<div class="form-group">
						<label class="control-label"> Apellidos: </label>
			            <input type="text" class="form-control input-sm" ng-model="fContacto.apellidos" placeholder="Ingrese apellidos" tabindex="30" /> 
					</div>
					<div class="form-group"> 
						<div class="inline mr-sm">
					        <label class="control-label"> Cargo/Area: </label> 
					        <select class="form-control input-sm" ng-model="fContacto.cargo_contacto" tabindex="40" style="width: 128px;"
					        	ng-options="item as item.descripcion for item in fArr.listaCargosContacto"> </select> 
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
					<div class="form-group">
						<label class="control-label"> ¿Enviar correos para separa cita?: </label> 
			      <input type="checkbox" class="" ng-model="fContacto.chk_envio_correo" tabindex="85" /> 
					</div> 
					<div class="form-group" ng-if="contBotonesReg">
						<button type="button" ng-click="agregarContacto(); $event.preventDefault();" ng-disabled="formContactoEmpresa.$invalid" tabindex="100" class="block btn btn-primary btn-sm btn-full"> <i class="fa fa-plus"></i> AGREGAR CONTACTO </button>
					</div> 
					<div class="form-group" ng-if="contBotonesEdit">
						<button type="button" ng-click="actualizarContacto(); $event.preventDefault();" tabindex="110" ng-disabled="formContactoEmpresa.$invalid" class="block btn btn-primary btn-sm btn-block"> <i class="fa fa-edit"></i> ACTUALIZAR CONTACTO </button>
						<button type="button" ng-click="quitarContacto(); $event.preventDefault();" tabindex="120" class="block btn btn-danger btn-sm btn-block"> <i class="fa fa-trash"></i> QUITAR CONTACTO </button>
					</div> 
				</fieldset>	
			</form>
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
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 