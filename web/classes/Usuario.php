<?php

include_once('mysql.class.php');
include_once('Acceso.php');

class Usuario
{
	private $_id;
	private $_nombre_usuario;
	private $_contrasena;
	private $_nombre;
	private $_apellidos;
	private $_email;
	private $_activo;
	private $_borrado;
	
	public function __construct() {
		
	}
	
    public function __set($name, $value)
    {
        //echo "Setting '$name' to '$value'\n";
        switch ($name) {
        	case "id" :
        		$this->_id = $value;
        		break;
        	case "nombre_usuario" :
        		$this->_nombre_usuario = $value;
        		break;
        	case "contrasena" :
        		$this->_contrasena = $value;
        		break;
        	case "nombre" :
        		$this->_nombre = $value;
        		break;
        	case "apellidos" :
        		$this->_apellidos = $value;
        		break;
        	case "email" :
        		$this->_email = $value;
        		break;
        	case "activo" :
        		$this->_activo = $value;
        		break;
        	case "borrado" :
        		$this->_borrado = $value;
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
        	case "nombre_usuario" :
        		return $this->_nombre_usuario;
        	case "contrasena" :
        		return $this->_contrasena;
        	case "nombre" :
        		return $this->_nombre;
        	case "apellidos" :
        		return $this->_apellidos;
        	case "email" :
        		return $this->_email;
        	case "activo" :
        		return $this->_activo;
        	case "borrado" :
        		return $this->_borrado;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
    
	public static function fromArray($p_ar) {
		$ret = new Usuario();
		
		$ret->_id = $p_ar[0]['id_usuario'];
		$ret->_nombre_usuario = $p_ar[0]['nombre_usuario'];
		$ret->_contrasena = $p_ar[0]['contrasena'];
		$ret->_nombre = $p_ar[0]['nombre'];
		$ret->_apellidos = $p_ar[0]['apellidos'];
		$ret->_email = $p_ar[0]['email'];
		$ret->_activo = $p_ar[0]['activo'];
		$ret->_borrado = $p_ar[0]['borrado'];
				
		return $ret;
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
	public static function getByUsername($p_db, $p_username) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_usuario, nombre_usuario, contrasena, nombre, apellidos, email, 0+activo AS activo, 0+borrado AS borrado" .
		 	"  FROM usuario u" .
			"  WHERE u.nombre_usuario = '$p_username'";
		
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
	
	public static function getByEmail($p_db, $p_email) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_usuario, nombre_usuario, contrasena, nombre, apellidos, email, 0+activo AS activo, 0+borrado AS borrado" .
		 	"  FROM usuario u" .
			"  WHERE u.email = '$p_email'";
		
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
	
	public static function getByID($p_db, $p_id) {
		$ret = null;
		
		$str_sql =
			"  SELECT id_usuario, nombre_usuario, contrasena, nombre, apellidos, email, 0+activo AS activo, 0+borrado AS borrado" .
		 	"  FROM usuario u" .
			"  WHERE u.id_usuario = '$p_id'";
		
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
	
	public function tieneAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// usuario 'admin' tienen todos los privilegios
		if ($this->_nombre_usuario == 'admin') {
			$ret = true;
		}
		else {
			$str_sql =
				"  SELECT ua.*" .
			 	"  FROM usuario_acceso ua" .
				"  JOIN usuario u ON u.id_usuario = ua.id_usuario_FK" .
				"  JOIN acceso a ON a.id_acceso = ua.id_acceso_FK" .
				"  WHERE a.descripcion = '$p_acceso'" .
				"  AND u.id_usuario = {$this->_id}";
			
			//echo '<br>' . $str_sql . '<br>';
			
			if ($p_db->Query($str_sql) !== false) {
				if ($p_db->RowCount() != 0) {
					$ret = true;	
				}
	 		}
			else {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
	public function revocaAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// al usuario 'admin' no se le pueden quitar privilegios
		if ($this->_nombre_usuario != 'admin') {
			
			if ($this->tieneAcceso($p_db, $p_acceso)) {
				
				// debo revocar el acceso
				$acceso = Acceso::getByDescripcion($p_db, $p_acceso);
				
				$id_acceso = $acceso->id;
				
				$str_sql =
					"  DELETE" .
				 	"  FROM usuario_acceso" .
					"  WHERE id_usuario_FK = {$this->_id}" .
					"  AND id_acceso_FK = $id_acceso";
				
				//echo '<br>' . $str_sql . '<br>';
				
				if ($p_db->Query($str_sql) === false) {
					throw new Exception('Error al revocar acceso: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
				}
				
				$ret = true;
			}
		}
		
		return $ret;
	}
	
	public function otorgaAcceso($p_db, $p_acceso) {
		$ret = false;
		
		// al usuario 'admin' no se le pueden otorgar privilegios, por definicion los tiene todos
		if ($this->_nombre_usuario != 'admin') {
			
			if (!$this->tieneAcceso($p_db, $p_acceso)) {
				
				// debo otorgar el acceso
				$acceso = Acceso::getByDescripcion($p_db, $p_acceso);
				
				$id_acceso = $acceso->id;
				
				$str_sql =
					"  INSERT" .
				 	"  INTO usuario_acceso" .
					"  (" .
					"  id_usuario_FK," .
					"  id_acceso_FK" .
					"  )" .
					"  VALUES" .
					"  (" .
					"  {$this->_id}," .
					"  $id_acceso" .
					"  )";
				
				//echo '<br>' . $str_sql . '<br>';
				
				if ($p_db->Query($str_sql) === false) {
					throw new Exception('Error al otorgar acceso: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
				}
				
				$ret = true;
			}
		}
		
		return $ret;
	}
	
	public static function seekSpecial($p_db, $p_param) {
		
		$str_sql =
			"  SELECT id_usuario AS id, nombre_usuario, contrasena, nombre, apellidos, email, 0+activo AS activo, 0+borrado AS borrado" .
		 	"  FROM usuario u" .
			"  WHERE (u.nombre LIKE '%$p_param%'" .
			"  OR u.apellidos LIKE '%$p_param%'" .
			"  OR u.nombre_usuario LIKE '%$p_param%')" .
			"  AND u.borrado = b'0'";
		
		//echo '<br>' . $str_sql . '<br>';
		
		$ret = $p_db->QueryArray($str_sql, MYSQL_ASSOC);
		
		if (!is_array($ret)) {

			if ($p_db->RowCount() != 0) {
				throw new Exception('Error al obtener registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
			}
		}
		
		return $ret;
		
	}
	
    public static function seek($db, $parameters, $order, $direction, $offset, $limit) {
		
		try {
			$array_clauses = array();
			
			$str_sql =
			"  SELECT u.id_usuario AS id, u.nombre_usuario, u.contrasena, u.nombre, u.apellidos, u.email, 0+u.activo AS activo, 0+u.borrado AS borrado" .
		 	"  FROM usuario u";
								
	        foreach($parameters as $key => $value) {
	    		if ($key == 'id') {
	                $array_clauses[] = "u.id_repuesto = $value";
	            }
	            else if ($key == 'borrado') {
	                $array_clauses[] = "r.borrado = b'1'";
	            }
	            else if ($key == 'no borrado') {
	                $array_clauses[] = "r.borrado = b'0'";
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
	
	public function update($p_db) {

		$str_sql =
			"  UPDATE usuario" .
			"  SET nombre_usuario = " . (isset($this->_nombre_usuario) ? "'{$this->_nombre_usuario}'" : 'null') . ',' .
			"  contrasena = " . (isset($this->_contrasena) ? "'{$this->_contrasena}'" : 'null') . ',' .
			"  nombre = " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  apellidos = " . (isset($this->_apellidos) ? "'{$this->_apellidos}'" : 'null') . ',' .
			"  email = " . (isset($this->_email) ? "'{$this->_email}'" : 'null') . ',' .
			"  activo = " . (isset($this->_activo) ? "b'{$this->_activo}'" : 'null') . ',' .
			"  borrado = " . (isset($this->_borrado) ? "b'{$this->_borrado}'" : 'null') .
			"  WHERE id_usuario = {$this->_id}";
		
		//echo '<br>' . $str_sql . '<br>';
		
		if ($p_db->Query($str_sql) === false) {
			throw new Exception('Error al actualizar registro: ' . $p_db->Error(), $p_db->ErrorNumber(), null);
		}
	}

	public function insert($p_db) {
		
		$str_sql =
			"  INSERT INTO usuario" .
			"  (" .
			"  nombre_usuario," .
			"  contrasena," .
			"  nombre," .
			"  apellidos," .
			"  email," .
			"  activo" .
			"  )" .
			"  VALUES" .
			"  (" .
			"  " . (isset($this->_nombre_usuario) ? "'{$this->_nombre_usuario}'" : 'null') . ',' .
			"  " . (isset($this->_contrasena) ? "'{$this->_contrasena}'" : 'null') . ',' .
			"  " . (isset($this->_nombre) ? "'{$this->_nombre}'" : 'null') . ',' .
			"  " . (isset($this->_apellidos) ? "'{$this->_apellidos}'" : 'null') . ',' .
			"  " . (isset($this->_email) ? "'{$this->_email}'" : 'null') . ',' .
			"  " . (isset($this->_activo) ? "b'{$this->_activo}'" : 'null') .
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