<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');

class Services extends GenericCommand {
	function execute(){
		global $fc;

		$this->processSuccess();
	}
}
?>