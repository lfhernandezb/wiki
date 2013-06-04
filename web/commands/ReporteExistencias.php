<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Zona.php');

class ReporteExistencias extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		// recuerdo la clave de busqueda por si el usuario quisiera 'volver al listado'
		$this->addVar('search_keyword', HTTP_Session::get('search_keyword', ''));
		
		$m = "&nbsp;";
		
		$this->addVar("message", $m);        		
		
		$plat = $fc->request->plataforma;
		
		// lleno combo zonas
		$zonas = Zona::seek($db, array(), null, null, 0, 10000);
					
		$this->addVar('zonas', $zonas);
		
		// lleno combo plataformas
		$plataformas = Plataforma::seek($db, '');
					
		$this->addVar('plataformas', $plataformas);
			
		// lleno combo fabricantes
		$fabricantes = Fabricante::seek($db, '');
					
		$this->addVar('fabricantes', $fabricantes);
		
		// ayuda en pantalla
		$user_help_desk = 
			"Otorga una vista de las existencias de repuestos por zona y sus umbrales de criticidad.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/detail.png\" />&nbsp;Muestra la ubicaci&oacute;n de los repuestos de la zona.<br>" .
			"<img src=\"images/bell.png\" />&nbsp;Establece el umbral de criticidad y selecciona los destinos de las alarmas.<br>";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		$zona = $fc->request->zona;
				
		if (!isset($zona)) {
			// llamado desde home
			
			
			// muestro campos limpios
			$v = '';
			$this->addVar('zona', $v);
			$this->addVar('plataforma', $v);
			$this->addVar('fabricante', $v);
			$this->addVar('modelo', $v);
		}
		else {
			// submit
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "utiliza repuesto"
			
			HTTP_session::set('search_keyword_reporte_existencias', array(
				'id_zona'		=> $fc->request->zona,
				'id_plataforma'	=> $fc->request->plataforma,
				'id_fabricante'	=> $fc->request->fabricante,
				'id_modelo'		=> $fc->request->modelo,
			));
			
			try {
				$search_result = Repuesto::getStockByZone($db, $fc->request->zona, $fc->request->plataforma, $fc->request->fabricante, $fc->request->modelo);
				// var_dump($search_result);
				
				$this->addVar('search_result', $search_result);
				
				$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
				
				//echo '<br>row_number: ' . $row_number . '<br>';
				
				$this->addVar('row_number', $row_number);
							
				$fv=array();
				
				$fv[0]="zona";
				$fv[1]="plataforma";
				$fv[2]="fabricante";
				$fv[3]="modelo";
				
				$this->initFormVars($fv);
				
				$exito = true;
				
				$this->addVar("exito", $exito);
				
				// opciones en combo de modelos
				$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);
			
			} catch (Exception $e) {
				$fc->writeError($e->getMessage());
			}			
		}
		
		$this->processSuccess();

	}
}
?>