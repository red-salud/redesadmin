<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="agregarCita">
		<div class="col-md-6">
			<div class="row">
				<div class="form-group col-md-12 mb-md" ng-if="fData.accion == 'reg'"> 
					<label class="control-label mb-n"> Asegurado/Cert.: </label> <!--  -->
					<h4 class="col-md-12 m-n p-n text-primary text-bold" > 
	      			{{ fPrimerDato.asegurado_cert.asegurado }}
	      	</h4>
				</div>
				<div class="form-group col-md-12 mb-md" ng-if="fData.accion == 'edit'"> 
					<label class="control-label mb-n"> Asegurado/Cert.: </label> <!--  -->
					<h4 class="col-md-12 m-n p-n text-primary text-bold" > 
	      			{{ fData.asegurado.asegurado }} 
	      	</h4>
				</div>
				<div class="form-group col-md-12 mb-md"> 
					<label class="control-label mb-n"> Proveedor: <small class="text-danger">(*)</small> </label>
					<div class="input-group">
		        <ui-select ng-model="fData.proveedor" theme="bootstrap" tabindex="110">
		          <ui-select-match placeholder="Seleccione un proveedor...">{{ fData.proveedor.descripcion }} </ui-select-match>
		          <ui-select-choices repeat="item in fArr.listaProveedores | propsFilter: {descripcion: $select.search}"> 
		            <span ng-bind-html="item.descripcion | highlight: $select.search"></span> 
		          </ui-select-choices>
		        </ui-select>
		        <span class="input-group-btn">
		          <button type="button" ng-click="fData.proveedor = undefined" class="btn btn-default" tabindex="120">
		            <span class="glyphicon glyphicon-trash"></span>
		          </button>
		        </span>
		      </div>
				</div> 
				<div class="form-group col-md-12 mb-md"> 
					<label class="control-label mb-n"> Producto/Servicio: <small class="text-danger">(*)</small> </label>
					<select class="form-control input-sm" ng-model="fData.producto" required tabindex="150" 
						ng-options="item as item.descripcion for item in fArr.listaProductos" ></select> 
				</div> 
				<div class="form-group col-md-12">
		    	<label for="name" class="control-label mb-n">Observaciones : </label> 
		    	<textarea placeholder="Digite sus anotaciones y/o cualquier dato adicional..." rows="5" ng-model="fData.observaciones" class="form-control input-sm"></textarea> 
			  </div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="form-group col-md-12">
			    <label for="name" class="control-label mb-n"> Fecha <span class="text-danger">*</span>:  </label>
			    <p class="input-group mb-n">
		        <input type="text" required class="form-control input-sm" uib-datepicker-popup ng-model="fData.fecha" is-open="configDP.popup.opened" 
		        	datepicker-options="configDP.dateOptions" close-text="Cerrar" tabindex="130" />
		        <span class="input-group-btn">
		        	<button type="button" class="btn btn-default btn-sm" tabindex="140" ng-click="configDP.open()"><i class="fa fa-calendar"></i></button>
		      	</span>
		      </p>
			  </div>
			  <div class="form-group col-md-6">
			    <label for="name" class="control-label mb-n">Hora inicio <span class="text-danger">*</span>:  </label>
			    <div uib-timepicker required ng-model="fData.hora_desde" ng-change="actualizarHoraFin();" hour-step="configTP.tpHoraInicio.hstep" 
		      	minute-step="configTP.tpHoraInicio.mstep" show-meridian="configTP.tpHoraInicio.ismeridian" tabindex="160">
			   	</div>
			  </div>
			  <div class="form-group col-md-6">
		    	<label for="name" class="control-label mb-n">Hora Fin <span class="text-danger">*</span>: </label>
		    	<div uib-timepicker required ng-model="fData.hora_hasta" hour-step="configTP.tpHoraFin.hstep" 
		    		minute-step="configTP.tpHoraFin.mstep" show-meridian="configTP.tpHoraFin.ismeridian" tabindex="170">
		    	</div>
			  </div> 
			  <div class="form-group col-md-12 mb-md"> 
					<label class="control-label mb-n"> Estado: <small class="text-danger">(*)</small> </label>
					<select ng-disabled="fData.idestadocitafijo == 3" class="form-control input-sm" ng-model="fData.estado_cita" required tabindex="150" 
						ng-options="item as item.descripcion for item in fArr.listaEstadosCita" ></select> 
					<div ng-if="fData.estado_cita.id == 1" class="bg-warning" style="height: 20px;"></div>
					<div ng-if="fData.estado_cita.id == 2" class="bg-primary" style="height: 20px;"></div>
					<div ng-if="fData.estado_cita.id == 3" class="bg-success" style="height: 20px;"></div>
				</div>
			</div>
		</div> 
		
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="agregarCita.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 