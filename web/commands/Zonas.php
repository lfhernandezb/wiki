<?php

include_once('GenericCommand.php');
include_once('../classes/Zona.php');
include_once('../classes/Region.php');

class Zonas extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Busca, edita y agrega zonas.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/user_edit.png\" />&nbsp;Edita zona.<br>" .
			"<img src=\"images/trash.png\" />&nbsp;Elimina zona.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		$search_keyword = $fc->request->search_keyword_zona;
		
		$parameters = null;
		
		if (isset($search_keyword)) {
			$parameters = array(
				'descripcion similar' => $search_keyword,
			);
		}
		else {
			$parameters = array(
				'descripcion similar' => '',
			);
			
			$search_keyword = '';
		}
			
		$exito = null;
		
		// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita usuario"
		
		HTTP_session::set('search_keyword_zona', $search_keyword);
		
		$search_result = Zona::seek($db, $parameters, null, null, 0, 10000);
		
		// var_dump($search_result);
		
		$this->addVar('search_result', $search_result);
		
		$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
		
		$this->addVar('row_number', $row_number);
		
		$exito = true;
		
		$this->addVar('exito', $exito);
		
		
		$fv=array();
		
		$fv[0]="search_keyword_zona";

		$this->initFormVars($fv);

		$this->processSuccess();
	}
}
?>