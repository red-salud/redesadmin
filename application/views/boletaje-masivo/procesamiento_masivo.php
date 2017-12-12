<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form class="row" name="procesamientoMasivo"> 
		<div class="form-group col-md-6 mb-md">
			<label class="control-label text-ellipsis"> Comprobante </label>
             <select class="form-control input-sm" ng-model="fData.tipo_documento_mov" tabindex="190"  ng-options="item as item.descripcion for item in fArr.listaTipoDocumentoMov"> </select> 
		</div>
		<div class="form-group col-md-6 mb-md">
			<label class="control-label text-ellipsis"> Serie </label>
            <select class="form-control input-sm" ng-options="item as item.descripcion for item in fArr.listaSeries" 
              	tabindex="190" ng-model="fData.serie"> </select> 
        </div> 
        <div class="form-group col-md-6 mb-md">
        	<label class="control-label text-ellipsis"> Glosa/Concepto </label> 
            <select class="form-control input-sm" ng-model="fData.concepto"	tabindex="220" 
            	ng-options="item as item.descripcion for item in fArr.listaConceptos"> </select> 
        </div>
        <!-- <div class="form-group col-md-6 mb-md">
        	<label class="control-label text-ellipsis"> Moneda </label>
            <select class="form-control input-sm" ng-model="fData.moneda" ng-options="item as item.descripcion for item in fArr.listaMoneda" tabindex="180" ng-change="cambiarSimbolo();"> </select> 
        </div> -->
		<div class="form-group col-sm-12">
			<button type="button" class="btn btn-md btn-primary" ng-click="convertirAComprobanteExec();" style="width: 100%;"> PROCESAR... </button>
		</div>
		<div class="line line-dashed b-b line-sm"></div>
		<div class="form-group col-md-12 mb-xs"> 
			<label class="control-label mb-n text-primary"> Cantidad de cobros: </label> 
			<h5 class="col-md-12 m-n p-n text-primary" > 
	     	{{fData.fConsolidado.cant_cobros}} 
	    </h5>
		</div>
		<div class="form-group col-md-12 mb-xs"> 
			<label class="control-label mb-n text-success"> Cantidad de cobros procesados: </label> 
			<h5 class="col-md-12 m-n p-n text-success" > 
	     	{{fData.fConsolidado.cant_cobros_procesados}}
	    </h5>
		</div>
		<div class="form-group col-md-12 mb-xs"> 
			<label class="control-label mb-n text-danger"> Cantidad de cobros no procesados: </label> 
			<h5 class="col-md-12 m-n p-n text-danger" > 
	     	{{fData.fConsolidado.cant_cobros_no_procesados}}
	    </h5>
		</div> 
		<div class="col-sm-12">
			<a class="text-info block pull-right" style="text-decoration: underline;" ng-click="goToUrl('/app/historial-venta'); $event.preventDefault();" href=""> VER COBROS PROCESADOS </a> 
		</div>
	</form>
</div>
<div class="modal-footer"> 
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 