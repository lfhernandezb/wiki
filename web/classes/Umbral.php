<?php

include_once('mysql.class.php');

class Umbral
{
	private $_id;
	private $_id_zona;
	private $_id_plataforma;
	private $_id_modelo;
	private $_valor;
	
	public function __construct() {
		 
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_zona" :
        		$this->_id_zona = $value;
        		break;
        	case "id_plataforma" :
        		$this->_id_plataforma = $value;
        		break;
        	case "id_modelo" :
        		$this->_id_modelo = $value;
        		break;
        	case "valor" :
        		$this->_valor = $value;
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
        	case "id_zona" :
        		return $this->_id_zona;
        	case "id_plataforma" :
        		return $this->_id_plataforma;
        	case "id_modelo" :
        		return $this->_id_modelo;
        	case "valor" :
        		return $this->_valor;
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
				"  SELECT u.id_umbral AS id, u.id_zona_FK AS id_zona, u.id_plataforma_FK AS id_plataforma, u.id_modelo_FK AS id_modelo, u.valor AS valor" .
			 	"  FROM umbral u";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == "id_zona") {
	                $array_clauses[] = "u.id_zona_FK = $value";
	            }
	            else if ($key == 'id_plataforma') {
	                $array_clauses[] = "u.id_plataforma_FK = $value";
	            }
	            else if ($key == 'id_modelo') {
	                $array_clauses[] = "u.id_modelo_FK = $value";
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
	
	public static function getByID($p_db, $p_id_zona, $p_id_plataforma, $p_id_modelo) {
		$ret = null;
		
		$str_sql =
			"  SELECT u.id_umbral AS id, u.id_zona_FK AS id_zona, u.id_plataforma_FK AS id_plataforma, u.id_modelo_FK AS id_modelo, u.valor AS valor" .
		 	"  FROM umbral u" .
			"  WHERE u.id_zona_FK = $p_id_zona AND u.id_plataforma_FK = $p_id_plataforma AND u.id_modelo_FK = $p_id_modelo";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Umbral();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_zona = $ar[0]['id_zona'];
			$ret->_id_plataforma = $ar[0]['id_plataforma'];
			$ret->_id_modelo = $ar[0]['id_modelo'];
			$ret->_valor = $ar[0]['valor'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO umbral" .
			"  (" .
			"  id_zona_FK," .
			"  id_plataforma_FK," .
			"  id_modelo_FK," .
			"  valor" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_zona) ? "'{$this->_id_zona}'" : 'null') . ',' .
			"  " . (isset($this->_id_plataforma) ? "'{$this->_id_plataforma}'" : 'null') . ',' .
			"  " . (isset($this->_id_modelo) ? "'{$this->_id_modelo}'" : 'null') . ',' .
			"  " . (isset($this->_valor) ? "'{$this->_valor}'" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		
	}
	
	public function update($p_db) {
		
		$str_sql =
			"  UPDATE umbral" .
			"  SET valor = {$this->_valor}" .
			"  WHERE id_umbral = {$this->_id}";

		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
	}
	
	public function save_to_db($p_db) {
		try {
			$umb = Umbral::getByID($p_db, $this->_id_zona, $this->_id_plataforma, $this->_id_modelo);
			
			if (isset($umb)) {
				// registro existe, update
				$this->_id = $umb->id;
				$this->update($p_db);
			}
			else {
				// registro no existe, insert
				$this->insert($p_db);
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode(), null);
		}
	}
}
?>