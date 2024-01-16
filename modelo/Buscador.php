<?php

include_once ("Modelo.php");
include_once ("Personaje.php");



/* *******************************************************************************************
 * CLASE Buscador
 * ***************************************************************************************** */

class Buscador extends Modelo  {

	private $arrCaracteristicas; // array de características
	private $arrPersonajes; // array de personajes
	private $arrRelaciones; // array de relaciones
	private $arrGuiones; // array de guiones
	private $arrRelatos; // array de guiones
	private $arrPalabrasClave; // array de guiones
	private $arrInstrucciones; // array de guiones



	/* *******************************************************************************************
	 * CONSTRUCTOR
	 * ***************************************************************************************** */

	function __construct() {

		parent::__construct();

	}



	/* *******************************************************************************************
	 * METODOS PRIVADOS
	 * ***************************************************************************************** */	
	
	private function crearFiltroBuscarPersonajes($nombre, $nombre_largo, $sexo, $anyo) {

		// se inicializa la cadena que contendrá el filtro
		$sql = "";

		// si hay algún filtro añadimos el "where" y los filtros correspondientes
		if (!empty($nombre) or (!empty($nombre_largo)) or (!empty($sexo)) or (!empty($anyo))) {
			$sql .= " where ";
		
			// se crea el filtro en base a los filtros pasados como parámetros
			if (!empty($nombre)) $sql .= " nombre like '%".$nombre."%' or ";
			if (!empty($nombre_largo)) $sql .= " nombre_largo like '%".$nombre_largo."%' or ";
			if (!empty($sexo)) $sql .= " sexo = '".$sexo."' or ";
			if (!empty($anyo)) $sql .= " anyo < '".$anyo."' or ";

			// se elimina la última partícula disyuntiva (or) de la cadena
			$sql = substr($sql, 0, strlen($sql)-4);
		}

		// se devuelve el filtro
		return $sql;
	}



	private function crearFiltroBuscarInstruciones($operacion, $descripcion) {

		// se inicializa la cadena que contendrá el filtro
		$sql = "";

		// si el filtro de operacion no está vacío
		if (!empty($operacion)) {

			// si el filtro descripción no está vacío...
			if (!empty($descripcion)) {
				$sql = " where operacion like '%".$operacion."%' or descripcion like '%".$descripcion."%' ";
			
			// si el filtro del descripción está vacío...
			} else {
				$sql = " where operacion like '%".$operacion."%' ";
			}

		// si el filtro de operacion está vacío pero descripción no...
		} else if (!empty($descripcion)) {
			$sql = " where descripcion like '%".$descripcion."%' ";
		}

		// se devuelve el filtro
		return $sql;		
	}	



	/* *******************************************************************************************
	 * METODOS PUBLICOS
	 * ***************************************************************************************** */	

	public function numRegistros($tabla, $arrId = null) {

		// cadena sql para realizar la búsqueda de todos los registros que cumplan la condición
		$sql = ($arrId) ? 
			"select count(".$arrId[0].") from ".$tabla." where ".$arrId[0]." = ".$arrId[1] : 
			"select count(id) from ".$tabla;

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

		// obteniendo el primer registro de la base de datos
		$arrRegistro = pg_fetch_array($query);

		// se devuelve la cantidad de registros encontrados
		return $arrRegistro[0];

	}	


	public function buscarPersonajes($nombre = '', $nombre_largo = '', $sexo = '', $anyo = '') {

		// se inicializa el array de personajes
		$this->arrPersonajes = array();

		// se inicia la cadena sql para realizar la búsqueda
		$sql = " select * from personaje ";

		// se incluye los filtros si los hubiere
		$sql .= $this->crearFiltroBuscarPersonajes($nombre, $nombre_largo, $sexo, $anyo);

		// aplicamos la ordenación
		$sql .= " Order by nombre_largo asc ";

		// si no está limitado se pone a false
		if ($this->limitar) $sql .= " limit ".$this->N;

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

		// obteniendo el primer registro de la base de datos
		$arrRegistro = pg_fetch_array($query);

		// recorriendo el apuntador para obtener todos los registros
		while ($arrRegistro) {

			// creando el personaje
			$p = new Personaje();
			$p->setId($arrRegistro['id']);
			$p->setNombre($arrRegistro['nombre']);
			$p->setNombreLargo($arrRegistro['nombre_largo']);
			$p->setSexo($arrRegistro['sexo']);
			$p->setAnyo($arrRegistro['anyo']);
			$p->setEdad();

			// introduciendo la característica en el array de características
			$this->arrPersonajes[] = $p;

			// obteniendo el siguiente registro de la base de datos
			$arrRegistro = pg_fetch_array($query);
		}

		return $this->arrPersonajes;

	}	
	


	/* Métodos getters y setters */

	public function getCaracteristicas() {
		return $this->arrCaracteristicas;
	}

	public function getPersonajes() {
		return $this->arrPersonajes;
	}

	public function getRelaciones() {
		return $this->arrRelaciones;
	}

	public function getGuiones() {
		return $this->arrGuiones;
	}

	public function getRelatos() {
		return $this->arrRelatos;
	}

	public function getPalabrasClave() {
		return $this->arrRelatos;
	}

	public function getInstrucciones() {
		return $this->arrInstrucciones;
	}



	public function setCaracteristicas($arrCaracteristicas) {
		$this->arrCaracteristicas = $arrCaracteristicas;
	}

	public function setPersonajes($arrPersonajes) {
		$this->arrPersonajes = $arrPersonajes;
	}

	public function setRelaciones($arrRelaciones) {
		$this->arrRelaciones = $arrRelaciones;
	}

	public function setGuiones($arrGuiones) {
		$this->arrGuiones = $arrGuiones;
	}

	public function setRelatos($arrRelatos) {
		$this->arrRelatos = $arrRelatos;
	}

	public function setPalabrasClave($arrPalabrasClave) {
		$this->arrPalabrasClave = $arrPalabrasClave;
	}

	public function setInstruciones($arrInstrucciones) {
		$this->arrInstrucciones = $arrInstrucciones;
	}

}

?>