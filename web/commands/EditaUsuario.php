<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Acceso.php');

class EditaUsuario extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$id = $_GET['id'];
		$fid = $fc->request->id;
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_usuario', HTTP_Session::get('search_keyword_usuario', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = false;
		$usuarioPuedeUtilizar = false;
		
		$usuario = null;
		
		$user_help_desk = 
			"Edita los datos de un usuario.<br>Accesos:<br><br>" .
			"Utiliza Repuesto: Permite disponer de repuestos por falla o TP<br>" .
			"Agrega Repuesto: Permite ingresar repuestos en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (isset($id)) {
			// llamado desde home
			$usuario = Usuario::getByID($db, $id);
			// cargo en los controles los parametros del repuesto elegido
			$this->addVar('nombre_usuario', $usuario->nombre_usuario);
			$this->addVar('nombre', $usuario->nombre);
			$this->addVar('apellidos', $usuario->apellidos);
			$this->addVar('email', $usuario->email);
			$this->addVar('activo', $usuario->activo);
			
			// recuerdo el id para grabar cambios en caso de utilizar repuesto
			$this->addVar('id', $id, null);
			
		}
		else if (isset($fid)) {
			// submit, actualiza usuario... grabamos cambios
			
			$exito = null;
			
			// recuerdo el id para validar si el usuario actualiza mas de una vez
			$this->addVar('id', $fid, null);
			
			try {
			
				$acceso_utilizar = Acceso::getByDescripcion($db, 'utilizar');
				
				if (!isset($acceso_utilizar)) {
					throw new Exception('Error al obtener acceso de utilizar: registro no existe', null);
				}
				
				$acceso_agregar = Acceso::getByDescripcion($db, 'agregar');
				
				if (!isset($acceso_agregar)) {
					throw new Exception('Error al obtener acceso de agregar: registro no existe', null);
				}
				
				$bInTransaction = false;
				
				$usuario = Usuario::getById($db, $fid);
				
				$usuario->nombre_usuario = $fc->request->nombre_usuario;
				$usuario->nombre = $fc->request->nombre;
				$usuario->apellidos = $fc->request->apellidos;
				$usuario->email = $fc->request->email;
				//$usuario->ubicacion = $fc->request->ubicacion);
				//$usuario->comentario = $fc->request->comentario);
				
				$ar_accesos = $fc->request->accesos;
				
				$reset_password = $fc->request->reset_password;
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$usuario->update($db);
					
					$usuarioPuedeAgregar = null;
					
					$usuarioPuedeUtilizar = null;
					
					
					if (is_array($ar_accesos)) {
						if (array_key_exists(0, $ar_accesos)) {
							// solicitado acceso a utilizar
							$usuario->otorgaAcceso($db, 'utilizar');
							//$usuarioPuedeUtilizar = true;
						}
						if (array_key_exists(1, $ar_accesos)) {
							// solicitado acceso a agregar
							$usuario->otorgaAcceso($db, 'agregar');
							//$usuarioPuedeAgregar = true;
						}
					}
					else {
						// solicitado quitar los accesos
						$usuario->revocaAcceso($db, 'agregar');
						//$usuarioPuedeAgregar = false;
						$usuario->revocaAcceso($db, 'utilizar');
						//$usuarioPuedeUtilizar = false;
					}
										
					// para que el estado establecido pueda verse post submit
					// $this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
								
					//$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
					
					$reset_pwd = false;
					
					if (isset($reset_password)) {
						$reset_pwd = true;
					}
					
					$this->addVar('reset_pwd', $reset_pwd);

					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Usuario modificado exitosamente';
					
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
				
				$status_message = 'Usuario no pudo ser modificado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			// cargo en los textboxes los mismos valores pre submit
			$fv=array();
			
			$fv[0]="nombre_usuario";
			$fv[1]="nombre";
			$fv[2]="apellidos";
			$fv[3]="email";
			$fv[4]="accesos";
			$fv[5]="reset_password";
			
			$this->initFormVars($fv);
		}
		
		if ($usuario->tieneAcceso($db, 'agregar')) {
			$usuarioPuedeAgregar = true;
		}

		$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
		
	
		
		if ($usuario->tieneAcceso($db, 'utilizar')) {
			$usuarioPuedeUtilizar = true;
		}
		
		$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
		
		$this->processSuccess();

	}
}
?>