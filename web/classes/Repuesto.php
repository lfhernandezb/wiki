<?php

include_once('mysql.class.php');

class Repuesto
{
	private $_id;
	private $_id_plataforma;
	private $_plataforma;
	private $_region;
	private $_ciudad;
	private $_id_radio_estacion;
	private $_radio_estacion;
	private $_fabricante;
	private $_id_modelo;
	private $_modelo;
	private $_sap;
	private $_serial;
	private $_nombre;
	private $_posicion;
	private $_ip;
	private $_consola_activa;
	private $_consola_standby;
	private $_ubicacion;
	private $_sla;
	private $_id_encargado;
	private $_encargado;
	private $_cantidad;
	private $_pid;
	private $_modelo_descripcion;
	private $_region_nombre;
	private $_utilizado;
	private $_borrado;
	
	protected static $select_header_seek = <<<EOD
  SELECT r.id_repuesto as id, r.id_radio_estacion_FK AS id_radio_estacion, r.serial AS serial, r.nombre AS nombre, r.posicion AS posicion, r.ip AS ip, r.consola_activa AS consola_activa, r.consola_standby AS consola_standby, r.ubicacion AS ubicacion, r.sla AS sla, r.cantidad AS cantidad, 0+r.utilizado AS utilizado, 0+r.borrado AS borrado, p.descripcion as plataforma, f.descripcion as fabricante, CONCAT(CONCAT(m.pid, '  '), m.descripcion) AS modelo, m.sap AS sap, re.descripcion as radio_estacion, c.descripcion as ciudad, rg.descripcion as region, e.descripcion AS encargado, m.pid AS pid, m.descripcion AS modelo_descripcion, rg.nombre AS region_nombre
  FROM repuesto r
  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK
  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK
  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK
  JOIN region rg ON rg.id_region = c.id_region_FK
  JOIN modelo m ON m.id_modelo = r.id_modelo_FK
  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK
  LEFT JOIN encargado e ON e.id_encargado = r.id_encargado_FK
EOD;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_plataforma" :
        		$this->_id_plataforma = $value;
        		break;
        	case "id_radio_estacion" :
        		$this->_id_radio_estacion = $value;
        		break;
        	case "id_modelo" :
        		$this->_id_modelo = $value;
        		break;
        	case "id_encargado" :
        		$this->_id_encargado = $value;
        		break;
        	case "serial" :
        		$this->_serial = $value;
        		break;
        	case "nombre" :
        		$this->_nombre = $value;
        		break;
        	case "posicion" :
        		$this->_posicion = $value;
        		break;
        	case "ip" :
        		$this->_ip = $value;
        		break;
        	case "consola_activa" :
        		$this->_consola_activa = $value;
        		break;
        	case "consola_standby" :
        		$this->_consola_standby = $value;
        		break;
        	case "ubicacion" :
        		$this->_ubicacion = $value;
        		break;
        	case "sla" :
        		$this->_sla = $value;
        		break;
        	case "cantidad" :
        		$this->_cantidad = $value;
        		break;
        	case "utilizado" :
        		$this->_utilizado = $value;
        		break;
        	case "borrado" :
        		$this->_borrado = $value;
        		break;
        	default:
		        $trace = debug_backtrace();
		        trigger_error(
		            'Undefined property via __set(): ' . $name .
		            ' in ' . $trace[0]['file'] .
		            ' on line ' . $trace[0]['line'],
		            E_USER_NOTICE);
        }

    }

    public function __get($name)
    {
        //echo "Getting '$name'\n";
        switch ($name) {
        	case "id" :
        		return $this->_id;
        	case "id_radio_estacion" :
        		return $this->_id_radio_estacion;
        	case "plataforma" :
        		return $this->_plataforma;
        	case "region" :
        		return $this->_region;
        	case "ciudad" :
        		return $this->_ciudad;
        	case "radio_estacion" :
        		return $this->_radio_estacion;
        	case "fabricante" :
        		return $this->_fabricante;
        	case "modelo" :
        		return $this->_modelo;
        	case "sap" :
        		return $this->_sap;
        	case "serial" :
        		return $this->_serial;
        	case "nombre" :
        		return $this->_nombre;
        	case "posicion" :
        		return $this->_posicion;
        	case "ip" :
        		return $this->_ip;
        	case "consola_activa" :
        		return $this->_consola_activa;
        	case "consola_standby" :
        		return $this->_consola_standby;
        	case "ubicacion" :
        		return $this->_ubicacion;
        	case "sla" :
        		return $this->_sla;
        	case "encargado" :
        		return $this->_encargado;
        	case "cantidad" :
        		return $this->_cantidad;
        	case "pid" :
        		return $this->_pid;
        		break;
        	case "modelo_descripcion" :
        		return $this->_modelo_descripcion;
        		break;
        	case "region_nombre" :
        		return $this->_region_nombre;
        		break;
        	case "utilizado" :
        		return $this->_utilizado;
        	case "borrado" :
        		return $this->_borrado;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql = self::$select_header_seek .
			"  WHERE (f.descripcion LIKE '%$p_param%'" .
			"  OR m.descripcion LIKE '%$p_param%'" .
			"  OR m.pid LIKE '%$p_param%'" .
			"  OR m.sap LIKE '%$p_param%'" .
			"  OR r.serial LIKE '%$p_param%'" .
			"  OR p.descripcion LIKE '%$p_param%')" .
			"  AND r.cantidad > 0" .
			"  AND r.borrado = b'0'" .
			"  ORDER BY p.id_plataforma, rg.id_region";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
	}
	
    
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$select_header_seek;
					
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "r.id_repuesto = $value";
	            }
	            else if ($key == 'plataforma') {
	                $array_clauses[] = "p.descripcion = '$value'";
	            }
	            else if ($key == 'id_plataforma') {
	                $array_clauses[] = "p.id_plataforma = $value";
	            }
	            else if ($key == 'id_region') {
	                $array_clauses[] = "rg.id_region = $value";
	            }
	            else if ($key == 'ciudad') {
	                $array_clauses[] = "c.descripcion = '$value'";
	            }
	            else if ($key == 'id_ciudad') {
	                $array_clauses[] = "c.id_ciudad = $value";
	            }
	            else if ($key == 'radio_estacion') {
	                $array_clauses[] = "re.descripcion = '$value'";
	            }
	            else if ($key == 'id_radio_estacion') {
	                $array_clauses[] = "re.id_radio_estacion = $value";
	            }
	            else if ($key == 'fabricante') {
	                $array_clauses[] = "f.descripcion = '$value'";
	            }
	            else if ($key == 'id_fabricante') {
	                $array_clauses[] = "f.id_fabricante = $value";
	            }
	            else if ($key == 'modelo') {
	                $array_clauses[] = "m.descripcion = '$value'";
	            }
	            else if ($key == 'id_modelo') {
	                $array_clauses[] = "m.id_modelo = $value";
	            }
	            else if ($key == 'serial') {
	                $array_clauses[] = "r.serial = '$value'";
	            }
	            else if ($key == 'no borrado') {
	                $array_clauses[] = "r.borrado = b'0'";
	            }
	            else if ($key == 'borrado') {
	                $array_clauses[] = "r.borrado = b'1'";
	            }
	            else if ($key == 'estado' && $value == 'libre') {
	                $array_clauses[] = "r.cantidad > 0";
	            }
	            else if ($key == 'estado' && $value == 'ocupado') {
	                $array_clauses[] = "r.utilizado = b'1'";
	            }
	            else {
	            	throw new Exception('Parametro no soportado: ' . $key, null, null);
	            }
	        }
	        
	        $bFirstTime = false;
	        foreach($array_clauses as $clause) {
	            if (!$bFirstTime) {
	                 $bFirstTime = true;
	                 $str_sql .= ' WHERE ';
	            }
	            else {
	                 $str_sql .= ' AND ';
	            }
	            $str_sql .= $clause;
	        }
			
	        if (isset($order) && isset($direction)) {
	        	$str_sql .= " ORDER BY $order $direction";
	        }
	        
	        if (isset($offset) && isset($limit)) {
	        	$str_sql .= "  LIMIT $offset, $limit";
	        }
			
	        //echo '<br>' . $str_sql . '<br>';
		
			$ret = $db->QueryArray($str_sql, MYSQL_ASSOC);
			
			if (!is_array($ret)) {
				$ret = null;

				if ($db->RowCount() != 0) {
					throw new Exception('Error al obtener registro: ' . $db->Error(), $db->ErrorNumber(), null);
				}
			}
			
			return $ret;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
	}
    
	public static function getStockByZone($p_db, $p_id_zona, $p_id_plataforma, $p_id_fabricante, $p_id_modelo) {
		
		$str_sql =
			"  SELECT SUM(r.cantidad) as stock, p.descripcion as plataforma, f.descripcion as fabricante, CONCAT(CONCAT(m.pid, '  '), m.descripcion) AS modelo, m.sap AS sap, z.descripcion as zona, z.id_zona AS id_zona, p.id_plataforma AS id_plataforma, f.id_fabricante AS id_fabricante, m.id_modelo AS id_modelo, u.valor as umbral" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN zona_region zr ON zr.id_region_FK = rg.id_region" .
			"  JOIN zona z ON z.id_zona = zr.id_zona_FK" .
			"  LEFT JOIN umbral u ON u.id_zona_FK = z.id_zona AND u.id_plataforma_FK = p.id_plataforma AND u.id_modelo_FK = m.id_modelo" .
			"  WHERE r.cantidad > 0" .
			"  AND r.borrado = b'0'";
		
		if ($p_id_zona != '') {
			$str_sql .= "  AND z.id_zona = $p_id_zona";
		}
		
		if ($p_id_plataforma != '') {
			$str_sql .= "  AND p.id_plataforma = $p_id_plataforma";
		}
		
		if ($p_id_fabricante != '') {
			$str_sql .= "  AND f.id_fabricante = $p_id_fabricante";
		}

		if ($p_id_modelo != '') {
			$str_sql .= "  AND m.id_modelo = $p_id_modelo";
		}
		
		$str_sql .=
			"  GROUP BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo" .
			"  ORDER BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;
			
			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public static function getStockByZone2($p_db, $p_id_zona, $p_id_plataforma, $p_id_fabricante, $p_id_modelo) {
		
		$str_sql =
			"  SELECT r.id_repuesto as id, r.serial AS serial, r.nombre AS nombre, r.posicion AS posicion, r.ip AS ip, r.consola_activa AS consola_activa, r.consola_standby AS consola_standby, r.ubicacion AS ubicacion, r.sla AS sla, r.cantidad AS cantidad, 0+r.utilizado AS utilizado, 0+r.borrado AS borrado, p.descripcion as plataforma, f.descripcion as fabricante, CONCAT(CONCAT(m.pid, '  '), m.descripcion) AS modelo, m.sap AS sap, re.descripcion as radio_estacion, c.descripcion as ciudad, rg.descripcion as region, z.descripcion AS zona, e.descripcion AS encargado, m.pid AS pid, m.descripcion AS modelo_descripcion, rg.nombre AS region_nombre" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN zona_region zr ON zr.id_region_FK = rg.id_region" .
			"  JOIN zona z ON z.id_zona = zr.id_zona_FK" .
			"  LEFT JOIN umbral u ON u.id_zona_FK = z.id_zona AND u.id_plataforma_FK = p.id_plataforma AND u.id_modelo_FK = m.id_modelo" .
			"  LEFT JOIN encargado e ON e.id_encargado = r.id_encargado_FK" .
			"  WHERE r.cantidad > 0" .
			"  AND r.borrado = b'0'";
		
		if ($p_id_zona != '') {
			$str_sql .= "  AND z.id_zona = $p_id_zona";
		}
		
		if ($p_id_plataforma != '') {
			$str_sql .= "  AND p.id_plataforma = $p_id_plataforma";
		}
		
		if ($p_id_fabricante != '') {
			$str_sql .= "  AND f.id_fabricante = $p_id_fabricante";
		}

		if ($p_id_modelo != '') {
			$str_sql .= "  AND m.id_modelo = $p_id_modelo";
		}
		
		$str_sql .=
			//"  GROUP BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo" .
			"  ORDER BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;
			
			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public static function getStockByZoneDetail($p_db, $p_id_zona, $p_id_plataforma, $p_id_fabricante, $p_id_modelo) {
		
		$str_sql =
			"  SELECT SUM(r.cantidad) AS stock, p.descripcion AS plataforma, f.descripcion AS fabricante, m.descripcion AS modelo, z.descripcion AS zona, CONCAT(CONCAT(rg.descripcion, '  '), rg.nombre) AS region, c.descripcion AS ciudad, re.descripcion AS re" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN zona_region zr ON zr.id_region_FK = rg.id_region" .
			"  JOIN zona z ON z.id_zona = zr.id_zona_FK" .
			"  WHERE r.cantidad > 0" .
			"  AND r.borrado = b'0'";
		
		if ($p_id_zona != '') {
			$str_sql .= "  AND z.id_zona = $p_id_zona";
		}
		
		if ($p_id_plataforma != '') {
			$str_sql .= "  AND p.id_plataforma = $p_id_plataforma";
		}
		
		if ($p_id_fabricante != '') {
			$str_sql .= "  AND f.id_fabricante = $p_id_fabricante";
		}

		if ($p_id_modelo != '') {
			$str_sql .= "  AND m.id_modelo = $p_id_modelo";
		}
		
		$str_sql .=
			"  GROUP BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo, rg.id_region, c.id_ciudad, re.id_radio_estacion" .
			"  ORDER BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo, rg.id_region, c.id_ciudad, re.id_radio_estacion";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;
			
			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public static function getThresholdInfo($p_db, $p_id_zona, $p_id_plataforma, $p_id_fabricante, $p_id_modelo) {
		
		$str_sql =
			"  SELECT SUM(r.cantidad) as stock, p.descripcion as plataforma, f.descripcion as fabricante, CONCAT(CONCAT(m.pid, '  '), m.descripcion) AS modelo, m.sap AS sap, z.descripcion as zona, z.id_zona AS id_zona, p.id_plataforma AS id_plataforma, f.id_fabricante AS id_fabricante, m.id_modelo AS id_modelo, u.id_umbral AS id_umbral, u.valor as umbral, n.id_notificacion AS id_notificacion" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN zona_region zr ON zr.id_region_FK = rg.id_region" .
			"  JOIN zona z ON z.id_zona = zr.id_zona_FK" .
			"  JOIN umbral u ON u.id_zona_FK = z.id_zona AND u.id_plataforma_FK = p.id_plataforma AND u.id_modelo_FK = m.id_modelo" .
			"  LEFT JOIN notificacion n ON n.id_umbral_FK = u.id_umbral" .
			"  WHERE r.cantidad > 0" .
			"  AND r.borrado = b'0'";
		
		if (!empty($p_id_zona)) {
			$str_sql .= "  AND z.id_zona = $p_id_zona";
		}
		
		if (!empty($p_id_plataforma)) {
			$str_sql .= "  AND p.id_plataforma = $p_id_plataforma";
		}
		
		if (!empty($p_id_fabricante)) {
			$str_sql .= "  AND f.id_fabricante = $p_id_fabricante";
		}

		if (!empty($p_id_modelo)) {
			$str_sql .= "  AND m.id_modelo = $p_id_modelo";
		}
		
		$str_sql .=
			"  GROUP BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo" .
			"  ORDER BY z.id_zona, p.id_plataforma, f.id_fabricante, m.id_modelo";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {
			$ret = null;
			
			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public static function fromArray($p_ar) {
		$ret = new Repuesto();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_id_radio_estacion = $p_ar[0]['id_radio_estacion'];
		$ret->_plataforma = $p_ar[0]['plataforma'];
		$ret->_region = $p_ar[0]['region'];
		$ret->_ciudad = $p_ar[0]['ciudad'];
		$ret->_radio_estacion = $p_ar[0]['radio_estacion'];
		$ret->_fabricante = $p_ar[0]['fabricante'];
		$ret->_modelo = $p_ar[0]['modelo'];
		$ret->_sap = $p_ar[0]['sap'];
		$ret->_serial = $p_ar[0]['serial'];
		$ret->_nombre = $p_ar[0]['nombre'];
		$ret->_posicion= $p_ar[0]['posicion'];
		$ret->_ip = $p_ar[0]['ip'];
		$ret->_consola_activa = $p_ar[0]['consola_activa'];
		$ret->_consola_standby = $p_ar[0]['consola_standby'];
		$ret->_ubicacion = $p_ar[0]['ubicacion'];
		$ret->_sla = $p_ar[0]['sla'];
		$ret->_encargado = $p_ar[0]['encargado'];
		$ret->_cantidad = $p_ar[0]['cantidad'];
		$ret->_pid = $p_ar[0]['pid'];
		$ret->_modelo_descripcion = $p_ar[0]['modelo_descripcion'];
		$ret->_region_nombre = $p_ar[0]['region_nombre'];
		$ret->_utilizado = $p_ar[0]['utilizado'];
		$ret->_borrado = $p_ar[0]['borrado'];
		
		return $ret;
	}
	
	public static function getById($p_db, $p_id) {
		return self::getByParameter($p_db, 'id_repuesto', $p_id);
	}
	
	public static function getBySerial($p_db, $p_serial) {
		return self::getByParameter($p_db, 'serial', "'$p_serial'");
	}
	
	protected static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$select_header_seek .
			"  WHERE r.$p_key = $p_value";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = self::fromArray($ar);
		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
		return $ret;
		
	}
	
	public function update($p_db) {

		$str_sql =
			"  UPDATE repuesto" .
			"  SET" .
			"  id_radio_estacion_FK = " . (isset($this->_id_radio_estacion) ? "{$this->_id_radio_estacion}" : 'null') . ',' .
			"  serial = " . (isset($this->_serial) ? "'{$this->_serial}'" : 'null') . ',' .
			"  nombre = " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  posicion = " . (isset($this->_posicion) ? "'{$this->_posicion}'" : 'null') . ',' .
			"  ip = " . (isset($this->_ip) ? "'{$this->_ip}'" : 'null') . ',' .
			"  consola_activa = " . (isset($this->_consola_activa) ? "'{$this->_consola_activa}'" : 'null') . ',' .
			"  consola_standby = " . (isset($this->_consola_standby) ? "'{$this->_consola_standby}'" : 'null') . ',' .
			"  ubicacion = " . (isset($this->_ubicacion) ? "'{$this->_ubicacion}'" : 'null') . ',' .
			"  sla = " . (isset($this->_sla) ? "'{$this->_sla}'" : 'null') . ',' .
			"  id_encargado_FK = " . (isset($this->_id_encargado) ? "{$this->_id_encargado}" : 'null') . ',' .
			"  cantidad = " . (isset($this->_cantidad) ? "{$this->_cantidad}" : 'null') . ',' .
			"  utilizado = " . (isset($this->_utilizado) ? "b'{$this->_utilizado}'" : 'null') . ',' .
			"  borrado = " . (isset($this->_borrado) ? "b'{$this->_borrado}'" : 'null') .
			"  WHERE id_repuesto = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO repuesto" .
			"  (" .
			"  id_plataforma_FK," .
			"  id_radio_estacion_FK," .
			"  id_modelo_FK," .
			"  serial," .
			"  nombre," .
			"  posicion," .
			"  ip," .
			"  consola_activa," .
			"  consola_standby," .
			"  ubicacion," .
			"  sla," .
			"  id_encargado_FK," .
			"  cantidad" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_plataforma) ? "'{$this->_id_plataforma}'" : 'null') . ',' .
			"  " . (isset($this->_id_radio_estacion) ? "'{$this->_id_radio_estacion}'" : 'null') . ',' .
			"  " . (isset($this->_id_modelo) ? "'{$this->_id_modelo}'" : 'null') . ',' .
			"  " . (isset($this->_serial) ? "'{$this->_serial}'" : 'null') . ',' .
			"  " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  " . (isset($this->_posicion) ? "'{$this->_posicion}'" : 'null') . ',' .
			"  " . (isset($this->_ip) ? "'{$this->_ip}'" : 'null') . ',' .
			"  " . (isset($this->_consola_activa) ? "'{$this->_consola_activa}'" : 'null') . ',' .
			"  " . (isset($this->_consola_standby) ? "'{$this->_consola_standby}'" : 'null') . ',' .
			"  " . (isset($this->_ubicacion) ? "'{$this->_ubicacion}'" : 'null') . ',' .
			"  " . (isset($this->_sla) ? "'{$this->_sla}'" : 'null') . ',' .
			"  " . (isset($this->_id_encargado) ? "'{$this->_id_encargado}'" : 'null') . ',' .
			"  " . (isset($this->_cantidad) ? "{$this->_cantidad}" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
}
?>