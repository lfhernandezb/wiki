<?php

include_once('GenericCommand.php');
include_once('../classes/Usuario.php');

class ActivaCuenta extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$usuario = null;
		
		if (empty($_POST)) {
			// recuerdo id_usuario
			$id_usuario = $fc->request->idUsuario;
			
			$this->addVar('id_usuario', $id_usuario);
			
			$usuario = Usuario::getByID($db, $id_usuario);
			
			// si la cuenta esta activa, redirijo a Login
			if ($usuario->activo == '1') {
				$fc->response->redirect($fc->receivers['main']."?do={$fc->defaultCommand}");
			}
		}
		else {
			// form submiteado
			$exito = null;
			
			$message = null;
			
			$id_usuario = $fc->request->id_usuario;
			
			$usuario = Usuario::getByID($db, $id_usuario);
			
			// si la cuenta esta activa, redirijo a Login
			if ($usuario->activo == '1') {
				$fc->response->redirect($fc->receivers['main']."?do={$fc->defaultCommand}");
			}
			else {
				$password = $fc->request->password;
				
				$new_password = $fc->request->new_password;
				
				if ($usuario->contrasena == md5($password)) {
					$exito = true;
					
					$usuario->contrasena = md5($new_password);
					
					$usuario->activo = '1';
					
					$usuario->update($db);
					
					$this->addVar('password', $password);
					
					$this->addVar('new_password', $new_password);
					
					$message = 'Cuenta activada exitosamente. Presione el bot&oacute;n Ingresar para entrar';
				}
				else {
					$exito = false;
					
					$message = 'Contrase&ntilde;a incorrecta';
				}
				
				$this->addVar("exito", $exito);
				
				$this->addVar("message", $message);
				
				$this->addVar('id_usuario', $id_usuario);
			}	
		}
		
		$this->addVar('username', $usuario->nombre_usuario);
		
		$this->processSuccess();

	}
}
?>