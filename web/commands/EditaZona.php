<?php

include_once('GenericCommand.php');
include_once('../classes/Zona.php');
include_once('../classes/Region.php');
include_once('../classes/ZonaRegion.php');

class EditaZona extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		$id_zona = null;
				
		// ayuda en pantalla
		$user_help_desk = 
			"Permite modificar el nombre de una zona, as&iacute; como las regiones que le pertenecen. Para incluir una regi&oacute;n se debe chequear la casilla correspondiente. Para quitar una, la casilla se debe desmarcar.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
				
		// recuerdo los parametros de busqueda para el comando 'volver', donde se submiteara a ReporteExistancias
		$ar = HTTP_session::get('search_keyword_zona', null);
		
		if (!empty($ar)) {
			$this->addVar('sel_id_zona', $ar);
		}
		
		// regiones en checkboxes... pertenecientes y vacantes
		
		$z = $fc->request->zona;
		
		if (!isset($z)) {
			// llamado desde zonas
			$id_zona = $fc->request->id;
			
			$parameters = array(
				'posible'	=> $id_zona,
			);
			
			$regiones = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
		}
		else {
			// submit
			$exito = null;
			
			$id_zona = $fc->request->id_zona;
			
			$parameters = array(
				'posible'	=> $id_zona,
			);
			
			$regiones = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
			
			$zona = Zona::getByID($db, $id_zona);
									
			try {
				
				$bInTransaction = false;
								
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					// cambio el nombre?
					if ($zona->descripcion != $fc->request->zona) {
						$zona->descripcion = $fc->request->zona;
						
						$zona->update($db);
					}
					
					// regiones pertenecientes a la zona
					$ar_regiones = $fc->request->region;
					
					ZonaRegion::delete($db, $id_zona);
					
					foreach ($ar_regiones as $k => $v) {
						$zona_region = new ZonaRegion();

						$zona_region->id_zona = $id_zona;
						$zona_region->id_region = $regiones[$k]['id'];
						
						$zona_region->insert($db);
					}
															
					// commit
					
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Zona modificada exitosamente';
					
				} catch (Exception $e) {
					// rollback
					if ($bInTransaction) {
						$db->TransactionRollback();
					}
					
					throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
				}
			} catch (Exception $e) {
				// estatus fracaso
				$exito = false;
				
				$status_message = 'La zona no pudo ser modificada. Raz&oacute;n: ' . $e->getMessage();
			}			
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			$fv=array();
			
			$fv[0]="zona";
			
			$this->initFormVars($fv);
		}
		
		$zona = Zona::getByID($db, $id_zona);
		
		$this->addVar('id_zona', $id_zona);
		$this->addVar('zona', $zona->descripcion);
		
		$parameters = array(
			'id_zona'	=> "$id_zona",
		);
		
		$regiones_propias = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
		
		$this->addVar('regiones_propias', $regiones_propias);
		
		$this->addVar('regiones', $regiones);
				
		$this->processSuccess();

	}
}
?>