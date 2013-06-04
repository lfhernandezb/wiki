<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Modelo.php');
include_once('../classes/Zona.php');

class DetalleExistencias extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		$id_zona = $fc->request->id_zona;
		
		$zona = Zona::getByID($db, $id_zona);
		
		$this->addVar('zona', $zona->descripcion);
		
		$id_plataforma = $fc->request->id_plataforma;
		
		$plataforma = Plataforma::getByID($db, $id_plataforma);
		
		$this->addVar('plataforma', $plataforma->descripcion);
				
		$id_fabricante = $fc->request->id_fabricante;
		
		$fabricante = Fabricante::getByID($db, $id_fabricante);
		
		$this->addVar('fabricante', $fabricante->descripcion);
		
		$id_modelo = $fc->request->id_modelo;
		
		$modelo = Modelo::getByID($db, $id_modelo);
		
		$m = $modelo->pid . '  ' . $modelo->descripcion;
		$this->addVar('modelo', $m);
		
		$this->addVar('sap', $modelo->sap);
		
		$stock = $fc->request->stock;
		
		$this->addVar('stock', $stock);
		
		$umbral = $fc->request->umbral;
		
		$this->addVar('umbral', $umbral);
		
		// ayuda en pantalla
		$user_help_desk = 
			"Muestra la ubicaci&oacute;n de los repuestos de la zona.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		$ar = HTTP_session::get('search_keyword_reporte_existencias', null);
		
		if (isset($ar)) {
			if (is_array($ar)) {
				$this->addVar('id_zona', $ar['id_zona']);
				$this->addVar('id_plataforma', $ar['id_plataforma']);
				$this->addVar('id_fabricante', $ar['id_fabricante']);
				$this->addVar('id_modelo', $ar['id_modelo']);
			}
		}
				
		$exito = null;
		
		try {
			$search_result = Repuesto::getStockByZoneDetail($db, $id_zona, $id_plataforma, $id_fabricante, $id_modelo);
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			//echo '<br>row_number: ' . $row_number . '<br>';
			
			$this->addVar('row_number', $row_number);
						
			$exito = true;
			
			$this->addVar("exito", $exito);
			
		
		} catch (Exception $e) {
			$fc->writeError($e->getMessage());
		}			
		
		$this->processSuccess();

	}
}
?>