<?php

include_once('GenericCommand.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Ciudad.php');
include_once('../classes/RadioEstacion.php');
include_once('../classes/Modelo.php');
include_once('../classes/Usuario.php');
include_once('../classes/Ciudad.php');
include_once('../classes/RadioEstacion.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Modelo.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Motivo.php');
include_once('../classes/Encargado.php');
include_once('../classes/MotivoMovimiento.php');
include_once('../classes/Region.php');
include_once('../classes/Zona.php');
include_once('../classes/ZonaRegion.php');


/***********************************************************************************************************
 * 
 * Ejecuta comandos ajax
 * 
 ***********************************************************************************************************/

class Ajax extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$req = $fc->request->req;
		
		if (isset($req)) {
			if ($req == 'getCiudades') {
				// se solicitan las ciudades pertenecientes a una determinada region; desde AgregaRepuesto
				$id_region = $fc->request->id_region;
				
				$parameters = array(
					'id_region' => "$id_region",
				);
				
				$ar = Ciudad::seek($db, $parameters, 'descripcion', 'ASC', null, null);
				
				$data = "{\"ciudades\": [";
				
				$i = 0;
				foreach ($ar as $ciudad) {
					$data .= "{\"id\": \"{$ciudad['id']}\", \"descripcion\": \"" . htmlentities($ciudad['descripcion']) . "\"}";
					$i++;
					if ($i < count($ar)) {
						$data .= ', ';
					}
				}
				
				$data .= "]}";
				
				HTTP_session::set('options_ciudades', $data);
				
				echo $data;
			}
			else if ($req == 'getRadioEstaciones') {
				// se solicitan las R/E pertenecientes a una determinada ciudad; desde AgregaRepuesto
				$id_ciudad = $fc->request->id_ciudad;
				
				$parameters = array(
					'id_ciudad' => "$id_ciudad",
				);
				
				$ar = RadioEstacion::seek($db, $parameters, 'descripcion', 'ASC', null, null);
				
				$data = "{\"res\": [";
				
				$i = 0;
				foreach ($ar as $re) {
					$data .= "{\"id\": \"{$re['id']}\", \"descripcion\": \"" . htmlentities($re['descripcion']) . "\"}";
					$i++;
					if ($i < count($ar)) {
						$data .= ', ';
					}
				}
				
				$data .= "]}";
				
				HTTP_session::set('options_res', $data);
				
				echo $data;
			}
			else if ($req == 'getModelos') {
				// se solicitan los modelos pertenecientes a un determinado fabricante; desde AgregaRepuesto
				$id_fabricante = $fc->request->id_fabricante;
				
				$parameters = array(
					'id_fabricante' => "$id_fabricante",
					'valido'		=> '',
				);
				
				$ar = Modelo::seek($db, $parameters, 'm.pid, m.descripcion', 'ASC', null, null);
				
				$data = "{\"modelos\": [";
				
				$i = 0;
				foreach ($ar as $modelo) {
					$data .= "{\"id\": \"{$modelo['id']}\", \"descripcion\": \"" . htmlentities($modelo['descripcion']). "\", \"pid\": \"" . htmlentities($modelo['pid']) . "\"}";
					$i++;
					if ($i < count($ar)) {
						$data .= ', ';
					}
				}
				
				$data .= "]}";
				
				HTTP_session::set('options_modelos', $data);
				
				echo $data;
			}
			else if ($req == 'getEncargados') {
				// se solicitan los encargados pertenecientes a una determinada region; desde AgregaRepuesto
				$id_region = $fc->request->id_region;
				
				$parameters = array(
					'id_region' => "$id_region",
				);
				
				$ar = Encargado::seek($db, $parameters, 'e.descripcion', 'ASC', null, null);
				
				$data = "{\"encargados\": [";
				
				$i = 0;
				foreach ($ar as $encargado) {
					$data .= "{\"id\": \"{$encargado['id']}\", \"descripcion\": \"" . htmlentities($encargado['descripcion']). "\"}";
					$i++;
					if ($i < count($ar)) {
						$data .= ', ';
					}
				}
				
				$data .= "]}";
				
				HTTP_session::set('options_encargados', $data);
				
				echo $data;
			}
			else if ($req == 'getMovimiento') {
				// se solicita el detalle de un movimiento
				$id_repuesto_movimiento = $fc->request->id_repuesto_movimiento;
				
				$rm = RepuestoMovimiento::getById($db, $id_repuesto_movimiento);
				
				$repuesto = Repuesto::getById($db, $rm->id_repuesto);
				
				$tipo = TipoRepuestoMovimiento::getByID($db, $rm->id_tipo_repuesto_movimiento);
				
				if ($tipo->descripcion == 'egreso') {
					$motivo_movimiento = MotivoMovimiento::getByIdRM($db, $rm->id);
					
					$motivo = Motivo::getByID($db, $motivo_movimiento->id_motivo);
				}
				
				$usuario = Usuario::getByID($db, $rm->id_usuario);
				
				$data =
					"{" .
					"	\"tipo_repuesto_movimiento\": {" .
					"		\"descripcion\": \"" . htmlentities($tipo->descripcion) . "\"" .
					"	}," . 
					"	\"repuesto_movimiento\": {" .
					"		\"cantidad\": \"" . $rm->cantidad . "\"," .
					"		\"fecha\": \"" . $rm->fecha . "\"" .
					"	},";
				
				if ($tipo->descripcion == 'egreso') {
					$data .=
					"	\"motivo_movimiento\": {" .
					"		\"motivo\": \"" . $motivo->descripcion . "\"," .
					"		\"valor\": \"" . $motivo_movimiento->valor . "\"" .
					"	},"; 
				}
				
				$data .=
					"	\"usuario\": {" .
					"		\"apellidos\": \"" . htmlentities($usuario->apellidos) . "\"," .
					"		\"nombre\": \"" . htmlentities($usuario->nombre) . "\"" .
					"	}," . 
					"	\"repuesto\": {" .
					"        \"plataforma\": \"" . $repuesto->plataforma . "\"," .
					"        \"region\": \"" . htmlentities($repuesto->region) . "\"," .
					"        \"ciudad\": \"" . htmlentities($repuesto->ciudad) . "\"," .
					"        \"radio_estacion\": \"" . htmlentities($repuesto->radio_estacion) . "\"," .
					"        \"fabricante\": \"" . $repuesto->fabricante . "\"," .
					"        \"modelo\": \"" . htmlentities($repuesto->modelo) . "\"," .
					"        \"sap\": \"" . $repuesto->sap . "\"," .
					"        \"serial\": \"" . $repuesto->serial . "\"," .
					"        \"nombre\": \"" . htmlentities($repuesto->nombre) . "\"," .
					"        \"posicion\": \"" . htmlentities($repuesto->posicion) . "\"," .
					"        \"ip\": \"" . $repuesto->ip . "\"," .
					"        \"consola_activa\": \"" . $repuesto->consola_activa . "\"," .
					"        \"consola_standby\": \"" . $repuesto->consola_standby . "\"," .
					"        \"ubicacion\": \"" . htmlentities($repuesto->ubicacion) . "\"," .
					"        \"sla\": \"" . htmlentities($repuesto->sla) . "\"";
				
				if ($tipo->descripcion == 'egreso') {
					$data .=
					"," .
					"        \"posicion\": \"" . htmlentities($repuesto->posicion) . "\"," .
					"        \"ip\": \"" . $repuesto->ip . "\"," .
					"        \"consola_activa\": \"" . $repuesto->consola_activa . "\"," .
					"        \"consola_standby\": \"" . $repuesto->consola_standby . "\"," .
					"        \"ubicacion\": \"" . htmlentities($repuesto->ubicacion) . "\"";
				}
				
				$data .=
					"    }" .
					"}";
				
				echo $data;
			}
			else if ($req == 'getRepuesto') {
				// se solicita el detalle de un repuesto
				$id_repuesto = $fc->request->id_repuesto;
				
				$repuesto = Repuesto::getById($db, $id_repuesto);
				
				$data =
					"{" .
					"	\"repuesto\": {" .
					"        \"plataforma\": \"" . $repuesto->plataforma . "\"," .
					"        \"region\": \"" . htmlentities($repuesto->region) . "\"," .
					"        \"ciudad\": \"" . htmlentities($repuesto->ciudad) . "\"," .
					"        \"radio_estacion\": \"" . htmlentities($repuesto->radio_estacion) . "\"," .
					"        \"fabricante\": \"" . $repuesto->fabricante . "\"," .
					"        \"modelo\": \"" . htmlentities($repuesto->modelo) . "\"," .
					"        \"sap\": \"" . $repuesto->sap . "\"," .
					"        \"serial\": \"" . $repuesto->serial . "\"," .
					"        \"nombre\": \"" . htmlentities($repuesto->nombre) . "\"," .
					"        \"posicion\": \"" . htmlentities($repuesto->posicion) . "\"," .
					"        \"ip\": \"" . $repuesto->ip . "\"," .
					"        \"consola_activa\": \"" . $repuesto->consola_activa . "\"," .
					"        \"consola_standby\": \"" . $repuesto->consola_standby . "\"," .
					"        \"ubicacion\": \"" . htmlentities($repuesto->ubicacion) . "\"," .
					"        \"sla\": \"" . htmlentities($repuesto->sla) . "\"," .
					"        \"encargado\": \"" . htmlentities($repuesto->encargado) . "\"," .
					"        \"cantidad\": \"" . htmlentities($repuesto->cantidad) . "\"" .
					"    }" .
					"}";
				
				echo $data;
			}
			else if ($req == 'getRegion') {
				// se solicita el detalle de una region
				$id_region = $fc->request->id;
				
				$region = Region::getById($db, $id_region);
				
				$data =
					"{" .
					"	\"region\": {" .
					"        \"descripcion\": \"" . htmlentities($region->descripcion) . "\"," .
					"        \"nombre\": \"" . htmlentities($region->nombre) . "\"" .
					"    }" .
					"}";
				
				echo $data;
			}
			else if ($req == 'delRepuesto') {
				// se solicita eliminar un repuesto (borrado en 1)
				$id_repuesto = $fc->request->id_repuesto;
				
				$repuesto = Repuesto::getById($db, $id_repuesto);
				
				$resp = '0';
				
				if (!empty($repuesto)) {
					
					$repuesto->borrado = 1;
					
					$repuesto->update($db);
					
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
								
			}
			else if ($req == 'delUsuario') {
				// se solicita eliminar un usuario (borrado en 1)
				$id_usuario = $fc->request->id_usuario;
				
				$usuario = Usuario::getByID($db, $id_usuario);
				
				$resp = '0';
				
				if (!empty($usuario)) {
					
					$usuario->borrado = 1;
					
					$usuario->update($db);
					
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
								
			}
			else if ($req == 'delZona') {
				// se solicita eliminar una zona
				$id_zona = $fc->request->id;
				
				$resp = '0';
				
				try {
					
					try {
	
						if (!$db->TransactionBegin()) {
							throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
						}
						
						ZonaRegion::delete($db, $id_zona);
						
						$zona = Zona::getByID($db, $id_zona);
						
						$zona->delete($db);
						
						// commit
						
						if (!$db->TransactionEnd()) {
							throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
						}
						
						$resp = '1';
						
					} catch (Exception $e) {
						// rollback
						if ($bInTransaction) {
							$db->TransactionRollback();
						}
						
						throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
					}
				
				} catch (Exception $e) {
				
				}
				
				echo "{\"respuesta\": \"$resp\"}";
								
			}
			else if ($req == 'resetPassword') {
				// se solicita eliminar un usuario (borrado en 1)
				$id_usuario = $fc->request->id_usuario;
				
				$usuario = Usuario::getByID($db, $id_usuario);
				
				$resp = '0';
				
				if (!empty($usuario)) {
					
					$usuario->activo = 0;
					
					$password = mt_rand(0, 9999);
					
					$usuario->contrasena = md5($password);					
					
					$usuario->update($db);
					
					$headers = null;
					
					$ar_aux = split('\?', $_SERVER["HTTP_REFERER"]);
					
					if ($fc->appSettings['html_email'] == 'yes') {
						// correo html
						// correo de notificacion
						// To send HTML mail, the Content-type header must be set
						$headers = '';
						
						$headers .= 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						
						// Additional headers
						$headers .= "To: \"{$usuario->nombre} {$usuario->apellidos}\" <{$usuario->email}>\r\n";
						$headers .= "From: SiGREP <sigrep>\r\n";
						// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
						$headers .= "Bcc: <lfhernandez@dsoft.cl>\r\n";					
						
						$content_template = htmlentities(file_get_contents('../templates/correo_reseteo_usuario.html'));
						
						$mail_body = html_entity_decode(sprintf($content_template, 
							$usuario->nombre, 
							$ar_aux[0] . "?do=ActivaCuenta&idUsuario={$usuario->id}", 
							$usuario->nombre_usuario, 
							$password));
						
						//echo "<br>$mail_body<br>";
					}
					else {
						// correo en texto plano
						$mail_body = sprintf("Estimado %s,\r\nLa contrasena de su cuenta ha sido reseteada.\r\nFavor ingrese al sistema en la URL %s con la cuenta %s y contrasena %s\r\n\r\nSaludos,\r\nEl Administrador", 
							$usuario->nombre, 
							$ar_aux[0] . "?do=ActivaCuenta&idUsuario={$usuario->id}", 
							$usuario->nombre_usuario, 
							$password);
					}
	
					mail($usuario->email, "SiGREP, Contrasena restablecida", $mail_body, $headers);
					
					
					
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
								
			}
			else if ($req == 'UsernameExistente') {
				// se solicita validar la existencia de algun usuario con el username indicado
				
				$usuario = Usuario::getByUsername($db, $fc->request->username);
				
				$resp = 0;
				
				if (isset($usuario)) {
					$resp = 1;					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'EmailExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$usuario = Usuario::getByEmail($db, $fc->request->email);
				
				$resp = '0';
				
				if (isset($usuario)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'ciudadExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$ciudad = Ciudad::getByDescripcion($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($ciudad)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'reExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$re = RadioEstacion::getByDescripcion($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'fabricanteExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$re = Fabricante::getByDescripcion($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'modeloExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$re = Modelo::getByDescripcion($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'sapExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$re = Modelo::getBySAP($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'descripcionExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$re = Modelo::getByDescripcion($db, $fc->request->param);
				
				$resp = '0';
				
				if (isset($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'serialExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$parameters = array(
					'id_modelo' => $fc->request->idModelo,
					'serial' => $fc->request->serial,
					'no borrado' => '',
				);
				
				$re = Repuesto::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'encargadoExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$parameters = array(
					'descripcion' => $fc->request->param,
				);
				
				$re = Encargado::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'plataformaExistente') {
				// se solicita validar la existencia de algun usuario con el email indicado
				
				$parameters = array(
					'descripcion' => $fc->request->param,
				);
				
				$re = Plataforma::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($re)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'regionDescripcionExistente') {
				// se solicita validar la existencia de una region con la descripcion indicada e id distindo al enviado
				
				$parameters = array(
					'descripcion' => "'{$fc->request->descripcion}'",
					'id distinto' => $fc->request->id,
				);
				
				$rg = Region::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($rg)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'regionNombreExistente') {
				// se solicita validar la existencia de una region con el nombre indicado e id distindo al enviado
				
				$parameters = array(
					'nombre' => "'{$fc->request->nombre}'",
					'id distinto' => $fc->request->id,
				);
				
				$rg = Region::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($rg)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'zonaExistente') {
				// se solicita validar la existencia de una region con el nombre indicado e id distindo al enviado
				
				$parameters = array(
					'descripcion' => "'{$fc->request->descripcion}'",
				);
				
				$z = Zona::seek($db, $parameters, null, null, 0, 10000);
				
				$resp = '0';
				
				if (!empty($z)) {
					$resp = '1';					
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
			else if ($req == 'modificaRegion') {
				// se solicita modificar region
				
				$resp = null;
				
				$rg = new Region();
				
				$rg->id = $fc->request->id;
				$rg->descripcion = $fc->request->descripcion;
				$rg->nombre = $fc->request->nombre;
				
				try {
					$rg->update($db);

					$resp = '0';
				} catch (Exception $e) {
					$resp = '1';
				}
				
				echo "{\"respuesta\": \"$resp\"}";
			}
		}
	}
}
?>