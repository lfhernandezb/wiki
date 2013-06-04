<?php

include_once('GenericCommand.php');
include_once('../classes/Repuesto.php');
include_once('../classes/Plataforma.php');
include_once('../classes/Region.php');
include_once('../classes/Fabricante.php');
include_once('../classes/Motivo.php');
include_once('../classes/Usuario.php');
include_once('../classes/Excel/reader.php');

class AgregaRepuestoMasivo extends GenericCommand{
	function execute(){
		global $fc;
		
		$db = $fc->getLink();
		
		/* ya en GenericCommand
		$puedeAgregar = false;
		
		$usuario = HTTP_Session::get('usuario', null);
		
		if ($usuario->tieneAcceso($db, 'agregar')) {
			$puedeAgregar = true;
		}

		$this->addVar('puedeAgregar', $puedeAgregar);
		*/
		
		// ayuda en pantalla
		$user_help_desk = 
			"Ingreso masivo de repuestos mediante un archivo Excel 97/2000/XP (.XLS). Archivos Excel 2003, 2007 (.XLSX) no est&aacute;n soportados. " .
			"Se procesa exclusivamente la primera hoja de la planilla. " .
			"La primera fila del archivo se considera de encabezado y es ignorada. Se despliegan los errores que se encuentren el el formato del archivo, as&iacute; como " .
			"tambi&eacute;n intentos por agregar repuestos ya presentes en la base de datos.";
		
		$this->addVar('user_help_desk', $user_help_desk);
		
		if (!isset($_FILES["upload"])) {
			// llamado desde menu
		}
		else {
			// submit
			
			$exito = null;			
			
			$num_errores = 0;
			
			$bInTransaction = false;
			
			try {
				if ($_FILES["upload"]["error"] > 0) {
					throw new Exception( "Error, {$_FILES["file"]["name"]}: " . $_FILES["upload"]["error"] );
				}

				/*
				echo "Upload: " . $_FILES["file"]["name"] . "<br />";
				echo "Type: " . $_FILES["file"]["type"] . "<br />";
				echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
				echo "Stored in: " . $_FILES["file"]["tmp_name"];
				*/				
				
				if ($_FILES["upload"]["size"] == 0) {
				      throw new Exception( "Error, {$_FILES["file"]["name"]} no tiene datos" );
				}
				if (pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION) != 'xls' || $_FILES["upload"]["type"] != 'application/vnd.ms-excel') {
				      throw new Exception( "Error, {$_FILES["file"]["name"]} no es una planilla Excel 97/2000/XP" );
				}
				
				// archivo candidato... lo copio
				$str_sql = "SELECT DATE_FORMAT(now(), '%Y%m%d%H%i%s') AS sufix";
				
				$ar = $db->QueryArray($str_sql, MYSQL_ASSOC);

				$sufix = null;
		
				if (is_array($ar)) {
					$sufix = $ar[0]['sufix'];
				}
				else {
					throw new Exception( "Error, no se pudo obtener la hora desde el servidor" );
				}
				
				$filename = "carga_{$this->usuario->nombre_usuario}_$sufix.xls";
				
				if (file_exists("upload/$filename")) {
					throw new Exception( "Error, archivo $filename ya existe" );
				}
				
				move_uploaded_file($_FILES["upload"]["tmp_name"], "upload/$filename");
				//echo "Stored in: " . "$filename";
				
				if (!is_file("upload/$filename")) {
				      throw new Exception( "Error, $filename no es un archivo regular" );
				}
				if (!is_readable("upload/$filename")) {
				      throw new Exception( "Error, $filename no es un archivo legible" );
				}
				
				// a partir del xls genero CSV
				
				
				
				
				
				
				// ExcelFile($filename, $encoding);
				$data = new Spreadsheet_Excel_Reader();
				
				
				// Set output Encoding.
				$data->setOutputEncoding('UTF-8');
				
				/***
				* if you want you can change 'iconv' to mb_convert_encoding:
				* $data->setUTFEncoder('mb');
				*
				**/
				
				/***
				* By default rows & cols indeces start with 1
				* For change initial index use:
				* $data->setRowColOffset(0);
				*
				**/
				
				
				
				/***
				*  Some function for formatting output.
				* $data->setDefaultFormat('%.2f');
				* setDefaultFormat - set format for columns with unknown formatting
				*
				* $data->setColumnFormat(4, '%.3f');
				* setColumnFormat - set format for column (apply only to number fields)
				*
				**/
				
				$data->read("upload/$filename");
				
				/*
				
				
				 $data->sheets[0]['numRows'] - count rows
				 $data->sheets[0]['numCols'] - count columns
				 $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column
				
				 $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
				    
				    $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
				        if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
				    $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format 
				    $data->sheets[0]['cellsInfo'][$i][$j]['colspan'] 
				    $data->sheets[0]['cellsInfo'][$i][$j]['rowspan'] 
				*/
				
				$filename = "carga_{$this->usuario->nombre_usuario}_$sufix.csv";
				
				$fp = fopen("upload/$filename", "w");
				
				for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
					// ignoro líneas vacias
					if (!empty($data->sheets[0]['cells'][$i][1])) {
						for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
							// saco comillas dobles
							fputs($fp, "\"".str_replace("\"", '', $data->sheets[0]['cells'][$i][$j])."\"");
							
							if ($j < $data->sheets[0]['numCols']) {
								fputs($fp, ",");
							}
						}
						
						fputs($fp, "\n");
					}
				
				}
				
