<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<form name="formCondicionesPlan" class=""> 
		<!-- <div class="row">
			<div class="col-xs-12"> 
		        <div class="inline pull-right"> 
		        	<button class="btn m-b-xs btn-success btn-xs" ng-click="btnNuevoContacto();">
	        			<i class="fa fa-file-text"></i> Nuevo Contacto </button> 
		        </div>
			</div>
		</div>  -->
		<div class="row"> 
			<div class="inline-block col-sm-12"> 
                <table class="table table-striped table-condensed table-hover m-b-none" style="font-size: 12px;"> 
                  <thead>
                    <tr>
                      <th> ID </th>
                      <th> CONDICIÓN </th>
                      <th> DESCRIPCIÓN </th> 
                      <!-- <th></th>  -->
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="cond in fArr.listaCondicionesPlan">
                      <td> {{ cond.idplandetalle }} </td> 
                      <td style="width: 100px;"> 
                        <span ng-class="">  {{ cond.nombre_var }}</span> 
                      </td>
                      <td class="" style="min-width: 140px;"> {{ cond.texto_web }} </td> 
                      <!-- <td>
                        <button ng-disabled="row.estado_atencion.valor = 2 || row.estado_atencion.valor = 3" ng-click="reservarCita(aseg);" class="btn btn-primary btn-xs"> RESERVAR </button>
                      </td> -->
                    </tr>
                  </tbody>
                </table>
                <!-- <div class="inline-block col-sm-12" ng-if="row.asegurados.length == 0"> 
                  <div class="waterMarkEmptyData" style="top: 10px;font-size: 22px;position: relative;opacity: 0.6;"> No se encontraron asegurados </div> 
                </div> -->
              </div>
			<!-- <div ui-grid="gridOptionsCP" ui-grid-pagination ui-grid-selection ui-grid-resize-columns ui-grid-auto-resize class="grid table-responsive fs-mini-grid"></div>  -->
		</div>
	</form>
</div>
<div class="modal-footer"> 
    <button class="btn btn-warning" ng-click="cancel();">Cerrar</button> 
</div> 