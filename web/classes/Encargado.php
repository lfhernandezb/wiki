<?php

include_once('mysql.class.php');

class Encargado
{
	private $_id;
	private $_descripcion;
	
	protected static $select_header_seek = <<<EOD
  SELECT DISTINCT e.id_encargado AS id, e.descripcion AS descripcion
  FROM encargado e
  JOIN repuesto r ON r.id_encargado_FK = e.id_encargado
  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK
  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK
  JOIN region rg ON rg.id_region = c.id_region_FK
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
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$select_header_seek;
					
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "e.id_encargado = $value";
	            }
	            else if ($key == 'id_region') {
	                $array_clauses[] = "rg.id_region = $value";
	            }
	            else if ($key == 'descripcion') {
	                $array_clauses[] = "e.descripcion = '$value'";
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
    	
	public static function fromArray($p_ar) {
		$ret = new Encargado();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_descripcion = $p_ar[0]['descripcion'];
		
		return $ret;
	}
	
	public static function getById($p_db, $p_id) {
		return self::getByParameter($p_db, 'id_encargado', $p_id);
	}
	
	public static function getByDescripcion($p_db, $p_descripcion) {
		return self::getByParameter($p_db, 'descripcion', "'$p_descripcion'");
	}
	
	protected static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$select_header_seek .
			"  WHERE e.$p_key = $p_value";
		
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
    
    /*
	public function update($p_db) {
		$str_sql =
			"  UPDATE repuesto_movimiento" .
			"  SET id_usuario_FK = {$this->_id_usuario}," .
			"  fecha = {$this->_fecha}," .
			"  WHERE id_repuesto_FK = {$this->_id_repuesto}" .
			"  AND id_tipo_repuesto_movimiento_FK = {$this->_id_tipo_repuesto_movimiento}";
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $$p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}
	*/
	
	public function insert($p_db) {
		$str_sql =
			"  INSERT INTO encargado" .
			"  (" .
			"  descripcion" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') .
			"  )";
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
	}
	
}
?>