				fclose ($fp);
				
				
				
				// borro tabla temporal
$str_sql = <<<EOD
DROP  TABLE IF EXISTS `sigrep`.`tmp_repuesto`;
EOD;
	
				$db->Query($str_sql);
				
				// la creo
				
$str_sql = <<<EOD
CREATE  TABLE IF NOT EXISTS `sigrep`.`tmp_repuesto` (
  `plataforma` VARCHAR(10) NOT NULL ,
  `region` VARCHAR(4) NOT NULL ,
  `ciudad` VARCHAR(20) NULL DEFAULT NULL ,
  `re` VARCHAR(20) NOT NULL ,
  `descripcion` VARCHAR(40) NOT NULL ,
  `fabricante` VARCHAR(20) NOT NULL ,
  `sap` INT NULL DEFAULT NULL ,
  `pid` VARCHAR(40) NULL DEFAULT NULL ,
  `serial` VARCHAR(20) NULL DEFAULT NULL ,
  `nombre` VARCHAR(100) NULL DEFAULT NULL ,
  `posicion` VARCHAR(20) NULL DEFAULT NULL ,
  `direccion_ip` VARCHAR(15) NULL DEFAULT NULL ,
  `consola_activa` VARCHAR(30) NULL DEFAULT NULL ,
  `consola_standby` VARCHAR(30) NULL DEFAULT NULL ,
  `ubicacion` VARCHAR(100) NULL DEFAULT NULL ,
  `sla` VARCHAR(20) NULL DEFAULT NULL ,
  `encargado` VARCHAR(128) NULL DEFAULT NULL,
  `cantidad` SMALLINT DEFAULT 1 )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
EOD;
				
				$db->Query($str_sql);
				
				// inicio transaccion
				/*
				if (!$db->TransactionBegin()) {
					throw new Exception('Error al iniciar transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
				}
				
				$bInTransaction = true;
				*/
				
				// comienzo a leer archivo
				$ar_errores = array();
				
				
				$file = fopen("upload/$filename", "r");
				$nline = 0;
				
				$bFoundValidRow = false;
				
