<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Acceso.php');

class AgregaUsuario extends GenericCommand {
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword_usuario', HTTP_Session::get('search_keyword_usuario', ''));
		
		$m = "&nbsp;";

		$this->addVar("message", $m);

		$usuarioPuedeAgregar = null;
		$usuarioPuedeUtilizar = null;
		
		$usuario = null;
		
		// ayuda en pantalla
		$user_help_desk = 
			"Crea un usuario de la aplicaci&oacute;n. Los campos marcados con * son obligatorios.<br>Accesos:<br><br>" .
			"Utiliza Repuesto: Permite disponer de repuestos por falla o TP<br>" .
			"Agrega Repuesto: Permite ingresar repuestos en forma manual o masiva";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (!isset($fc->request->nombre_usuario)) {
			// llamado desde 'GestionaUsuarios'
			
			// muestro controles limpios
			$v = '';

			$this->addVar('nombre_usuario', $v);
			$this->addVar('nombre', $v);
			$this->addVar('apellidos', $v);
			$this->addVar('email', $v);
			//$this->addVar('activo', $v);

			$usuarioPuedeAgregar = false;
			$usuarioPuedeUtilizar = false;
			
		}
		else {
			// submit, agrega usuario... grabamos cambios
			
			$exito = null;
			
			$usuarioPuedeAgregar = false;
			$usuarioPuedeUtilizar = false;
			
			$ar_accesos = $fc->request->accesos;
			
			if (is_array($ar_accesos)) {
				if (array_key_exists(0, $ar_accesos)) {
					// solicitado acceso a utilizar
					
					$usuarioPuedeUtilizar = true;
				}
				if (array_key_exists(1, $ar_accesos)) {
					// solicitado acceso a agregar
					
					$usuarioPuedeAgregar = true;
				}
			}
			
			
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
				
				$usuario = new Usuario();
				
				$usuario->nombre_usuario = $fc->request->nombre_usuario;
				$usuario->nombre = $fc->request->nombre;
				$usuario->apellidos = $fc->request->apellidos;
				$usuario->email = $fc->request->email;
				$usuario->activo = 0;
				//$usuario->comentario = $fc->request->comentario;
				
				// contrasena aleatoria de 4 digitos
				
				$password = mt_rand(0, 9999);
				
				$usuario->contrasena = md5($password);
				
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$usuario->insert($db);
										
					if ($usuarioPuedeAgregar) {
						$usuario->otorgaAcceso($db, 'agregar');
					}

					if ($usuarioPuedeUtilizar) {
						$usuario->otorgaAcceso($db, 'utilizar');
					}
					
					// commit
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$headers = null;
					
					// correo de notificacion
					$to = $usuario->email;
					
					$ar_aux = split('\?', $_SERVER["HTTP_REFERER"]);											
					
					if ($fc->appSettings['html_email'] == 'yes') {
						// To send HTML mail, the Content-type header must be set
						$headers = '';
						
						$headers .= 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						
						// Additional headers
						$headers .= "To: {$usuario->nombre} {$usuario->apellidos} <{$usuario->email}>\r\n";
						$headers .= 'From: SiGREP <sigrep>' . "\r\n";
						// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
						$headers .= 'Bcc: lfhernandez@dsoft.cl, lfhernandezb@gmail.com' . "\r\n";					
						
						$content_template = htmlentities(file_get_contents('../templates/correo_creacion_usuario.html'));
						
						//echo "<br>content_template $content_template<br>";
						
						//echo "<br>usuario->nombre $usuario->nombre<br>";
						
						//echo "<br>usuario->id $usuario->id<br>";
						
						//echo "<br>usuario->nombre_usuario $usuario->nombre_usuario<br>";
						
						//echo "<br>usuario->get(nombre_usuario) " . $usuario->nombre_usuario . "<br>";
						
						//echo "<br>password $password<br>";
						
						$mail_body = html_entity_decode(sprintf($content_template, 
							$usuario->nombre, 
							$ar_aux[0] . "?do=ActivaCuenta&idUsuario={$usuario->id}", 
							$usuario->nombre_usuario, 
							$password));
					}
					else {
						// correo en texto plano
						// $mail_body = "Estimado {$usuario->nombre},\r\nLe ha sido creada una cuenta en el sistema SiGREP.\r\nFavor ingrese al sistema en la URL {$ar_aux[0]} con la cuenta {$usuario->nombre_usuario} y contrasena $password\r\n\r\nSaludos,\r\nEl Administrador";
						$mail_body = sprintf("Estimado %s,\r\nLe ha sido creada una cuenta en el sistema SiGREP.\r\nFavor ingrese al sistema en la URL %s con la cuenta %s y contrasena %s\r\n\r\nSaludos,\r\nEl Administrador", 
							$usuario->nombre, 
							$ar_aux[0] . "?do=ActivaCuenta&idUsuario={$usuario->id}", 
							$usuario->nombre_usuario, 
							$password);
					}
					
					
					//echo "<br>$mail_body<br>";
	
					mail($to, "SiGREP, Creacion Cuenta", $mail_body, $headers);
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Usuario agregado exitosamente';
					
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
				
				$status_message = 'Usuario no pudo ser agregado. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);

			// para que el estado establecido pueda verse post submit
			$this->addVar('usuarioPuedeAgregar', $usuarioPuedeAgregar);
						
			$this->addVar('usuarioPuedeUtilizar', $usuarioPuedeUtilizar);
			
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
				
		$this->processSuccess();

	}
}
?>