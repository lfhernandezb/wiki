<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Motivo.php');
include_once('../classes/MotivoMovimiento.php');

class UtilizaRepuesto extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$repuesto = null;
		
		$parameters = array();
		
		// lleno combo plataformas
		$motivos = Motivo::seek($db, $parameters, null, null, 0, 10000);
					
		$this->addVar('motivos', $motivos);

		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		// ayuda en pantalla
		$user_help_desk = 
			"Hace uso de un repuesto por falla o TP. Los campos marcados con * son obligatorios.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		// desde que form se hizo submit en 'DisplayHome'?
		
		$key1 = HTTP_session::get('search_keyword', null);
		
		$key2 = HTTP_session::get('search_keyword_2', null);
		
		if (!empty($key1)) {
			// submit en 'DisplayHome' desde busqueda rapida
			$this->addVar('search_keyword', HTTP_Session::get('search_keyword', ''));
		}
		else if (!empty($key2)) {
			// submit en 'DisplayHome' desde busqueda con selects
			$this->addVar('plataforma', $key2['id_plataforma']);
			$this->addVar('region', $key2['id_region']);
			$this->addVar('ciudad', $key2['id_ciudad']);
			$this->addVar('re', $key2['id_re']);
			$this->addVar('fabricante', $key2['id_fabricante']);
			$this->addVar('modelo', $key2['id_modelo']);
		}
		else {
			// llamado desde menu... no habrá boton 'Volver al listado'
		}
		
		if (isset($id)) {
			// llamado desde home
			$repuesto = Repuesto::getById($db, $id);
			
			/*
			// cargo en los controles los parametros del repuesto elegido
			$this->addVar('plataforma', $repuesto->plataforma);
			$this->addVar('region', $repuesto->region);
			$this->addVar('ciudad', $repuesto->ciudad);
			$this->addVar('re', $repuesto->radio_estacion);
			$this->addVar('descripcion', $repuesto->descripcion);
			$this->addVar('fabricante', $repuesto->fabricante);
			$this->addVar('modelo', $repuesto->modelo);
			$this->addVar('serial', $repuesto->serial);
			$this->addVar('nombre', $repuesto->nombre);
			$this->addVar('sla', $repuesto->sla);
			
			$this->addVar('posicion', $repuesto->posicion);
			$this->addVar('ip', $repuesto->ip);
			$this->addVar('consola_activa', $repuesto->consola_activa);
			$this->addVar('consola_standby', $repuesto->consola_standby);
			$this->addVar('ubicacion', $repuesto->ubicacion);
			*/
			// recuerdo el id para grabar cambios en caso de utilizar repuesto
			$this->addVar('id', $id, null);
			
		}
		else if (isset($fid)) {
			// submit, ocupa repuesto... grabamos cambios
			
			$exito = null;
			
			// recuerdo el id para validar si el usuario actualiza mas de una vez
			$this->addVar('id', $fid, null);
			
			$this->addVar('posicion', $fc->request->posicion);
			$this->addVar('ip', $fc->request->ip);
			$this->addVar('consola_activa', $fc->request->consola_activa);
			$this->addVar('consola_standby', $fc->request->consola_standby);
			$this->addVar('ubicacion', $fc->request->ubicacion);
			
			$this->addVar('motivo', $fc->request->motivo);
			$this->addVar('valor', $fc->request->valor);
			$this->addVar('retiro', $fc->request->retiro);
			
			
			try {
			
				$trm = TipoRepuestoMovimiento::getByDescripcion($db, 'egreso');
				
				if (!isset($trm)) {
					throw new Exception('Error al obtener tipo de movimiento de egreso: registro no existe', null);
				}
								
				$bInTransaction = false;
				
				$repuesto = Repuesto::getById($db, $fid);
				
				$repuesto->posicion = $fc->request->posicion;
				$repuesto->ip = $fc->request->ip;
				$repuesto->consola_activa = $fc->request->consola_activa;
				$repuesto->consola_standby = $fc->request->consola_standby;
				$repuesto->ubicacion = $fc->request->ubicacion;
				
				//$repuesto->comentario = $fc->request->comentario;
				
				$retiro = null;
				
				// chequeo si el repuesto ya fue marcado como utilizado
				if ($repuesto->cantidad < 1) {
					throw new Exception('El repuesto ya est&aacute; marcado como utilizado', null);
				}
				else if ($repuesto->cantidad == 1) {
					$repuesto->cantidad = 0;
					$retiro = 1;
				}
				else {
					$repuesto->cantidad = $repuesto->cantidad - $fc->request->retiro;
					$retiro = $fc->request->retiro;
				}
				
				if ($repuesto->cantidad == 0) {
					$repuesto->utilizado = 1;
				}
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$repuesto->update($db);
					
					
					$rm = new RepuestoMovimiento();
					
					$rm->id_repuesto = $fid;
					$rm->id_tipo_repuesto_movimiento = $trm->id;
					$rm->id_usuario = $this->usuario->id;
					$rm->cantidad = $retiro;
					$rm->fecha = 'now()';
					
					$rm->insert($db);
					
					$mm = new MotivoMovimiento();
					
					$mm->id_repuesto_movimiento = $rm->id;
					$mm->id_motivo = $fc->request->motivo;
					$mm->valor = $fc->request->valor;
					
					$mm->insert($db);
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Repuesto marcado como utilizado exitosamente';
					
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
				
				$status_message = 'Repuesto no pudo ser marcado como utilizado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			/*
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			$fv[0]="plataforma";
			$fv[1]="region";
			$fv[2]="ciudad";
			$fv[3]="re";
			$fv[4]="descripcion";
			$fv[5]="fabricante";
			$fv[6]="modelo";
			$fv[7]="serial";
			$fv[8]="nombre";
			$fv[9]="sla";
			
			$fv[10]="posicion";
			$fv[11]="ip";
			$fv[12]="consola_activa";
			$fv[13]="consola_standby";
			$fv[14]="ubicacion";
			
			$this->initFormVars($fv);
			*/
		}
		
		// cargo en los controles los parametros del repuesto elegido
		$this->addVar('ur_plataforma', $repuesto->plataforma);
		//$this->addVar('ur_region', sprintf("%s %s", $repuesto->region, $repuesto->region_nombre));
		$aux = $repuesto->region . ' ' . $repuesto->region_nombre;
		$this->addVar('ur_region', $aux);
		$this->addVar('ur_ciudad', $repuesto->ciudad);
		$this->addVar('ur_re', $repuesto->radio_estacion);
		$this->addVar('ur_fabricante', $repuesto->fabricante);
		$this->addVar('ur_modelo', $repuesto->modelo);
		$this->addVar('ur_serial', $repuesto->serial);
		$this->addVar('ur_nombre', $repuesto->nombre);
		$this->addVar('ur_sla', $repuesto->sla);
		$this->addVar('ur_cantidad', $repuesto->cantidad);
						
		$this->processSuccess();

	}
}
?>