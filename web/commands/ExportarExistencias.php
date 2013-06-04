<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');

class ExportarExistencias extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		if (!empty($_POST)) {
			// submit
			
			$exito = null;
			
			$search_result = Repuesto::getStockByZone2($db, $fc->request->zona, $fc->request->plataforma, $fc->request->fabricante, $fc->request->modelo);
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			//echo '<br>row_number: ' . $row_number . '<br>';
			
			$this->addVar('row_number', $row_number);
						
			$exito = true;
			
			$this->addVar('exito', $exito);
			
		}
		
		header('Content-type: application/excel');
		
		header('Content-Disposition: attachment; filename="export.xls"');

		$this->processSuccess();
	}
}
?>