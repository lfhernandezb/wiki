<?php

include_once('mysql.class.php');

class UmbralUsuario
{
	private $_id;
	private $_id_umbral;
	private $_id_usuario;
	
	public function __construct() {
		 
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_umbral" :
        		$this->_id_umbral = $value;
        		break;
        	case "id_usuario" :
        		$this->_id_usuario = $value;
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
        	case "id_usuario" :
        		return $this->_id_usuario;
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
				"  SELECT u.id_umbral_usuario AS id, u.id_umbral_FK AS id_umbral, u.id_usuario_FK AS id_usuario" .
			 	"  FROM umbral_usuario u" .
			 	"  LEFT JOIN umbral um ON um.id_umbral = u.id_umbral_FK";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "u.id_umbral_usuario = $value";
	            }
	            else if ($key == 'id_umbral') {
	                $array_clauses[] = "u.id_umbral_FK = $value";
	            }
	            else if ($key == 'id_usuario') {
	                $array_clauses[] = "u.id_usuario_FK = $value";
	            }
	            else if ($key == 'id_zona') {
	                $array_clauses[] = "um.id_zona_FK = $value";
	            }
	            else if ($key == 'id_plataforma') {
	                $array_clauses[] = "um.id_plataforma_FK = $value";
	            }
	            else if ($key == 'id_modelo') {
	                $array_clauses[] = "um.id_modelo_FK = $value";
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
	
	public static function getByID($p_db, $p_id_umbral, $p_id_usuario) {
		$ret = null;
		
		$str_sql =
			"  SELECT u.id_umbral_usuario AS id, u.id_umbral_FK AS id_umbral, u.id_usuario_FK AS id_usuario" .
		 	"  FROM umbral_usuario u" .
			"  WHERE u.id_umbral_FK = $p_id_umbral AND u.id_usuario_FK = $p_id_usuario";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Umbral();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_umbral = $ar[0]['id_umbral'];
			$ret->_id_usuario = $ar[0]['id_usuario'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO umbral_usuario" .
			"  (" .
			"  id_umbral_FK," .
			"  id_usuario_FK" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_umbral) ? "'{$this->_id_umbral}'" : 'null') . ',' .
			"  " . (isset($this->_id_usuario) ? "'{$this->_id_usuario}'" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
	
	public static function delete($p_db, $p_id_umbral, $p_id_usuario) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM umbral_usuario" .
			"  WHERE id_umbral_usuario IS NOT NULL";
		
		if (isset($p_id_umbral)) {
			$str_sql .= "  AND id_umbral_FK = $p_id_umbral";
		}
		
		if (isset($p_id_usuario)) {
			$str_sql .= "  AND id_usuario_FK = $p_id_usuario";
		}
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
}
?>