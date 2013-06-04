<?php

include_once('GenericCommand.php');
include_once('../classes/Zona.php');
include_once('../classes/Region.php');
include_once('../classes/ZonaRegion.php');

class AgregaZona extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		$id_zona = null;
				
		// ayuda en pantalla
		$user_help_desk = 
			"Permite agregar una nueva zona, agregando regiones de aquellas que no pertenecen a ninguna. Solamente se deben chequear las casillas de las regiones a pertenecer a la nueva zona";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
				
		// recuerdo los parametros de busqueda para el comando 'volver', donde se submiteara a ReporteExistancias
		$ar = HTTP_session::get('search_keyword_zona', null);
		
		if (!empty($ar)) {
			$this->addVar('sel_id_zona', $ar);
		}
		
		$parameters = array(
			'sin zona'	=> '',
		);
		
		// para checkboxes de regiones
		$regiones = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
		
		//$this->addVar('regiones', $regiones);
		
		$z = $fc->request->zona;
		
		if (!isset($z)) {
			// llamado desde zonas... cargo las regiones sin pertenencia
						
		}
		else {
			// submit
			$exito = null;
			
			$zona_desc = $fc->request->zona;
			
			$zona = new Zona();
			
			$zona->descripcion = $zona_desc;
						
			try {
				
				$bInTransaction = false;
								
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$zona->insert($db);
					
					$id_zona = $zona->id;
					
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
					
					$status_message = 'Zona creada exitosamente';
					
					$parameters = array(
						'id_zona'	=> "$id_zona",
					);
					
					$regiones_propias = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
					
					$this->addVar('regiones_propias', $regiones_propias);
					
					$parameters = array(
						'sin zona'	=> '',
					);
										
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
				
				$status_message = 'La zona no pudo ser agregada. Raz&oacute;n: ' . $e->getMessage();
			}			
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			$fv=array();
			
			$fv[0]="zona";
			
			$this->initFormVars($fv);
		}
		
		$this->addVar('regiones', $regiones);
				
		$this->processSuccess();

	}
}
?>