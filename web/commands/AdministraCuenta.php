<?php

include_once('GenericCommand.php');
include_once('../classes/Usuario.php');

class AdministraCuenta extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Permite cambiar la contrase&ntilde;a de ingreso al sistema. La nueva contrase&ntilde;a debe tener al menos 6 caracteres. La contrase&ntilde;a actual tambi&eacute;n debe ingresarse";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (empty($_POST)) {
			// recuerdo id_usuario
			$this->addVar('id_usuario', $this->usuario->id);
			
		}
		else {
			// form submiteado
			$exito = null;
			
			$message = null;
			
			$password = $fc->request->password;
			
			$new_password = $fc->request->new_password;
			
			if ($this->usuario->contrasena == md5($password)) {
				$exito = true;
				
				$this->usuario->contrasena = md5($new_password);
				
				$this->usuario->update($db);
				
				$this->addVar('password', $password);
				
				$this->addVar('new_password', $new_password);
				
				$message = 'Contrase&ntilde;a modificada exitosamente.';
			}
			else {
				$exito = false;
				
				$message = 'Contrase&ntilde;a incorrecta';
			}
			
			$this->addVar("exito", $exito);
			
			$this->addVar("message", $message);
			
		}
		
		$this->addVar('id_usuario', $this->usuario->id);
		
		$this->addVar('username', $this->usuario->nombre_usuario);
		
		$this->processSuccess();

	}
}
?>