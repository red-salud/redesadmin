<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="usuarioForm"> 
		<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Usuario: <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" autocomplete="off" ng-model="fData.username" placeholder="Ingrese usuario" required tabindex="100" />
		</div>
    	<div class="form-group col-md-6 mb-md">
			<label class="control-label mb-n"> Tipo Usuario: <small class="text-danger">(*)</small> </label>
 			<select class="form-control input-sm" ng-model="fData.tipo_usuario" ng-options="item as item.descripcion for item in fArr.listaTipoUsuario" required tabindex="110" ></select> 
		</div>
		<div ng-if="accion == 'reg'"> 
	    	<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Ingrese Contraseña: <small class="text-danger">(*)</small> </label>
				<input type="password" class="form-control input-sm" ng-model="fData.password_view" placeholder="Registre contraseña" required tabindex="120" />
			</div>
	    	<div class="form-group col-md-6 mb-md">
				<label class="control-label mb-n"> Repita la contraseña: <small class="text-danger">(*)</small> </label>
				<input type="password" class="form-control input-sm" ng-model="fData.password" placeholder="Repita contraseña" required tabindex="130" />
			 </div>
		</div>
		<div ng-if="accion == 'reg'">
			<div class="form-group col-md-12" ng-show="fData.tipo_usuario.key_tu=='key_proveedor'">
				<label class="control-label mb-n"> Asociar al proveedor: <small class="text-danger">(*)</small> </label>
				<div class="input-group"> 
					<input id="fDataproveedor" type="text" class="form-control input-sm" ng-model="fData.proveedor" placeholder="Ingrese el Proveedor" disabled /> 
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-sm" ng-click="asociarProveedor();">ASOCIAR</button>
					</span>
				</div>
			</div>
			<div class="form-group col-md-12" ng-show="fData.tipo_usuario.key_tu!='key_proveedor' && fData.tipo_usuario.key_tu">
				<label class="control-label mb-n"> Asociar al colaborador: <small class="text-danger">(*)</small> </label>
				<div class="input-group"> 
					<input id="fDatacolaborador" type="text" class="form-control input-sm" ng-model="fData.colaborador" 
						placeholder="Ingrese el Colaborador" disabled /> 
					<span class="input-group-btn">
						<button type="button" class="btn btn-default btn-sm" ng-click="asociarColaborador();" >ASOCIAR</button>
					</span>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="usuarioForm.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 