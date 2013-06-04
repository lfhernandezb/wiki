<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Ciudad.php');
include_once('../classes/RadioEstacion.php');
include_once('../classes/Modelo.php');
include_once('../classes/Encargado.php');

class MueveRepuesto extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
				
		$m = "&nbsp;";
		
		$this->addVar("message", $m);        		
		
		// lleno combo regiones
		$regiones = Region::seek($db, array(), 'orden', 'ASC', 0, 10000);
					
		$this->addVar('regiones', $regiones);
				
		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		$repuesto = null;
		
		$exito = null;
		
		// ayuda en pantalla
		$user_help_desk = 
			"Mueve el repuesto a otra R/E";
		
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
			
		}
		else if (isset($fid)) {
			// submit, mueve repuesto... grabamos cambios
			
			$id = $fc->request->id;
						
			try {
				
				$bInTransaction = false;
								
				$status_message = '';
				
				try {
					
					$id_ciudad = null;
					$id_radio_estacion = null;
					
					$ciudad = null;
					$re = null;
					$encargado = null;
					
					$repuesto = Repuesto::getById($db, $id);
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					if ($fc->request->mr_ciudad == 9999) {
						// nueva ciudad
						$ciudad = new Ciudad();
						
						$ciudad->id_region = $fc->request->mr_region;
						
						$ciudad->descripcion = $fc->request->otro_ciudad;
						
						$ciudad->insert($db);
						
						$fc->request->mr_ciudad = $ciudad->id;
						
						// refresco opciones ciudad post commit
						
						$parameters = array(
							'id_region' => "{$fc->request->mr_region}",
						);
						
						$ar = Ciudad::seek($db, $parameters, 'descripcion', 'ASC', null, null);
						
						$data = "{\"ciudades\": [";
						
						$i = 0;
						foreach ($ar as $ciudad) {
							$data .= "{\"id\": \"{$ciudad['id']}\", \"descripcion\": \"{$ciudad['descripcion']}\"}";
							$i++;
							if ($i < count($ar)) {
								$data .= ', ';
							}
						}
						
						$data .= "]}";
												
						HTTP_session::set('options_ciudades', $data);
						
					}
					
					// si se elige otra ciudad, el select de R/E se esconde y $fc->request->ar_re queda indefinido
					$aux_re = $fc->request->mr_re; 
					
					if ($aux_re == 9999 || !isset($aux_re)) {
						// nueva R/E
						$re = new RadioEstacion();
						
						$re->id_ciudad = $fc->request->mr_ciudad;
						$re->descripcion = $fc->request->otro_re;
						
						$re->insert($db);
						
						$fc->request->mr_re = $re->id;

						// refresco opciones R/E post commit
						
						$parameters = array(
							'id_ciudad' => "{$fc->request->mr_ciudad}",
						);
						
						$ar = RadioEstacion::seek($db, $parameters, 'descripcion', 'ASC', null, null);
						
						$data = "{\"res\": [";
						
						$i = 0;
						foreach ($ar as $re) {
							$data .= "{\"id\": \"{$re['id']}\", \"descripcion\": \"{$re['descripcion']}\"}";
							$i++;
							if ($i < count($ar)) {
								$data .= ', ';
							}
						}
						
						$data .= "]}";
												
						HTTP_session::set('options_res', $data);
					}
					
					if ($fc->request->encargado == 9999) {
						// nuevo encargado
						$encargado = new Encargado();
						
						// pueden venir caracteres <, >, que vendran como entidades html
						$encargado->descripcion = html_entity_decode($fc->request->otro_encargado);
						
						$encargado->insert($db);
						
						$fc->request->encargado = $encargado->id;
						
						$this->addVar('encargado', $fc->request->encargado);
						
						// refresco listado de encargados mas abajo, despues de actualizar el repuesto
						
					}
					
					$repuesto->id_radio_estacion = $fc->request->mr_re;
					$repuesto->id_encargado = $fc->request->encargado;
					
					$repuesto->update($db);
															
					if (!empty($encargado)) {
						// nuevo encargado

						// refresco listado de encargados; necesito el repuesto actualizado para que funcione
						
						$parameters = array(
							'id_region' => $fc->request->mr_region,	
						);
						
						$ar = Encargado::seek($db, $parameters, 'descripcion', 'ASC', 0, 100000);
									
						$data = "{\"encargados\": [";
						
						$i = 0;
						foreach ($ar as $encargado) {
							$data .= "{\"id\": \"{$encargado['id']}\", \"descripcion\": \"{$encargado['descripcion']}\"}";
							$i++;
							if ($i < count($ar)) {
								$data .= ', ';
							}
						}
						
						$data .= "]}";
						
						HTTP_session::set('options_encargados', $data);
					}
					
					// commit
					
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Repuesto movido exitosamente';

					// recuerdo el id para validar si el usuario actualiza mas de una vez
					$this->addVar('id', $id_repuesto, null);
					
					// opciones en combo de ciudades
					$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
					
					// opciones en combo de radio estaciones
					$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
					
					// opciones en combo de encargados
					$this->addVar('options_encargados', HTTP_Session::get('options_encargados', null), null);
					
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
				
				$status_message = 'Repuesto no pudo ser movido. Raz&oacute;n: ' . $e->getMessage();
			}
							
			$this->addVar("status_message", $status_message);
						
			$fv=array();
			/*			
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
			*/
			$fv[0]="mr_region";
			$fv[1]="mr_ciudad";
			$fv[2]="mr_re";
			
			
			$this->initFormVars($fv);
			
		}
		
		$this->addVar("exito", $exito);
		
		// recuerdo el id para grabar cambios en caso de mover repuesto
		$this->addVar('id', $id, null);
		
		// cargo en los controles los parametros del repuesto elegido
		$this->addVar('txt_plataforma', $repuesto->plataforma);
		$this->addVar('txt_region', sprintf("%s %s", $repuesto->region, $repuesto->region_nombre));
		$this->addVar('txt_ciudad', $repuesto->ciudad);
		$this->addVar('txt_re', $repuesto->radio_estacion);
		$this->addVar('txt_descripcion', $repuesto->descripcion);
		$this->addVar('txt_fabricante', $repuesto->fabricante);
		$this->addVar('txt_modelo', $repuesto->modelo);
		$this->addVar('txt_serial', $repuesto->serial);
		$this->addVar('txt_nombre', $repuesto->nombre);
		$this->addVar('txt_sla', $repuesto->sla);
		/*
		$this->addVar('posicion', $repuesto->posicion);
		$this->addVar('ip', $repuesto->ip);
		$this->addVar('consola_activa', $repuesto->consola_activa);
		$this->addVar('consola_standby', $repuesto->consola_standby);
		$this->addVar('ubicacion', $repuesto->ubicacion);
		*/
		
		$this->processSuccess();

	}
}
?>