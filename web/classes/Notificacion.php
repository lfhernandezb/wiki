<?php

include_once('mysql.class.php');

class Notificacion
{
	private $_id;
	private $_id_umbral;
	private $_fecha;
	
	public function __construct() {
		 
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_umbral" :
        		$this->_id_umbral = $value;
        		break;
        	case "fecha" :
        		$this->_fecha = $value;
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
        	case "id_umbral" :
        		return $this->_id_umbral;
        	case "fecha" :
        		return $this->_fecha;
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
				"  SELECT u.id_notificacion AS id, u.id_umbral_FK AS id_umbral, DATE_FORMAT(u.fecha, '%d-%b-%Y') AS fecha" .
			 	"  FROM notificacion u";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "u.id_notificacion = $value";
	            }
	            else if ($key == 'id_umbral') {
	                $array_clauses[] = "u.id_umbral_FK = $value";
	            }
	            else if ($key == 'fecha') {
	                $array_clauses[] = "u.fecha = $value";
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
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql =
			"  SELECT u.id_notificacion AS id, u.id_umbral_FK AS id_umbral, DATE_FORMAT(u.fecha, '%d-%b-%Y') AS fecha" .
		 	"  FROM notificacion u" .
			"  WHERE u.id_notificacion = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Umbral();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_umbral = $ar[0]['id_umbral'];
			$ret->_fecha = $ar[0]['fecha'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}
	
	public static function getByIDs($p_db, $p_id_umbral, $p_id_usuario) {
		$ret = null;
		
		$str_sql =
			"  SELECT u.id_notificacion AS id, u.id_umbral_FK AS id_umbral, DATE_FORMAT(u.fecha, '%d-%b-%Y') AS fecha" .
		 	"  FROM notificacion u" .
			"  WHERE u.id_umbral_FK = $p_id_umbral AND u.id_usuario_FK = $p_id_usuario";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Umbral();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_umbral = $ar[0]['id_umbral'];
			$ret->_fecha = $ar[0]['fecha'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO notificacion" .
			"  (" .
			"  id_umbral_FK," .
			"  fecha" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_umbral) ? "'{$this->_id_umbral}'" : 'null') . ',' .
			"  " . (isset($this->_fecha) ? "{$this->_fecha}" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
	
	public static function deleteByID($p_db, $p_id) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM notificacion" .
			"  WHERE id_notificacion = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
	public static function deleteByIDs($p_db, $p_id_umbral, $p_id_usuario) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM notificacion" .
			"  WHERE id_notificacion IS NOT NULL";
		
		if (isset($p_id_umbral)) {
			$str_sql .= "  AND id_umbral_FK = $p_id_zona";
		}
		
		if (isset($p_id_usuario)) {
			$str_sql .= "  AND id_usuario_FK = $p_id_modelo";
		}
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
}
?>