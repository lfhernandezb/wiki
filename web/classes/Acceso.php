<?php

include_once('mysql.class.php');

class Acceso
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
	
	public static function getByDescripcion($p_db, $p_descripcion) {
		
		$ret = null;
		
		$str_sql =
			"  SELECT id_acceso AS id, descripcion" .
		 	"  FROM acceso" .
			"  WHERE descripcion = '$p_descripcion'";
		
		//echo '<br>' . $str_sql . '<br>';
				
		$ar = $p_db->QueryArray($str_sql, MYSQL_ASSOC); 
		
		if (is_array($ar)) {
			$ret = new Acceso();
			
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