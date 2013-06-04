<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');

class ExportarHome extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();


		$search_keyword = HTTP_session::get('search_keyword', null);
			
		$search_keyword_2 = HTTP_session::get('search_keyword_2', null);

		if (!empty($search_keyword)) {
			// submit
			
			$exito = null;
			
			$search_result = Repuesto::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
		}
		else if (!empty($search_keyword_2)) {
			// submit
			
			$exito = null;
			
			$parameters = array();
			
			$id_plataforma = $search_keyword_2['id_plataforma'];
			
			if (!empty($id_plataforma)) {
				$parameters['id_plataforma'] = $id_plataforma;
			}
			
			$id_region = $search_keyword_2['id_region'];
			
			if (!empty($id_region)) {
				$parameters['id_region'] = $id_region;
			}
			
			$id_ciudad = $search_keyword_2['id_ciudad'];
			
			if ($id_ciudad != '') {
				$parameters['id_ciudad'] = $id_ciudad;
			}
			
			$id_radio_estacion = $search_keyword_2['id_radio_estacion'];
			
			if ($id_radio_estacion != '') {
				$parameters['id_radio_estacion'] = $id_radio_estacion;
			}
			
			$id_fabricante = $search_keyword_2['id_fabricante'];
			
			if ($id_fabricante != '') {
				$parameters['id_fabricante'] = $id_fabricante;
			}
			
			$id_modelo = $search_keyword_2['id_modelo'];
			
			if ($id_modelo != '') {
				$parameters['id_modelo'] = $id_modelo;
			}
			
			$parameters['no borrado'] = null;
			
			$search_result = Repuesto::seek($db, $parameters, 'p.id_plataforma, rg.id_region', 'ASC', 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
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