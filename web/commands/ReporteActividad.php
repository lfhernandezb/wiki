<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/RepuestoMovimiento.php');
include_once('../classes/TipoRepuestoMovimiento.php');
include_once('../classes/Usuario.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');

class ReporteActividad extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		$fecha_desde = null;
		
		$fecha_hasta = null;

		// lleno combo tipo movimiento
		$tipos = TipoRepuestoMovimiento::seek($db, array(), null, null, 0, 10000);
		
		$this->addVar('tipos', $tipos);
		
		// lleno combo plataformas
		$plataformas = Plataforma::seek($db, array(), null, null, 0, 10000);
					
		$this->addVar('plataformas', $plataformas);
			
		// lleno combo regiones
		$regiones = Region::seek($db, array(), 'orden', 'ASC', 0, 10000);
					
		$this->addVar('regiones', $regiones);
		
		// lleno combo fabricantes
		$fabricantes = Fabricante::seek($db, '');
					
		$this->addVar('fabricantes', $fabricantes);
		
		// lleno combo motivos
		$motivos = Motivo::seek($db, array(), null, null, 0, 10000);
					
		$this->addVar('motivos', $motivos);
		
		// lleno combo usuarios
		$usuarios = Usuario::seek($db, array(), null, null, 0, 10000);
					
		$this->addVar('usuarios', $usuarios);
		
		// ayuda en pantalla
		$user_help_desk = 
			"Despliega informaci&oacute;n de movimientos de repuestos, tanto de ingreso como egreso. Los par&aacute;metros de b&uacute;squeda son opcionales y permiten " .
			"resultados m&aacute;s ajustados.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		$dummy = $fc->request->dummy;
				
		if (!isset($dummy)) {
			// llamado desde home
			$fecha_desde = strftime('%Y-%m-%d');
			
			$fecha_hasta = $fecha_desde;
			
		}
		else {
			// submit
			
			$exito = null;
			
			$parameters = array();
						
			try {
				$id_tipo = $fc->request->tipo;
				
				if (isset($id_tipo) && $id_tipo != '') {
					$parameters['id_tipo'] = "$id_tipo";
				}
				
				$id_plataforma = $fc->request->plataforma;
				
				if (isset($id_plataforma) && $id_plataforma != '') {
					$parameters['id_plataforma'] = "$id_plataforma";
				}
			
				$id_region = $fc->request->region;
				
				if (isset($id_region) && $id_region != '') {
					$parameters['id_region'] = "$id_region";
				}
				
				$id_ciudad = $fc->request->ciudad;
				
				if (isset($id_ciudad) && $id_ciudad != '') {
					$parameters['id_ciudad'] = "$id_ciudad";
				}

				$id_radio_estacion = $fc->request->re;
				
				if (isset($id_radio_estacion) && $id_radio_estacion != '') {
					$parameters['id_radio_estacion'] = "$id_radio_estacion";
				}
				
				$id_fabricante = $fc->request->fabricante;
				
				if (isset($id_fabricante) && $id_fabricante != '') {
					$parameters['id_fabricante'] = "$id_fabricante";
				}

				$id_modelo = $fc->request->modelo;
				
				if (isset($id_modelo) && $id_modelo != '') {
					$parameters['id_modelo'] = "$id_modelo";
				}
				
				$id_motivo = $fc->request->motivo;
				
				$n_motivo = null;
				
				if (isset($id_motivo) && $id_motivo != '') {
					$parameters['id_motivo'] = "$id_motivo";

					$n_motivo = $fc->request->id_razon;
					
					if (isset($n_motivo) && $n_motivo != '') {
						$parameters['n_motivo'] = "$n_motivo";
					}
				}
				
				$id_usuario = $fc->request->usuario;
				
				if (isset($id_usuario) && $id_usuario != '') {
					$parameters['id_usuario'] = "$id_usuario";
				}
				
				$fecha_desde = $fc->request->fecha_desde;
				
				$parameters['fecha_desde'] = $fecha_desde . ' 00:00:00';
				
				$fecha_hasta = $fc->request->fecha_hasta;
				
				$parameters['fecha_hasta'] = $fecha_hasta . ' 23:59:59';
			
				$search_result = RepuestoMovimiento::seek($db, $parameters, 'rm.fecha', 'ASC', 0, 10000);
				
				// var_dump($search_result);
				
				$this->addVar('search_result', $search_result);
				
				$row_number = $db->RowCount() === false ? 0 : $db->RowCount();
				
				//echo '<br>row_number: ' . $row_number . '<br>';
				
				$this->addVar('row_number', $row_number);
				
				$exito = true;
				
				// opciones en combo de ciudades
				$this->addVar('options_ciudades', HTTP_Session::get('options_ciudades', null), null);
				
				// opciones en combo de radio estaciones
				$this->addVar('options_res', HTTP_Session::get('options_res', null), null);
			
				// opciones en combo de modelos
				$this->addVar('options_modelos', HTTP_Session::get('options_modelos', null), null);

				$this->addVar('tipo', $id_tipo);
				$this->addVar('plataforma', $id_plataforma);
				$this->addVar('region', $id_region);
				$this->addVar('ciudad', $id_ciudad);
				$this->addVar('re', $id_re);
				$this->addVar('fabricante', $id_fabricante);
				$this->addVar('modelo', $id_modelo);
				$this->addVar('motivo', $id_motivo);
				$this->addVar('id_razon', $n_motivo);
				$this->addVar('usuario', $id_usuario);
				
				
			} catch (Exception $e) {
				// estatus fracaso
				$exito = false;
				
				$status_message = 'Consulta fallida. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			
		}
		
		$this->addVar('fecha_desde', $fecha_desde);
		
		$this->addVar('fecha_hasta', $fecha_hasta);
		
		$this->processSuccess();

	}
}
?>