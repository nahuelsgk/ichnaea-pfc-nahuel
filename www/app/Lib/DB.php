<?php

class DBi{
  private $user="root";
  private $password="qwerasdf";
  private $database="ichnaea";
  private $host='localhost';
 
  
  public function __construct(){}


  public function getSimpleQuery($query){
    $con = mysql_connect($this->host,$this->user,$this->password) or die('Error en la conexion');
    mysql_select_db($this->database) or die( "Unable to select database");
    $resultado = mysql_query($query);
    if (!$resultado) {
      printHTML('No se pudo ejecutar la consulta: ' . mysql_error());
      exit;
    }
    else{
      $fila = mysql_fetch_row($resultado);
      printHTML($fila[0]); // 42
      printHTML($fila[1]); // el valor de email
    }
    mysql_close();
    return $resultado;
  }
 
  public function getSimpleValue($query){
   
  }
   
  private function connectDB{}
  /*
  TODO:
  a) Un array de hashes.
  b) Simple row como una hash.
  */
  
}

?>
