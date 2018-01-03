<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="envioCorreo">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group col-md-6 mb-md"> 
					<label class="control-label mb-n"> Asegurado: </label> 
					<h4 class="col-md-12 m-n p-n text-primary text-bold" title="{{fData.asegurado.asegurado}}" > {{fData.asegurado.asegurado}} </h4>
				</div>
				<div class="form-group col-md-6 mb-md"> 
					<label class="control-label mb-n"> Establecimiento: </label> 
					<h5 class="col-md-12 m-n p-n text-primary text-bold text-ellipsis" title="{{fData.proveedor.descripcion}}"> {{fData.proveedor.descripcion}} </h5> 
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="row">
				<div class="form-group col-md-12 ">
	        <uib-tabset justified="true" class="tab-container" active="fData.active"> 
	          <uib-tab index="0" heading="SOLICITUD DE CITA">
	          	<div class="form-group col-md-6 pl-n">
		          	<label> Remitente </label> 
		          	<input disabled type="text" ng-model="fCorreo.solicitud.remitente" class="form-control input-sm" placeholder="Correo remitente." /> 
		          </div>
		          <div class="form-group col-md-6 pl-n">
		          	<label> Cc. </label>
		          	<input type="text" ng-model="fCorreo.solicitud.remitente_copia" class="form-control input-sm" placeholder="Con copia." /> 
		          </div>
	          	<div class="form-group col-md-12 pl-n mb-n"> 
		          	<label style="display: block;"> Destinatario(s) <small class="hidden">{{ fCorreo.solicitud.destinatario }}</small> </label> 
		          	<input type="text" ui-jq="tagsinput" ng-model="fCorreo.solicitud.destinatario" ui-refresh="changeDestinatario();" ui-options="" class="tg-destinatario form-control w-md input-sm block" placeholder="Correo destinatario." /> 
		          </div>
		          <div class="form-group col-md-12 pl-n">
		          	<label> Título </label>
		          	 <input type="text" class="form-control input-sm" ng-model="fCorreo.solicitud.titulo" placeholder="Título del mensaje.">
		          </div>
		          <!-- <div class="form-group col-md-12 pl-n"> 
		          	<label> Cuerpo del Mensaje </label> 
		          	<textarea class="form-control input-sm" placeholder="Cuerpo del Mensaje." rows="6" ng-model="fCorreo.solicitud.cuerpo"> </textarea> 
		          </div> -->
		          <div class="form-group col-md-12 pl-n">
		          	<a href="mailto:{{fCorreo.solicitud.destinatario}}?cc={{fCorreo.solicitud.remitente_copia}}&subject={{fCorreo.solicitud.titulo}}&body={{fCorreo.solicitud.cuerpo}}" class="btn btn-info btn-sm pull-right"> <i class="fa fa-envelope"></i> ENVIAR A OUTLOOK </a> 
		          </div>
		          <div class="clearfix"></div>
	          </uib-tab>
	          <uib-tab index="1" heading="CONFIRMACIÓN DE CITA"> 
	          	<div class="form-group col-md-6 pl-n">
		          	<label> Remitente </label>
		          	<input disabled type="text" ng-model="fCorreo.confirmacion.remitente" class="form-control input-sm" placeholder="Correo remitente." /> 
		          </div>
		          <div class="form-group col-md-6 pl-n">
		          	<label> Cc. </label>
		          	<input type="text" ng-model="fCorreo.confirmacion.remitente_copia" class="form-control input-sm" placeholder="Con copia." /> 
		          </div>
	          	<div class="form-group col-md-12 pl-n mb-n">
		          	<label style="display: block;"> Destinatario(s) </label> 
		          	<input style="width: 100%;" type="text" ui-jq="tagsinput" ui-refresh="changeDestinatario();" ui-options="" ng-model="fCorreo.confirmacion.destinatario" 
		          		class="tg-destinatario form-control w-md input-sm block" placeholder="Correo destinatario." /> 
		          </div>
		          <div class="form-group col-md-12 pl-n">
		          	<label> Título </label>
		          	 <input type="text" class="form-control input-sm" ng-model="fCorreo.confirmacion.titulo" placeholder="Título del mensaje.">
		          </div>
	          	<!-- <div class="form-group col-md-12 pl-n">
		          	<label> Cuerpo del Mensaje </label>
		          	<textarea class="form-control input-sm" placeholder="Cuerpo del Mensaje." rows="6" ng-model="fCorreo.confirmacion.cuerpo"> </textarea> 
		          </div> -->
		          <div class="form-group col-md-12 pl-n">
		          	<a href="mailto:{{fCorreo.confirmacion.destinatario}}?cc={{fCorreo.confirmacion.remitente_copia}}&subject={{fCorreo.confirmacion.titulo}}&body={{fCorreo.confirmacion.cuerpo}}" class="btn btn-info btn-sm pull-right"> <i class="fa fa-envelope"></i> ENVIAR A OUTLOOK </a> 
		          </div>
		          <div class="clearfix"></div>
	          </uib-tab> 
	        </uib-tabset>
			  </div>
			</div>
		</div> 
	</form>
</div>
<div class="modal-footer">
    <!-- <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="envioCorreo.$invalid">Aceptar</button> -->
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 