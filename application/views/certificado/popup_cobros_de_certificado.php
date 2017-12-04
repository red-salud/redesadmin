<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="form-group block col-sm-4 mb-xs">
			<label class="control-label"> Canal/Cliente: </label>
            <p class="text-bold text-ellipsis"> {{ fData.canal_cliente }} </p> 
		</div>
		<div class="form-group block col-sm-4 mb-xs">
			<label class="control-label"> Plan: </label>
            <p class="text-bold text-ellipsis"> {{ fData.plan }} </p> 
		</div>
		<div class="form-group block col-sm-4 mb-xs">
			<label class="control-label"> N° Certificado: </label>
            <p class="text-bold"> {{ fData.num_certificado }} </p> 
		</div>
		<div class="form-group block col-sm-12 mb-xs">
			<label class="control-label"> Contratante: </label>
            <p class="text-bold"> {{ fData.contratante }} </p> 
		</div>
		<div class="col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div ui-grid="gridOptionsCobro" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid">
						<div class="waterMarkEmptyData" ng-show="!gridOptionsCobro.data.length"> No se encontraron cobros. </div> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 