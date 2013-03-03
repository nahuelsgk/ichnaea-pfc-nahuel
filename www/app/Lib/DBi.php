<?php

class DBi extends MySQL{
  public function __construct(){
    parent::__construct(true, 'ichnaea', 'localhost', 'root', 'qwerasdf', 'utf8');
  }

  /*
  * Temporaly Insert. Must be more generic.
  * - $tablename
  * - $fields: associative array ["name_of_column"] => "type_of_colum"
  * - $values: array_of_arrays: as follows
  *   [
  *     [column.0.0, column.0.1,...],
  *     [column.1.0, column.0.2,...]
  *   ]
  * Last update: 3 March 2013
  */
  public function BuildSQLInsertMassive($table_name, $fields, $values){

    $insert = "INSERT INTO $table_name (";
    foreach ($fields as $k=>$v){
      $columns[] = $k;
    }
    $insert .= implode(' , ', $columns);
    
    $insert .= ') VALUES ';

    // Keys by numeric order
    $keys = array_keys($fields);

    foreach($values as $row_j){
      //$j = 0;
      $i = 0;
      $record_values = array();
      foreach($row_j as $column_i){
        //printHTML("Columna $j row $i: $column_i del tipo ".$fields[$keys[$i]]);
        $record_values [] = DBi::SQLValue($column_i, $fields[$keys[$i]]) ; 
	$i++;
      }
      $records [] = '('.implode(',', $record_values).')';
      //$j++;
    }
    $insert .= implode(',', $records);
    return $insert;
  }
}
?>
