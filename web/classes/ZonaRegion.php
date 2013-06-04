<?php

include_once('mysql.class.php');

class ZonaRegion
{
	private $_id_zona;
	private $_id_region;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_zona" :
        		$this->_id_zona = $value;
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
        	case "id_zona" :
        		return $this->_id_zona;
        	case "id_region" :
        		return $this->_id_region;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
	
    public static function seek($p_db, $p_id_zona) {
		
		$str_sql =
			"  SELECT id_zona_FK AS id_zona, id_region_FK AS id_region" .
		 	"  FROM zona_region zr" .
			"  WHERE zr.id_region_FK = $p_id_zona";
		
		// echo '<br> . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
	}
	/*
	public static function getByid_region($p_db, $p_id_region) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_zona AS id, id_region" .
		 	"  FROM zona z" .
			"  WHERE z.id_region = '$p_id_region'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Fabricante();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_region = $ar[0]['id_region'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_zona AS id, id_region" .
		 	"  FROM zona z" .
			"  WHERE z.id_zona = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Zona();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_region = $ar[0]['id_region'];
 		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
 		 		
		return $ret;
		
	}
	*/
	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO zona_region" .
			"  (" .
			"  id_zona_FK," .
			"  id_region_FK" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_id_zona) ? "'{$this->_id_zona}'" : 'null') . ',' .
			"  " . (isset($this->_id_region) ? "'{$this->_id_region}'" : 'null') .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		/*
		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
		*/
	}
	
	public static function delete($p_db, $p_id_zona) {
		
		$str_sql =
			"  DELETE" .
		 	"  FROM zona_region" .
			"  WHERE id_zona_FK IS NOT NULL";
		
		if (isset($p_id_zona)) {
			$str_sql .= "  AND id_zona_FK = $p_id_zona";
		}
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al borrar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
				
	}
	
}
?>