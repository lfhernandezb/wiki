<?php

include_once('mysql.class.php');

class Ciudad
{
	private $_id;
	private $_descripcion;
	private $_id_region;
	private $_table_name;
	
	public function __construct() {
		$this->_table_name = 'ciudad'; 
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
        	case "id_region" :
        		$this->_id_region = $value;
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
				"  SELECT c.id_ciudad AS id, c.descripcion" .
			 	"  FROM ciudad c";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "c.id_ciudad = $value";
	            }
	            else if ($key == 'id_region') {
				    $str_sql .= " " .
				    "  JOIN region r ON r.id_region = c.id_region_FK";
				    $array_clauses[] = "r.id_region = $value";
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
			"  SELECT id_ciudad AS id, descripcion, id_region_FK AS id_region" .
		 	"  FROM ciudad c" .
			"  WHERE c.descripcion = '$p_descripcion'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Ciudad();
			
			$ret->_id = $ar[0]['id'];
			$ret->_descripcion = $ar[0]['descripcion'];
			$ret->_id_region = $ar[0]['id_region'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO ciudad" .
			"  (" .
			"  descripcion," .
			"  id_region_FK" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  " . (isset($this->_id_region) ? "'{$this->_id_region}'" : 'null') .
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