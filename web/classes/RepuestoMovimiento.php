<?php

include_once('mysql.class.php');

class RepuestoMovimiento
{
	private $_id;
	private $_id_repuesto;
	private $_id_tipo_repuesto_movimiento;
	private $_id_usuario;
	private $_cantidad;
	private $_fecha;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id_repuesto" :
        		$this->_id_repuesto = $value;
        		break;
        	case "id_tipo_repuesto_movimiento" :
        		$this->_id_tipo_repuesto_movimiento = $value;
        		break;
        	case "id_usuario" :
        		$this->_id_usuario = $value;
        		break;
        	case "cantidad" :
        		$this->_cantidad = $value;
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
        	case "id_repuesto" :
        		return $this->_id_repuesto;
        	case "id_tipo_repuesto_movimiento" :
        		return $this->_id_tipo_repuesto_movimiento;
        	case "id_usuario" :
        		return $this->_id_usuario;
        	case "cantidad" :
        		return $this->_cantidad;
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
	
	/*
	public static function seek($p_param) {
		global $fc;
		
		$str_sql =
			"  SELECT r.id_repuesto as id, p.descripcion as plataforma, f.descripcion as fabricante, m.descripcion as modelo, re.descripcion as radio_estacion, c.descripcion as ciudad, rg.descripcion as region" .
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
	
	public static function getById($p_db, $p_id) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_repuesto_movimiento AS id, id_repuesto_FK, id_tipo_repuesto_movimiento_FK, id_usuario_FK, cantidad, DATE_FORMAT(fecha, '%d-%b-%Y') AS fecha" .
		 	"  FROM repuesto_movimiento" .
			"  WHERE id_repuesto_movimiento = $p_id";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new RepuestoMovimiento();
			
			$ret->_id = $ar[0]['id'];
			$ret->_id_repuesto = $ar[0]['id_repuesto_FK'];
			$ret->_id_tipo_repuesto_movimiento = $ar[0]['id_tipo_repuesto_movimiento_FK'];
			$ret->_id_usuario = $ar[0]['id_usuario_FK'];
			$ret->_cantidad = $ar[0]['cantidad'];
			$ret->_fecha = $ar[0]['fecha'];
		}
		else if ($p_db->RowCount() != 0) {
			throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
		
		return $ret;
		
	}
	
	public function update($p_db) {
		
		$str_sql =
			"  UPDATE repuesto_movimiento" .
			"  SET id_usuario_FK = {$this->_id_usuario}," .
			"  cantidad = {$this->_cantidad}," .
			"  fecha = {$this->_fecha}," .
			"  WHERE id_repuesto_movimiento = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO repuesto_movimiento" .
			"  (" .
			"  id_repuesto_FK," .
			"  id_tipo_repuesto_movimiento_FK," .
			"  id_usuario_FK," .
			"  cantidad," .
			"  fecha" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  {$this->_id_repuesto}," .
			"  {$this->_id_tipo_repuesto_movimiento}," .
			"  {$this->_id_usuario}," .
			"  {$this->_cantidad}," .
			"  {$this->_fecha}" .
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
			
			$str_sql =
				"  SELECT u.nombre_usuario, DATE_FORMAT(rm.fecha, '%d-%b-%Y') AS fecha, f.descripcion AS fabricante, m.descripcion AS modelo, p.descripcion AS plataforma, rg.descripcion AS region, c.descripcion AS ciudad, re.descripcion AS radio_estacion, trm.descripcion AS tipo, rm.id_repuesto_movimiento AS id, rm.id_repuesto_FK AS id_repuesto, rm.id_tipo_repuesto_movimiento_FK AS id_tipo_repuesto_movimiento, rm.cantidad AS cantidad" .
			 	"  FROM repuesto_movimiento rm" .
				"  JOIN tipo_repuesto_movimiento trm ON trm.id_tipo_repuesto_movimiento = rm.id_tipo_repuesto_movimiento_FK" .
				"  LEFT JOIN usuario u ON u.id_usuario = rm.id_usuario_FK" .
				"  JOIN repuesto r ON r.id_repuesto = rm.id_repuesto_FK" .
				"  JOIN modelo m ON m.id_modelo = r.id_modelo_FK" .
				"  JOIN fabricante f ON f.id_fabricante = m.id_fabricante_FK" .
				"  JOIN plataforma p ON p.id_plataforma = r.id_plataforma_FK" .
				"  JOIN radio_estacion re ON re.id_radio_estacion = r.id_radio_estacion_FK" .
				"  JOIN ciudad c ON c.id_ciudad = re.id_ciudad_FK" .
				"  JOIN region rg ON rg.id_region = c.id_region_FK" .
				"  LEFT JOIN motivo_movimiento mm ON mm.id_repuesto_movimiento_FK = rm.id_repuesto_movimiento";
				
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id_plataforma') {
	                $array_clauses[] = "p.id_plataforma = $value";
	            }
	            else if ($key == 'id_region') {
	                $array_clauses[] = "rg.id_region = $value";
	            }
	            else if ($key == 'id_ciudad') {
	                $array_clauses[] = "c.id_ciudad = $value";
	            }
	            else if ($key == 'id_radio_estacion') {
	                $array_clauses[] = "re.id_radio_estacion = $value";
	            }
	            else if ($key == 'id_fabricante') {
	                $array_clauses[] = "f.id_fabricante = $value";
	            }
	            else if ($key == 'id_modelo') {
	                $array_clauses[] = "u.id_modelo_FK_FK = $value";
	            }
	            else if ($key == 'id_motivo') {
	                $array_clauses[] = "mm.id_motivo_FK = $value";
	            }
	            else if ($key == 'n_motivo') {
	                $array_clauses[] = "mm.valor = $value";
	            }
	            else if ($key == 'id_usuario') {
	                $array_clauses[] = "u.id_usuario = $value";
	            }
	            else if ($key == 'fecha_desde') {
	                $array_clauses[] = "rm.fecha >= '$value'";
	            }
	            else if ($key == 'fecha_hasta') {
	                $array_clauses[] = "rm.fecha <= '$value'";
	            }
	            else if ($key == 'id_tipo') {
	                $array_clauses[] = "trm.id_tipo_repuesto_movimiento = $value";
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