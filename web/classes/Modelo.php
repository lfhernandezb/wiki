<?php

include_once('mysql.class.php');

class Modelo
{
	private $_id;
	private $_id_fabricante;
	private $_descripcion;
	private $_pid;
	private $_sap;
	
	protected static $select_header_seek = <<<EOD
  SELECT m.id_modelo AS id, m.id_fabricante_FK AS id_fabricante, m.descripcion AS descripcion, m.pid AS pid, m.sap AS sap
  FROM modelo m
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
        	case "id_fabricante" :
        		$this->_id_fabricante = $value;
        		break;
        	case "descripcion" :
        		$this->_descripcion = $value;
        		break;
        	case "pid" :
        		$this->_pid = $value;
        		break;
        	case "sap" :
        		$this->_sap = $value;
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
        	case "id_fabricante" :
        		return $this->_id_fabricante;
        	case "descripcion" :
        		return $this->_descripcion;
        	case "pid" :
        		return $this->_pid;
        	case "sap" :
        		return $this->_sap;
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
				/*
				"  SELECT m.id_modelo AS id, m.id_fabricante_FK AS id_fabricante, m.descripcion AS descripcion, m.pid AS pid, m.sap AS sap" .
			 	"  FROM modelo m";
				*/
			
	        foreach($parameters as $key => $value) {
	    		if ($key == "id") {
	                $array_clauses[] = "m.id_modelo = $value";
	            }
	    		else if ($key == "pid") {
	                $array_clauses[] = "m.pid = '$value'";
	            }
	    		else if ($key == "sap") {
	                $array_clauses[] = "m.sap = $value";
	            }
	            else if ($key == 'valido') {
	            	$array_clauses[] = "m.descripcion IS NOT NULL AND TRIM(m.descripcion) <> ''";
	            }
	            else if ($key == 'id_fabricante') {
				    $str_sql .= " " .
				    "  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK";
				    $array_clauses[] = "f.id_fabricante = $value";
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
		
	public static function getById($p_db, $p_id) {
		return self::getByParameter($p_db, 'id_modelo', $p_id);
	}
	
	public static function getByDescripcion($p_db, $p_descripcion) {
		return self::getByParameter($p_db, 'descripcion', "'$p_descripcion'");
	}
	
	public static function getBySAP($p_db, $p_sap) {
		return self::getByParameter($p_db, 'sap', "$p_sap");
	}
	
	protected static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$select_header_seek .
			"  WHERE m.$p_key = $p_value";
		
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
	
	public static function fromArray($p_ar) {
		$ret = new Modelo();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_id_fabricante = $p_ar[0]['id_fabricante'];
		$ret->_descripcion = $p_ar[0]['descripcion'];
		$ret->_pid = $p_ar[0]['pid'];
		$ret->_sap = $p_ar[0]['sap'];
		
		return $ret;
	}
	
	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO modelo" .
			"  (" .
			"  id_fabricante_FK," .
			"  descripcion," .
			"  pid," .
			"  sap" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_fabricante) ? "{$this->_id_fabricante}" : 'null') . ',' .
			"  " . (isset($this->_descripcion) ? "'{$this->_descripcion}'" : 'null') . ',' .
			"  " . (isset($this->_pid) ? "'{$this->_pid}'" : 'null') . ',' .
			"  " . (isset($this->_sap) ? "{$this->_sap}" : 'null') .
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