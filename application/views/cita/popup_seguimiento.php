<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="seguimientoCita">
		<div class="col-md-12">
			<div class="row">
				<div class="form-group col-md-6 mb-xs"> 
					<label class="control-label mb-n"> Asegurado: </label> 
					<h5 class="col-md-12 m-n p-n text-primary text-bold"> {{fData.asegurado.asegurado}} </h5> 
				</div> 
				<div class="form-group col-md-6 mb-xs"> 
					<label class="control-label mb-n"> Establecimiento: </label> 
					<h5 class="col-md-12 m-n p-n text-primary text-bold text-ellipsis"> {{fData.proveedor.descripcion}} </h5> 
				</div>
			</div>
		</div>
		<div class="col-md-12"> 
			<div class="form-group block pl-n mb-sm"> 
      	<label> Agregue una nueva incidencia y/o comentario </label> 
      	<textarea class="form-control input-sm" placeholder="Describa..." rows="3" ng-model="fSeg.contenido"> </textarea> 
      	<button type="button" class="btn btn-success btn-sm mt-sm btn-block" ng-click="agregarSeguimiento();"> AGREGAR </button>
      </div>
		</div>
		<div class="col-md-12">
			<small class="text-help">Se muestra las incidencias ordenadas por fecha de forma descendente. (el más actual arriba) </small>
			<div class="wrapper p" style="border: 1px solid;box-shadow: 1px 1px 1px #868686 inset;"> 
				<ul class="timeline" ng-if="fArr.listaSeguimiento[0]">
	        <li class="tl-header">
	          <div class="btn btn-success btn-xs">Ahora</div> 
	        </li>
	        <li class="tl-item tl-left" ng-repeat="row in fArr.listaSeguimiento"> 
	          <div class="tl-wrap b-{{row.estado_seguimiento.clase}}">
	            <span class="tl-date" style="font-size: 11px;"> {{ row.nombre_empleado }} </span> 
	            <div class="tl-content panel bg-{{row.estado_seguimiento.clase}} padder line-{{row.estado_seguimiento.clase}}" style="background-color: white;">
	              <span class="arrow arrow-{{row.estado_seguimiento.clase}} left pull-up hidden-left"></span>
	              <span class="arrow arrow-{{row.estado_seguimiento.clase}} right pull-up visible-left"></span>
	              <div class="" style="color: #626f77;" ng-show="row.estado_seguimiento.valor == 1"> {{ row.contenido }} </div> 
	              <div class="" style="color: #626f77;" ng-show="row.estado_seguimiento.valor == 0"> 
	              	<a href="" class="text-danger" ng-click="flagToggle = 1">Comentario anulado</a> 
	              	<span ng-show="row.estado_seguimiento.valor == 0 && flagToggle == 1">{{ row.contenido }}</span> 
	              </div> 
	            	<a uib-tooltip="Eliminar" ng-if="row.estado_seguimiento.valor == 1 && row.puedo_eliminar" ng-click="eliminarSeguimiento(row.idcitaseguimiento);" 
	            		class="pull-right btn btn-ico btn-xs" style="color:red;margin-top: 4px;">
	            		<i class="fa fa-trash"></i>
	            	</a>
	            	<small class="pull-right mt-sm text-info" style="font-size:10px;"> {{ row.fecha }} </small> 
	            </div>
	          </div>
	        </li>
	      </ul>
	      <div ng-if="!(fArr.listaSeguimiento[0])"> 
	      	No se encontraron seguimientos. 
	      </div>
      </div> 
		</div>
	</form>
</div>
<div class="modal-footer"> 
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button>
</div> 