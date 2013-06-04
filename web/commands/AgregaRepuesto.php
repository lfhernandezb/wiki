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

class AgregaRepuesto extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		/*
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword', HTTP_Session::get('search_keyword', ''));
		*/
		$m = "&nbsp;";
		
		$this->addVar("message", $m);        		
		
		// lleno combo plataformas
		$plataformas = Plataforma::seek($db, array(), 'descripcion', 'ASC', 0, 10000);
					
		$this->addVar('plataformas', $plataformas);
			
		// lleno combo regiones
		$regiones = Region::seek($db, array(), 'orden', 'ASC', 0, 10000);
					
		$this->addVar('regiones', $regiones);
		
		// lleno combo fabricantes
		$fabricantes = Fabricante::seek($db, '');
					
		$this->addVar('fabricantes', $fabricantes);
		
		// ayuda en pantalla
		$user_help_desk = 
			"Ingresa un repuesto individual. Los campos marcados con * son obligatorios. El n&uacute;mero serial debe ser &uacute;nico para el modelo.";
		
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
		
		if (empty($_POST)) {
			// llamado desde home
			
			/*
			// muestro campos limpios
			$v = '';
			$this->addVar('plataforma', $v);
			$this->addVar('region', $v);
			$this->addVar('ciudad', $v);
			$this->addVar('re', $v);
			$this->addVar('fabricante', $v);
			$this->addVar('modelo', $v);
			$this->addVar('descripcion', $v);
			$this->addVar('serial', $v);
			$this->addVar('nombre', $v);
			$this->addVar('sla', $v);
			$this->addVar('posicion', $v);
			$this->addVar('ip', $v);
			$this->addVar('consola_activa', $v);
			$this->addVar('consola_standby', $v);
			$this->addVar('ubicacion', $v);
			*/
		}
		else {
			// submit, agrega repuesto... grabamos cambios
			
			$exito = null;
						
			try {
				$id = $fc->request->id;
				
				if (isset($id)) {
					throw new Exception('El repuesto ya fue agregado', null);
				}
			
				$trm = TipoRepuestoMovimiento::getByDescripcion($db, 'ingreso');
				
				if (!isset($trm)) {
					throw new Exception('Error al obtener tipo de movimiento de ingreso: registro no existe', null);
				}
				
				$bInTransaction = false;
								
				$status_message = '';
				
				try {
					$id_plataforma = null;
					$id_ciudad = null;
					$id_radio_estacion = null;
					$id_fabricante = null;
					$id_modelo = null;
					
					$ciudad = null;
					$re = null;
					$fabricante = null;
					$modelo = null;
					$encargado = null;
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					if ($fc->request->ar_plataforma == 9999) {
						// nueva plataforma
						$plataforma = new Plataforma();
						
						$plataforma->descripcion = $fc->request->otro_plataforma;
						
						$plataforma->insert($db);
						
						$fc->request->ar_plataforma = $plataforma->id;
						
						// refresco opciones plataforma post commit
						
						$parameters = array(
						);
						
						$ar = Plataforma::seek($db, $parameters, 'descripcion', 'ASC', 0, 10000);
						
						$this->addVar('plataformas', $ar);
						/*
						$data = "{'plataformas': [";
						
						$i = 0;
						foreach ($ar as $plat) {
							$data .= "{'id': '{$plat['id']}', 'descripcion': '{$plat['descripcion']}'}";
							$i++;
							if ($i < count($ar)) {
								$data .= ', ';
							}
						}
						
						$data .= "]}";
						
						HTTP_session::set('options_plataformas', $data);
						*/
					}
					
					
					if ($fc->request->ar_ciudad == 9999) {
						// nueva ciudad
						$ciudad = new Ciudad();
						
						$ciudad->id_region = $fc->request->ar_region;
						
						$ciudad->descripcion = $fc->request->otro_ciudad;
						
						$ciudad->insert($db);
						
						$fc->request->ar_ciudad = $ciudad->id;
						
						// refresco opciones ciudad post commit
						
						$parameters = array(
							'id_region' => "{$fc->request->ar_region}",
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
					$aux = $fc->request->ar_re;
					 
					if ($aux == 9999 || !isset($aux)) {
						// nueva R/E
						$re = new RadioEstacion();
						
						$re->id_ciudad = $fc->request->ar_ciudad;
						
						$re->descripcion = $fc->request->otro_re;
						
						$re->insert($db);
						
						$fc->request->ar_re = $re->id;
						
						$this->addVar('ar_re', $fc->request->ar_re);

						// refresco opciones R/E post commit
						
						$parameters = array(
							'id_ciudad' => "{$fc->request->ar_ciudad}",
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
					
					if ($fc->request->ar_fabricante == 9999) {
						// nuevo fabricante
						$fabricante = new Fabricante();
						
						$fabricante->descripcion = $fc->request->otro_fabricante;
						
						$fabricante->insert($db);
						
						$fc->request->ar_fabricante = $fabricante->id;

						$this->addVar('ar_fabricante', $fc->request->ar_fabricante);

						// refresco listado de fabricantes
						
						$ar = Fabricante::seek($db, '');
									
						$this->addVar('fabricantes', $ar);
												
					}
					
					// si se elige otro fabricante, el select de modelos se esconde y $fc->request->ar_modelo queda indefinido
					$aux = $fc->request->ar_modelo;
					 
					if ($aux == 9999 || !isset($aux)) {
						// nuevo modelo
						$modelo = new Modelo();
						
						$modelo->id_fabricante = $fc->request->ar_fabricante;
						
						$aux = $fc->request->otro_pid;
						
						if (!empty($aux)) {
							$modelo->pid = $aux;
						}
						
						$aux = $fc->request->otro_descripcion;
						
						if (!empty($aux)) {
							$modelo->descripcion = $aux;
						}

						$aux = $fc->request->otro_sap;
						
						if (!empty($aux)) {
							$modelo->sap = $aux;
						}

						$modelo->insert($db);
						
						$fc->request->ar_modelo = $modelo->id;

						// refresco opciones modelo post commit

						$parameters = array(
							'id_fabricante' => "{$fc->request->ar_fabricante}",
							'valido'		=> '',
						);
						
						$ar = Modelo::seek($db, $parameters, 'descripcion', 'ASC', null, null);
						
						$data = "{\"modelos\": [";
						
						$i = 0;
						foreach ($ar as $modelo) {
							$data .= "{\"id\": \"{$modelo['id']}\", \"descripcion\": \"{$modelo['descripcion']}\", \"pid\": \"{$modelo['pid']}\", \"sap\": \"{$modelo['sap']}\"}";
							$i++;
							if ($i < count($ar)) {
								$data .= ', ';
							}
						}
						
						$data .= "]}";
						
						HTTP_session::set('options_modelos', $data);
					}
					
					if ($fc->request->encargado == 9999) {
						// nuevo encargado
						$encargado = new Encargado();
						
						// pueden venir caracteres <, >, que vendran como entidades html
						$encargado->descripcion = html_entity_decode($fc->request->otro_encargado);
						
						$encargado->insert($db);
						
						$fc->request->encargado = $encargado->id;
						
						$this->addVar('encargado', $fc->request->encargado);
						
						// refresco listado de encargados mas abajo, despues de insertar el repuesto
						
					}
					
					$this->addVar('cantidad', $fc->request->cantidad);
					
					$repuesto = new Repuesto();
					
					$repuesto->id_plataforma = $fc->request->ar_plataforma;
					$repuesto->id_radio_estacion = $fc->request->ar_re;
					$repuesto->id_modelo = $fc->request->ar_modelo;
					$repuesto->id_encargado = $fc->request->encargado;
					$repuesto->serial = $fc->request->serial;
					$repuesto->nombre = $fc->request->nombre;
					//$repuesto->posicion = $fc->request->posicion;
					//$repuesto->ip = $fc->request->ip;
					//$repuesto->consola_activa = $fc->request->consola_activa;
					//$repuesto->consola_standby = $fc->request->consola_standby;
					//$repuesto->ubicacion = $fc->request->ubicacion;
					$repuesto->sla = $fc->request->sla;
					$repuesto->cantidad = $fc->request->cantidad;
					
					$repuesto->insert($db);
															
					if (!empty($encargado)) {
						// nuevo encargado

						// refresco listado de encargados; necesito el repuesto insertado para que funcione
						
						$parameters = array(
							'id_region' => $fc->request->ar_region,	
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
					
					$rm = new RepuestoMovimiento();
					
					$rm->id_repuesto = $repuesto->id;
					$rm->id_tipo_repuesto_movimiento = $trm->id;
					$rm->id_usuario = $this->usuario->id;
					$rm->cantidad = $fc->request->cantidad;
					$rm->fecha = 'now()';
					
					$rm->insert($db);
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
										
					// estatus exito
					$exito = true;
					
					$status_message = 'Repuesto agregado exitosamente';

					// recuerdo el id para validar si el usuario actualiza mas de una vez
					$this->addVar('id', $id_repuesto, null);
					
					// opciones en combo de ciudades
					$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
					
					// opciones en combo de radio estaciones
					$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
					
					// opciones en combo de modelos
					$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);
					
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
				
				$status_message = 'Repuesto no pudo ser agregadoado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			$this->addVar('ar_plataforma', $fc->request->ar_plataforma);
			$this->addVar('ar_region', $fc->request->ar_region);
			$this->addVar('ar_ciudad', $fc->request->ar_ciudad);
			$this->addVar('ar_re', $fc->request->ar_re);
			$this->addVar('ar_fabricante', $fc->request->ar_fabricante);
			$this->addVar('ar_modelo', $fc->request->ar_modelo);
			$this->addVar('encargado', $fc->request->encargado);
			
			$fv=array();
			
			$fv[0]="descripcion";
			$fv[1]="serial";
			$fv[2]="nombre";
			$fv[3]="sla";

			$this->initFormVars($fv);
			
		}
		
		$this->processSuccess();

	}
}
?>