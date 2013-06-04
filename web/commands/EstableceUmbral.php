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
include_once('../classes/Umbral.php');
include_once('../classes/UmbralUsuario.php');

class EstableceUmbral extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();

		$id_zona = $fc->request->id_zona;
		
		$zona = Zona::getByID($db, $id_zona);
		
		$this->addVar('id_zona', $id_zona);
		$this->addVar('zona', $zona->descripcion);
		
		$id_plataforma = $fc->request->id_plataforma;
		
		$plataforma = Plataforma::getByID($db, $id_plataforma);
		
		$this->addVar('id_plataforma', $id_plataforma);
		$this->addVar('plataforma', $plataforma->descripcion);
				
		$id_fabricante = $fc->request->id_fabricante;
		
		$fabricante = Fabricante::getByID($db, $id_fabricante);
		
		$this->addVar('id_fabricante', $id_fabricante);
		$this->addVar('fabricante', $fabricante->descripcion);
		
		$id_modelo = $fc->request->id_modelo;
		
		$modelo = Modelo::getByID($db, $id_modelo);
		
		$this->addVar('id_modelo', $id_modelo);
		
		$m = $modelo->pid . '  ' . $modelo->descripcion;
		$this->addVar('modelo', $m);
				
		$this->addVar('sap', $modelo->sap);
		
		$stock = $fc->request->stock;
		
		$this->addVar('stock', $stock);
		
		$umbral = $fc->request->umbral;
		
		$this->addVar('umbral', $umbral);

		// para checkboxes de usuarios
		$usuarios = Usuario::seek($db, '');
		
		$this->addVar('usuarios', $usuarios);
		
		// ayuda en pantalla
		$user_help_desk = 
			"Permite establecer los umbrales de criticidad de existencias de repuestos, y los usuarios que ser&aacute;n notificados cuando esos umbrales sean alcanzados.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
				
		// recuerdo los parametros de busqueda para el comando 'volver', donde se submiteara a ReporteExistancias
		$ar = HTTP_session::get('search_keyword_reporte_existencias', null);
		
		if (isset($ar)) {
			if (is_array($ar)) {
				$this->addVar('sel_id_zona', $ar['id_zona']);
				$this->addVar('sel_id_plataforma', $ar['id_plataforma']);
				$this->addVar('sel_id_fabricante', $ar['id_fabricante']);
				$this->addVar('sel_id_modelo', $ar['id_modelo']);
			}
		}
		
		$z = $fc->request->zona;
		
		if (!isset($z)) {
			// llamado desde reporte de existencias
			
		}
		else {
			// submit
			$exito = null;
						
			try {
				
				$bInTransaction = false;
								
				$status_message = '';
				
				try {
					
					// inicio transaccion
					
					if (!$db->TransactionBegin()) {
						throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					$bInTransaction = true;
					
					$umb = new Umbral();
					
					$umb->id_zona = $id_zona;
					$umb->id_plataforma = $id_plataforma;
					$umb->id_modelo = $id_modelo;
					$umb->valor = $umbral;
					
					$umb->save_to_db($db);
					
					// notificaciones
					$ar_usuario = $fc->request->usuario;
					
					UmbralUsuario::delete($db, $umb->id, null);
					
					foreach ($ar_usuario as $k => $v) {
						$umbral_usuario = new UmbralUsuario();

						$umbral_usuario->id_umbral = $umb->id;
						$umbral_usuario->id_usuario = $usuarios[$k]['id'];
						
						$umbral_usuario->insert($db);
					}
															
					// commit
					
					if (!$db->TransactionEnd()) {
						throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
					}
					
					// estatus exito
					$exito = true;
					
					$status_message = 'Umbral establecido exitosamente';
					
				} catch (Exception $e) {
					// rollback
					if ($bInTransaction) {
						$db->TransactionRollback();
					}
					
					throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
				}
			} catch (Exception $e) {
				// estatus fracaso
				$exito = false;
				
				$status_message = 'Umbral no pudo ser establecido. Raz&oacute;n: ' . $e->getMessage();
			}
				
			$this->addVar("exito", $exito);
			
			$this->addVar("status_message", $status_message);
			
			$fv=array();
			
			$fv[0]="zona";
			$fv[1]="plataforma";
			$fv[2]="fabricante";
			$fv[3]="modelo";
			$fv[4]="stock";
			$fv[5]="umbral";
			
			$this->initFormVars($fv);
		}
		
		// usuarios para notificacion
		$parameters = array(
			'id_zona'			=> "$id_zona",
			'id_plataforma'		=> "$id_plataforma",
			'id_modelo'			=> "$id_modelo",
		);
		
		$notificados = UmbralUsuario::seek($db, $parameters, null, null, 0, 10000);
		
		$this->addVar('notificados', $notificados);
		
		
		
		$this->processSuccess();

	}
}
?>