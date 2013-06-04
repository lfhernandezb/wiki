<?php

include_once('mysql.class.php');

class RadioEstacion
{
	private $_id;
	private $_id_ciudad;
	private $_descripcion;
	private $_table_name;
	
	public function __construct() {
		$this->_table_name = 'radio_estacion'; 
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
        	case "id_ciudad" :
        		$this->_id_ciudad = $value;
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
			
			$str_sql =
				"  SELECT re.id_radio_estacion AS id, re.id_ciudad_FK as id_ciudad, re.descripcion" .
			 	"  FROM radio_estacion re";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "re.id_radio_estacion = $value";
	            }
	            else if ($key == 'id_ciudad') {
				    $str_sql .= " " .
				    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK";
				    $array_clauses[] = "c.id_ciudad = $value";
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
	        	$str_sql .= " OFFSET $offset LIMIT $limit";
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
	
	public static function getByDescripcion($p_db, $p_descripcion) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_radio_estacion AS id, descripcion, id_ciudad_FK as id_ciudad" .
		 	"  FROM radio_estacion re" .
			"  WHERE re.descripcion = '$p_descripcion'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new RadioEstacion();
			
			$ret->_id = $ar[0]['id'];
			$ret->_descripcion = $ar[0]['descripcion'];
			$ret->_id_ciudad = $ar[0]['id_ciudad'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO radio_estacion" .
			"  (" .
			"  descripcion," .
			"  id_ciudad_FK" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  " . (isset($this->_id_ciudad) ? "'{$this->_id_ciudad}'" : 'null') .
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