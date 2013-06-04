<?php

include_once('mysql.class.php');

class TipoRepuestoMovimiento
{
	private $_id;
	private $_descripcion;
	
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
			
			$str_sql =
				"  SELECT trm.id_tipo_repuesto_movimiento AS id, trm.descripcion AS descripcion" .
			 	"  FROM tipo_repuesto_movimiento trm";
		
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "trm.id_tipo_repuesto_movimiento = $value";
	            }
	            else if ($key == 'descripcion') {
	                $array_clauses[] = "trm.descripcion = '$value'";
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
    	
	public static function getByDescripcion($p_db, $p_descripcion) {
		
		$ret = null;
		
		$str_sql =
			"  SELECT id_tipo_repuesto_movimiento AS id, descripcion" .
		 	"  FROM tipo_repuesto_movimiento" .
			"  WHERE descripcion = '$p_descripcion'";
		
		//echo '<br>' . $str_sql . '<br>';
				
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new TipoRepuestoMovimiento();
			
			$ret->_id = $ar[0]['id'];
			$ret->_descripcion = $ar[0]['descripcion'];
		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
		return $ret;
		
	}

	public static function getByID($p_db, $p_id) {
		
		$ret = null;
		
		$str_sql =
			"  SELECT id_tipo_repuesto_movimiento AS id, descripcion" .
		 	"  FROM tipo_repuesto_movimiento" .
			"  WHERE id_tipo_repuesto_movimiento = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
				
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new TipoRepuestoMovimiento();
			
			$ret->_id = $ar[0]['id'];
			$ret->_descripcion = $ar[0]['descripcion'];
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
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}
	*/
	/*
	public function insert($p_db) {
		$str_sql =
			"  INSERT INTO repuesto_movimiento" .
			"  (" .
			"  id_repuesto_FK," .
			"  id_tipo_repuesto_movimiento_FK," .
			"  id_usuario_FK," .
			"  fecha," .
			"  )" .
			"  VALUES" .
			"  (" .
			"  {$this->_id_repuesto}," .
			"  {$this->_id_tipo_repuesto_movimiento}," .
			"  {$this->_id_usuario}," .
			"  {$this->_fecha}," .
			"  )";
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}
	*/
}
?>