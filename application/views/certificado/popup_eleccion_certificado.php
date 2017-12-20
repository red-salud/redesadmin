<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div> 
<div class="modal-body">  
	<div class="row" > 
		<div class="col-sm-12 mb-xs">
			<table class="table table-striped table-condensed table-hover m-b-none"> 
              <thead>
                <tr>
                  <th>DNI Aseg.</th>
                  <th>N° Certificado </th>
                  <th>Plan</th> 
                  <th>Estado Cert.</th> 
                  <th></th> 
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="cert in fArr.listaCertificadosSeleccion"> 
                  <td class="" style="width: 220px;"> {{ cert.numero_doc_aseg }} </td>
                  <td class=""> {{ cert.num_certificado }} </td>
                  <td class=""> {{ cert.nombre_plan }} </td> 
                  <td class=""> {{ cert.estado_certificado.labelText }} </td> 
                  <td>
                    <button ng-if="cert.estado_certificado.valor == 1" ng-click="seleccionarCertificado(cert);" 
                    	class="btn btn-primary btn-xs"> SELECCIONAR </button> 
                  </td>
                </tr>
              </tbody>
            </table>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button class="btn btn-warning" ng-click="cancel()">Cerrar</button>
</div> 