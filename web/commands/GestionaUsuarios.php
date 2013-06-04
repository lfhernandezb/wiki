<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Usuario.php');

class GestionaUsuarios extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Busca usuarios por nombre, apellido o nombre de usuario.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/user_edit.png\" />&nbsp;Edita usuario.<br>" .
			"<img src=\"images/reset_password.png\" />&nbsp;Resetea contrase&ntilde;a.<br>" .
			"<img src=\"images/trash.png\" />&nbsp;Elimina usuario.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		
		if (isset($fc->request->search_keyword_usuario)) {
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita usuario"
			
			HTTP_session::set('search_keyword_usuario', $fc->request->search_keyword_usuario);
			
			$search_keyword = $fc->request->search_keyword_usuario;
			
			$search_result = Usuario::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
		}
		
		$fv=array();
		
		$fv[0]="search_keyword_usuario";

		$this->initFormVars($fv);

		$this->processSuccess();
	}
}
?>