				while(!feof($file)) {
					$nline++;
					
					// ignoramos la primera linea
					if ($nline != 1) {
						$columns = fgetcsv($file);
						
						if (count($columns) < 15 && count($columns) > 1) {
							//var_dump($columns);
							$ar_errores[] = array(
								'linea' => $nline,
								'descripcion' => 'No tiene el n&uacute;mero esperado de columnas (' . count($columns) .')',
							);
						}
						else {
							// chequeo que el S/N no este ya presente en la BD
							if (strlen($columns[7]) > 0) {
								$r = Repuesto::getBySerial($db, $columns[7]);

								if (!empty($r)) {
									$ar_errores[] = array(
										'linea' => $nline,
										'descripcion' => "El S/N {$columns[7]} ya est&aacute; registrado",
									);
								}
								else {
									$bFoundValidRow = true;
								}
							}
						}
					}
				}
				
				fclose($file);		
				/*
				echo '<br>';
				
				var_dump($ar_errores);
				
				echo '<br>';
				*/
				$num_errores = count($ar_errores);
				
				$this->addVar('num_errores', $num_errores);
				
				$this->addVar('ar_errores', $ar_errores);
				
				if ($bFoundValidRow) {
					// cargo datos
					
					$filepath = dirname($_SERVER["SCRIPT_FILENAME"]) . "/upload/$filename";
					
$str_sql = <<<EOD
LOAD DATA LOCAL INFILE '$filepath'
INTO TABLE tmp_repuesto 
CHARACTER SET latin1
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\\n'
IGNORE 1 LINES
(plataforma, region, ciudad, re, descripcion, fabricante, sap, pid, serial, nombre, posicion, direccion_ip, consola_activa, consola_standby, ubicacion, sla, encargado, cantidad);
EOD;
					
					$db->Query($str_sql);
					
					$str_sql = "UPDATE tmp_repuesto SET cantidad = 1 WHERE cantidad = 0";
					
					$db->QueryArray($str_sql);
					
					$str_sql = "CALL normaliza_datos('$filename', " . $this->usuario->id . ")";
					
					$aux_ar = $db->QueryArray($str_sql);
					
					// commit
					if ($bInTransaction) {
						if (!$db->TransactionEnd()) {
							throw new Exception('Error al comitear transaccion: ' . $db->Error(), $db->ErrorNumber(), null);
						}
					}

					$str_sql = 
						"  SELECT COUNT(*) AS n FROM repuesto r" . 
						"  JOIN repuesto_archivo_carga rac ON rac.id_repuesto_FKEY = r.id_repuesto" .
						"  JOIN archivo_carga ac ON ac.id_archivo_carga = rac.id_archivo_carga_FKEY" .
						"  WHERE ac.descripcion = '$filename'";
					
					$ar = $db->QueryArray($str_sql, MYSQL_ASSOC);
					
					$num_rows = null;
	
					if (is_array($ar)) {
						$num_rows = $ar[0]['n'];
					}
					else {
						throw new Exception("Error, no se pudo obtener el n&uacute;mero de registros insertados");
					}
					
					if ($num_rows > 0) {
						// estatus exito
						$exito = true;
						
						$status_message = "Se han agregado exitosamente $num_rows repuestos";
					}
					else {
						// estatus fallo
						$exito = false;
						
						$status_message = "El archivo subido no contiene datos de repuestos v&aacute;lidos";
						
					}
				}
				else {
					
					// estatus fallo
					$exito = false;
					
					$status_message = "El archivo subido no contiene datos de repuestos v&aacute;lidos";
				}
				
			} catch (Exception $e) {
				// rollback
				
				if ($bInTransaction) {
					$db->TransactionRollback();
				}
				
				$exito = false;

				$status_message = 'Repuesto no pudo ser agregadoado. Raz&oacute;n: ' . $e->getMessage();	
			}
			
			$this->addVar('exito', $exito);
			
			$this->addVar("status_message", $status_message);
			
			$aux = $_FILES["upload"]["name"];
			
			$this->addVar('file', $aux);
			
			/*
			$fv=array();
			
			$fv[0]="upload";
			
			$this->initFormVars($fv);
			*/
		}
		

		$this->processSuccess();
	}
}
?>