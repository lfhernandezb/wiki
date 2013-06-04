<?php

include_once('mysql.class.php');

class Region
{
	private $_id;
	private $_descripcion;
	private $_nombre;
	
	protected static $select_header_seek = <<<EOD
  SELECT rg.id_region AS id, rg.descripcion, rg.nombre, CONCAT(CONCAT(rg.descripcion, '  '), rg.nombre) AS compuesto
  FROM region rg
EOD;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "descripcion" :
        		$this->_descripcion = $value;
        		break;
        	case "nombre" :
        		$this->_nombre = $value;
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
        	case "descripcion" :
        		return $this->_descripcion;
        	case "nombre" :
        		return $this->_nombre;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
	/*
	public static function seek($p_db, $p_param) {
		
		$str_sql =
			"  SELECT id_region AS id, CONCAT(CONCAT(descripcion, '  '), nombre) AS descripcion" .
		 	"  FROM region r" .
			"  WHERE r.descripcion LIKE '%$p_param%'" .
			"  ORDER BY orden";
		
		// echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
	}
	*/
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$select_header_seek;
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "rg.id_region = $value";
	            }
	    		else if ($key == "id distinto") {
	                $array_clauses[] = "rg.id_region <> $value";
	            }
	            else if ($key == "descripcion") {
	                $array_clauses[] = "rg.descripcion = $value";
	            }
	    		else if ($key == "nombre") {
	                $array_clauses[] = "rg.nombre = $value";
	            }
	    		else if ($key == "compuesto") {
	                $array_clauses[] = "(rg.descripcion = '$value' OR rg.nombre LIKE '%$value%')";
	            }
	            else if ($key == 'id_zona') {
				    $str_sql .= " " .
				    "  JOIN zona_region zr ON zr.id_region_FK = rg.id_region";
				    $array_clauses[] = "zr.id_zona_FK = $value";
	            }
	            else if ($key == 'sin zona') {
				    $array_clauses[] = "rg.id_region NOT IN (SELECT id_region_FK FROM zona_region)";;
	            }
	            else if ($key == 'posible') {
				    $str_sql .= " " .
				    "  LEFT JOIN zona_region zr ON zr.id_region_FK = rg.id_region";
	            	$array_clauses[] = "(zr.id_zona_FK = $value OR rg.id_region NOT IN (SELECT id_region_FK FROM zona_region))";;
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

				if ($db->RowCount() != 0) {
					throw new Exception('Error al obtener registro: ' . $db->Error(), $db->ErrorNumber(), null);
				}
			}
			
			return $ret;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
	}
	
	public static function fromArray($p_ar) {
		$ret = new Region();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_descripcion = $p_ar[0]['descripcion'];
		$ret->_nombre = $p_ar[0]['nombre'];
		
		return $ret;
	}
	
	public static function getById($p_db, $p_id) {
		return self::getByParameter($p_db, 'id_region', $p_id);
	}
	
	public static function getByDescripcion($p_db, $p_descripcion) {
		return self::getByParameter($p_db, 'descripcion', "'$p_descripcion'");
	}
	
	public static function getByNombre($p_db, $p_nombre) {
		return self::getByParameter($p_db, 'nombre', "'$p_nombre'");
	}
	
	protected static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$select_header_seek .
			"  WHERE rg.$p_key = $p_value";
		
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
			"  UPDATE region" .
			"  SET" .
			"  descripcion = " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  nombre = " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') .
			"  WHERE id_region = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}
	/*
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
	*/
}
?>