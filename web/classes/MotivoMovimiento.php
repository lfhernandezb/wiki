<?php

include_once('mysql.class.php');

class MotivoMovimiento
{
	private $_id;
	private $_id_repuesto_movimiento;
	private $_id_motivo;
	private $_valor;
	
	protected static $select_header_seek = <<<EOD
  SELECT mm.id_motivo_movimiento AS id, mm.id_repuesto_movimiento_FK AS id_repuesto_movimiento, mm.id_motivo_FK AS id_motivo, mm.valor AS valor
  FROM motivo_movimiento mm
  JOIN repuesto_movimiento rm ON rm.id_repuesto_movimiento = mm.id_repuesto_movimiento_FK
  JOIN tipo_repuesto_movimiento trm ON trm.id_tipo_repuesto_movimiento = rm.id_tipo_repuesto_movimiento_FK
  JOIN repuesto r ON r.id_repuesto = rm.id_repuesto_FK
  JOIN motivo m ON m.id_motivo = mm.id_motivo_FK
EOD;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_repuesto_movimiento" :
        		$this->_id_repuesto_movimiento = $value;
        		break;
        	case "id_motivo" :
        		$this->_id_motivo = $value;
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
        	case "id_repuesto_movimiento" :
        		return $this->_id_repuesto_movimiento;
        	case "id_motivo" :
        		return $this->_id_motivo;
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
	
	/*
	public static function seek($p_param) {
		global $fc;
		
		$str_sql =
			"  SELECT r.id_repuesto_movimiento as id, p.descripcion as plataforma, f.descripcion as fabricante, m.descripcion as modelo, re.descripcion as radio_estacion, c.descripcion as ciudad, rg.descripcion as region" .
		 	"  FROM repuesto r" .
			"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
			"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
		    "  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
		    "  JOIN region rg ON rg.id_region = c.id_region_FK" .
			"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
			"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
			"  WHERE f.descripcion LIKE '%$p_param%'" .
			"  OR m.descripcion LIKE '%$p_param%'" .
			"  OR r.descripcion LIKE '%$p_param%'" .
			"  OR p.descripcion LIKE '%$p_param%'" .
			"  AND r.ubicacion IS NULL";
		
		// echo $str_sql . '<br>';
		
		return $fc->getLink()->QueryArray($str_sql, MYSQL_ASSOC);
	}
	*/
		
	public static function fromArray($p_ar) {
		$ret = new MotivoMovimiento();
		
		$ret->_id = $p_ar[0]['id'];
		$ret->_id_motivo = $p_ar[0]['id_motivo'];
		$ret->_id_repuesto_movimiento = $p_ar[0]['id_repuesto_movimiento'];
		$ret->_valor = $p_ar[0]['valor'];
		
		return $ret;
	}
	
	public static function getById($p_db, $p_id) {
		return self::getByParameter($p_db, 'id_motivo_movimiento', $p_id);
	}
	
	public static function getByIdRM($p_db, $p_id_repuesto_movimiento) {
		return self::getByParameter($p_db, 'id_repuesto_movimiento_FK', $p_id_repuesto_movimiento);
	}
	
	protected static function getByParameter($p_db, $p_key, $p_value) {
		$ret = null;
		
		$str_sql = self::$select_header_seek .
			"  WHERE mm.$p_key = $p_value";
		
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
			"  UPDATE motivo_movimiento" .
			"  SET id_motivo_FK = {$this->_id_motivo}," .
		    "  id_repuesto_movimiento_FK = {$this->_id_repuesto_movimiento}," .
			"  valor = {$this->_valor}," .
			"  WHERE id_motivo_movimiento = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO motivo_movimiento" .
			"  (" .
			"  id_repuesto_movimiento_FK," .
			"  id_motivo_FK," .
			"  valor" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  {$this->_id_repuesto_movimiento}," .
			"  {$this->_id_motivo}," .
			"  {$this->_valor}" .
			"  )";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al insertar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}

		$ar_id = $p_db->QueryArray('SELECT LAST_INSERT_ID()');
		
		$this->_id = $ar_id[0][0];
	}
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql = self::$select_header_seek;
		
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id_repuesto') {
	                $array_clauses[] = "r.id_repuesto = $value";
	            }
	            else if ($key == 'id_tipo_repuesto_movimiento') {
	                $array_clauses[] = "trm.id_tipo_repuesto_movimiento = $value";
	            }
	            else if ($key == 'id_motivo') {
	                $array_clauses[] = "m.id_motivo = $value";
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
	
}
?>