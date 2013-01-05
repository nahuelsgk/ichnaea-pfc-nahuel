<?php 

class Vars{
  private static $TABLE = 'var';
  private static $FIELDS = array ('id', 'matrix_id', 'name', 'created');

  public function __construct(){
  
  }

  public function saveMatrixVars($pid,$vars){
    $db = new Dbi();

    $st = "INSERT INTO ".self::$TABLE."(matrix_id,name) VALUES ";
    foreach ($vars as $var){
      $values[] = '(' . DBi::SQLValue($pid, DBi::SQLVALUE_NUMBER) . ',' . Dbi::SQLValue($var) . ')';
    }
    $st.=implode(',', $values);
    $db->Query($st);
  }
}
?>
