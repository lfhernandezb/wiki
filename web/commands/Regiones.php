<?php

include_once('GenericCommand.php');
include_once('../classes/Region.php');
include_once('../classes/Zona.php');

class Regiones extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		// ayuda en pantalla
		$user_help_desk = 
			"Permite buscar y editar regiones.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/user_edit.png\" />&nbsp;Edita regi&oacute;n.<br>";
			
		$this->addVar('user_help_desk', $user_help_desk);
		
		$search_keyword = $fc->request->search_keyword_region;
		
		// aca hay auto submit
		
		$parameters = null;
		
		if (isset($search_keyword)) {
			$parameters = array(
				'compuesto' => $search_keyword,
			);
		}
		else {
			$parameters = array(
				'compuesto' => '',
			);
			
			$search_keyword = '';
		}
			
		$exito = null;
		
		// guardo el valor de busqueda para utilizarlo en "volver al listado" en "edita usuario"
		
		HTTP_session::set('search_keyword_region', $search_keyword);
		
		$search_result = Region::seek($db, $parameters, 'orden', 'ASC', 0, 10000);
		
		// var_dump($search_result);
		
		$this->addVar('search_result', $search_result);
		
		$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
		
		$this->addVar('row_number', $row_number);
		
		$exito = true;
		
		$this->addVar('exito', $exito);
		
		$fv=array();
		
		$fv[0]="search_keyword_region";

		$this->initFormVars($fv);

		$this->processSuccess();
	}
}
?>