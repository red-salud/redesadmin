<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row m-n" > 
		<fieldset>
			<legend class="lead lead-sm" style="font-size: 18px;"> Datos Generales </legend>
			<div class="row m-n">
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> RUC: </label>
		            <p title="{{ fData.numero_documento }}" class="text-primary"> {{ fData.numero_documento }} </p> 
				</div> 
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Razón Social: </label>
		            <p title="{{ fData.razon_social }}" class="text-primary text-ellipsis mr"> {{ fData.razon_social }} </p> 
				</div>
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Nombre Comercial: </label>
		            <p title="{{ fData.nombre_comercial }}" class="text-primary text-ellipsis mr"> {{ fData.nombre_comercial }} </p> 
				</div> 
			</div>
			<div class="row m-n">
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Dirección: </label>
		            <p title="{{ fData.direccion }}" class="text-primary text-ellipsis mr"> {{ fData.direccion }} </p> 
				</div> 
				<div class="form-group block col-sm-8 p-n mb-xs">
					<label class="control-label"> Referencia de ubicación: </label>  
		            <p title="{{ fData.referencia }}" class="text-primary text-ellipsis mr"> {{ fData.referencia }} </p> 
				</div> 
			</div>
			<div class="row m-n">
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Departamento: </label>  
		            <p class="text-primary"> {{ fData.departamento }} </p> 
				</div> 
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Provincia: </label> 
		            <p class="text-primary"> {{ fData.provincia }} </p> 
				</div> 
				<div class="form-group block col-sm-4 p-n mb-xs">
					<label class="control-label"> Distrito: </label>  
		            <p class="text-primary"> {{ fData.distrito }} </p> 
				</div>
			</div> 
		</fieldset> 
		<fieldset>
			<legend class="lead lead-sm" style="font-size: 18px;"> Credenciales de Acceso </legend> 
			<div class="form-group block col-sm-4 p-n mb-xs">
				<label class="control-label"> Usuario: </label>  
	            <p class="text-primary"> {{ fData.username }} </p> 
			</div> 
			<div class="form-group block col-sm-4 p-n mb-xs">
				<label class="control-label"> Clave: </label> 
	            <p class="text-primary"> {{ fData.password_view }} </p> 
			</div> 
			<div class="form-group block col-sm-4 p-n mb-xs">
				<label class="control-label"> Último inicio sesión: </label> 
	            <p class="text-primary"> {{ fData.ultimo_inicio_sesion }} </p> 
			</div> 
		</fieldset> 
		<fieldset>
			<legend class="lead lead-sm" style="font-size: 18px;"> Contactos </legend> 
			<div class="row">
				<div class="col-xs-12">
					<div ui-grid="gridOptionsContactos" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 