<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');

class DisplayHome extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		/* ya en GenerricCommand
		$puedeAgregar = false;
		
		$usuario = HTTP_Session::get('usuario', null);
		
		if ($usuario->tieneAcceso($db, 'agregar')) {
			$puedeAgregar = true;
		}

		$this->addVar('puedeAgregar', $puedeAgregar);
		*/
		$puedeUtilizar = false;
		
		if ($this->usuario->tieneAcceso($db, 'utilizar')) {
			$puedeUtilizar = true;
		}
		
		$this->addVar('puedeUtilizar', $puedeUtilizar);
		
		$puedeMover = false;
		
		if ($this->usuario->tieneAcceso($db, 'mover')) {
			$puedeMover = true;
		}
		
		$this->addVar('puedeMover', $puedeMover);
		
		$puedeEliminar = false;
		
		if ($this->usuario->tieneAcceso($db, 'eliminar')) {
			$puedeEliminar = true;
		}
		
		$this->addVar('puedeEliminar', $puedeEliminar);
		
		// lleno combo plataformas
		$plataformas = Plataforma::seek($db, array(), 'descripcion', 'ASC', 0, 10000);
					
		$this->addVar('plataformas', $plataformas);
			
		// lleno combo regiones
		$regiones = Region::seek($db, array(), 'orden', 'ASC', 0, 10000);
					
		$this->addVar('regiones', $regiones);
		
		// lleno combo fabricantes
		$fabricantes = Fabricante::seek($db, '');

		$this->addVar('fabricantes', $fabricantes);
		
		$search_keyword = $fc->request->search_keyword;
		
		// ayuda en pantalla
		$user_help_desk = 
			"Mediante la b&uacute;squeda r&aacute;pida de la esquina superior izquierda se pueden consultar repuestos por plataforma, fabricante, pid, c&oacute;digo SAP, " .
			"descripcion del repuesto o n&uacute;mero serial.<br>Con los controles de la derecha podemos hacer b&uacute;squedas por cada par&aacute;metro se&ntilde;alado.<br>" .
			"Acciones:<br><br>" .
			"<img src=\"images/detail.png\" />&nbsp;Entrega detalles del repuesto.<br>" .
			"<img src=\"images/use.png\" />&nbsp;Saca repuesto por falla o TP.<br>" .
			"<img src=\"images/move.png\" />&nbsp;Mueve repuesto a otra R/E.<br>" .
			"<img src=\"images/trash.png\" />&nbsp;Elimina repuesto.<br>";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		$id_plataforma = $fc->request->plataforma;

		if (empty($_POST)) {
			// limpio variables
			HTTP_session::set('search_keyword', null);
			
			HTTP_session::set('search_keyword_2', null);
		}
		else if (isset($search_keyword)) {
			// submit
			
			$exito = null;
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "utiliza repuesto" y "agrega repuesto"
			
			HTTP_session::set('search_keyword', $search_keyword);
			
			// elimino el valor del form alternativo
			
			HTTP_session::set('search_keyword_2', null);
			
			$search_result = Repuesto::seekSpecial($db, $search_keyword);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			$fv=array();
			
			$fv[0]="search_keyword";
	
			$this->initFormVars($fv);
		}
		else if (isset($id_plataforma)) {
			// submit
			
			$exito = null;
			
			$parameters = array();
			
			// guardo el valor de busqueda para utilizarlo en "volver al listado" en "utiliza repuesto"
			
			HTTP_session::set('search_keyword_2', array(
				'id_plataforma'		=> $fc->request->plataforma,
				'id_region'			=> $fc->request->region,
				'id_ciudad'			=> $fc->request->ciudad,
				'id_radio_estacion'	=> $fc->request->re,
				'id_fabricante'		=> $fc->request->fabricante,
				'id_modelo'			=> $fc->request->modelo,
				'no borrado'		=> null,
			));
						
			// elimino el valor del form alternativo
			
			HTTP_session::set('search_keyword', null);
			
			if ($id_plataforma != '') {
				$parameters['id_plataforma'] = $id_plataforma;
			}
			
			$id_region = $fc->request->region;
			
			if ($id_region != '') {
				$parameters['id_region'] = $id_region;
			}
			
			$id_ciudad = $fc->request->ciudad;
			
			if ($id_ciudad != '') {
				$parameters['id_ciudad'] = $id_ciudad;
			}
			
			$id_re = $fc->request->re;
			
			if ($id_re != '') {
				$parameters['id_radio_estacion'] = $id_re;
			}
			
			$id_fabricante = $fc->request->fabricante;
			
			if ($id_fabricante != '') {
				$parameters['id_fabricante'] = $id_fabricante;
			}
			
			$id_modelo = $fc->request->modelo;
			
			if ($id_modelo != '') {
				$parameters['id_modelo'] = $id_modelo;
			}
			
			$parameters['no borrado'] = null;
			
			$parameters['estado'] = 'libre';
			
			$search_result = Repuesto::seek($db, $parameters, 'p.id_plataforma, rg.id_region', 'ASC', 0, 10000);
			
			// var_dump($search_result);
			
			$this->addVar('search_result', $search_result);
			
			$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
			
			$this->addVar('row_number', $row_number);
			
			$exito = true;
			
			$this->addVar('exito', $exito);
			
			// opciones en combo de ciudades
			$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
			
			// opciones en combo de radio estaciones
			$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
		
			// opciones en combo de modelos
			$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);
			
			$this->addVar('plataforma', $id_plataforma);
			$this->addVar('region', $id_region);
			$this->addVar('ciudad', $id_ciudad);
			$this->addVar('re', $id_re);
			$this->addVar('fabricante', $id_fabricante);
			$this->addVar('modelo', $id_modelo);
			
		}
		

		$this->processSuccess();
	}
}
?>