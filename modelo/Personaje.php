<?php

include_once ("Modelo.php");
include_once ("PersonajeCaracteristica.php");
include_once ("PersonajeRelacion.php");
include_once ("PersonajeImagen.php");

/* *******************************************************************************************
 
 * CLASE Personaje

 * ***************************************************************************************** */

class Personaje extends Modelo  {

	private $id;
	private $nombre;
	private $nombre_largo;
	private $sexo;
	private $anyo;
	private $edad;
	private $numero_imagen;

	private $arrCaracteristicas;
	private $arrRelaciones;
	private $arrImagenes;



	/* *******************************************************************************************
	 * CONSTRUCTOR
	 * ***************************************************************************************** */

	function __construct() {
		parent::__construct();

		switch (func_num_args()) { 
			case 1: { $this->cargar(func_get_arg(0)); } // si hay un parámetro en el constructor es el id
		}
	}


	/* *******************************************************************************************
	 * METODOS PRIVADOS
	 * ***************************************************************************************** */	
	
	private function cargar($id) {

		// cadena sql para realizar la carga
		$sql = "select * from personaje where id=".$id;

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

		// obteniendo los valores
		$arrCampos = pg_fetch_Array($query);

		$this->id = $arrCampos['id'];
		$this->nombre = $arrCampos['nombre'];
		$this->nombre_largo = $arrCampos['nombre_largo'];
		$this->sexo = $arrCampos['sexo'];
		$this->anyo = $arrCampos['anyo'];
		$this->edad = $this->calcularEdad();
		$this->numero_imagen = $arrCampos['numero_imagen'];

		// si no se ha podido cargar es que no existe
		if (!$this->id) throw new Exception('Personaje::cargar: Personaje no existe');
	}


	private function calcularEdad() {

		// se obtiene el año de nacimiento
		$anyo = new DateTime($this->anyo);

		// se instancia la fecha actual
		//$hoy = date('Y-m-d');
		$hoy = new DateTime();

		// se obtiene la diferencia entre fechas
		$diferencia = $anyo->diff($hoy);

		// se obtiene la edad
		$edad = $diferencia->y;

		// si no tiene edad se indica a null
		$this->edad = ($edad) ? $edad : '';

	}


	private function crearFiltroBuscarPersonajesSeleccionables($nombreLargo) {

		// se inicializa la cadena que contendrá el filtro
		$sql = "";

		// si hay algún filtro añadimos el "where" y los filtros correspondientes
		if (!empty($nombreLargo)) {
			$sql = " where nombre_largo like '%".$nombreLargo."%' ";
		}

		// se devuelve el filtro
		return $sql;		
	}


		
	/* *******************************************************************************************
	 * METODOS PUBLICOS
	 * ***************************************************************************************** */	

	public function insertar($nombre, $nombre_largo, $sexo, $anyo) {

		// si la fecha no es válida no se puede insertar
		$arrFecha = explode('-', $anyo);

 		$fechaValida = checkdate( $arrFecha[1] , $arrFecha[2] , $arrFecha[0] ); // mes, día, año
		if (!$fechaValida) throw new Exception('Personaje::insertar: Fecha no válida: '.$anyo);

		// cadena sql para realizar la inserción
		$sql = "insert into personaje (nombre, nombre_largo, sexo, anyo, numero_imagen) ".
			"values ('".$nombre."', '".$nombre_largo."', '".$sexo."', '".$anyo."', 0) ".
			"Returning id";

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

		// obteniendo el id del personaje
		$row = pg_fetch_row($query);
		$this->id = $row[0];

		// cargando el resto de atributos
		$this->nombre = $nombre;
		$this->nombre_largo = $nombre_largo;
		$this->sexo = $sexo;
		$this->anyo = $anyo;
		$this->edad = $this->calcularEdad();

		return $this->id;

	}	


	public function actualizar($nombre, $nombre_largo, $sexo, $anyo) {

		// si no está cargada la relación, no se puede actualizar
		if (!$this->id) throw new Exception('Personaje::actualizar: Personaje no cargado');

		// cadena sql para realizar la carga
		$sql = "update personaje set ".
			"nombre = '".$nombre."', ".
			"nombre_largo = '".$nombre_largo."', ".
			"sexo = '".$sexo."', ".
			"anyo = '".$anyo."' ".
			"where id = ".$this->id;

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

	}	


	public function eliminar() {

		// si no está cargada la característica, no se puede actualizar
		if (!$this->id) throw new Exception('Personaje::eliminar: Personaje no cargado');

		// cadena sql para realizar la carga
		$sql = "delete from personaje where id = ".$this->id;

		// ejecución de la consulta
		$query = pg_exec($this->conn, $sql);

	}		

	

	/* Métodos getters y setters */

	public function getId() {
		return $this->id;
	}

	public function getNombre() {
		return $this->nombre;
	}

	public function getNombreLargo() {
		return $this->nombre_largo;
	}

	public function getSexo() {
		return $this->sexo;
	}

	public function getAnyo() {
		return $this->anyo;
	}

	public function getEdad() {
		return $this->edad;	
	}

	public function getNumeroImagen() {
		return $this->numero_imagen;
	}

	public function setId($id) {
		$this->id = $id;
	}	

	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}	

	public function setNombreLargo($nombre_largo) {
		$this->nombre_largo = $nombre_largo;
	}	

	public function setSexo($sexo) {
		$this->sexo = $sexo;
	}	

	public function setAnyo($anyo) {
		$this->anyo = $anyo;
	}	

	public function setEdad() {
		$this->calcularEdad();
	}	

	public function setNumeroImagen($numero_imagen) {
		$this->numero_imagen = $numero_imagen;
	}	

}

?>