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

  /*
  * Temporaly Update Generic Method
  * - type:
  *   - "execute": executes the query and throws an exception
  *   - "query": returns the query as string
  * - tableName
  * - fields: ["name_column" => type_of_column]
  * - values: ["name_column" => new_value]
  * - where:  ["name_column" => where_value]
  * 
  * Last update: 5 march 2013
  */
  public function BuildSQLUpdateGeneric($mode="query", $tableName, $fields, $valuesArray, $whereArray = null){
    $values_generic = array();
    foreach($valuesArray as $k=>$v){
      $values_generic[$k] = DBi::SQLValue($v, $fields[$k]);
    }

    $where_generic = array();
    foreach($whereArray as $k => $v){
      $where_generic[$k] = DBi::SQLValue($v, $fields[$k]);
    }

    $update = $this->BuildSQLUpdate($tableName, $values_generic, $where_generic);

    switch ($mode){
      case "execute":
        if ( ($result = $this->Query($update)) == FALSE ) throw new Exception($db->Error);
        return $result;
        break;
      case "query":
        return $update;
        break;
	
      case "default":
       return $update;
       break;
    }
    return;
  }

  /*
  * Builds or execute a more generic select
  * Extra fields:
  * - $mode: 
  *   - "query": returns the query built
  *   - "execute": execute the query and throws an Exception
  *   - "query_debug": prints the query and return it
  *   - "execute_local_catch" execute the query and the Exception doesn't propagate
  * Last update: 13 march 2013
  */
  public function BuildSQLSelectGeneric($mode = "query", $table_name, $fields, $whereArray = null, $columns = null, $sortColumns = null, $sortAscending = true, $limit = null){
    
    $generic_where = array();
    foreach ($whereArray as $k => $v){
      $generic_where[$k] =  DBi::SQLValue($v, $fields[$k]);
    }

    $sql = $this->BuildSQLSelect(
      $table_name,
      $generic_where,
      $columns,
      $sortColumns,
      $sortAscending,
      $limit
    ); 
    switch ($mode){
      case "execute":
        printHTML($sql);
        if ( ($result = $this->QueryArray($sql, MYSQL_ASSOC)) == FALSE ) throw new Exception($this->Error());
	return $result;
	break;

      case "execute_local_catch":
         try{
           if ( ($result = $this->QueryArray($sql, MYSQL_ASSOC)) == FALSE ) throw new Exception($this->Error());
	   return $result;
	   break;
	 }
	 catch(Exception $e){
	   printHTML($e->Error());
	 }

      case "query":
        return $sql;
        break;
 
      case "query_debug":
        printHTML($sql);
	return $sql;
	break;

      case "default":
        return $sql;
	break;
    }
  }
  
  /*
  * A generic builder for queries.
  * - the sql needs to follow the sprintf format for give format to query
  * - $format_array: collection of values. Accepts an array for build multiple select. Exemple:
  *   [2, [a,b,c]] => the second element will transform it into '('a','b','c')' for queries type NOT IN ('a','b','c'). 
  *   In that cases, needs a format as string will be formated as string
  * - $type:
  *   - "execute": executes the query
  *   - "query" : returns the string
  *
  * Last update: 4 march 2013
  */
  public function QuerySimple($sql, $format_array, $type = "execute"){
    $values = array();

    if(!empty($format_array)){
      foreach($format_array as $format){
        if (is_array($format) === TRUE ){
          $values [] = '('. implode(',', $format) . ')';
        }
        else $values [] = $format;
      }
    }
    $query = vsprintf($sql, $values);
    if ($type == "execute"){
      if($db->Query($query) === FALSE) throw new Exception($db->Error());
    }
    if ($type == "query"){ 
      return $query;
    }
    return;
  }
}
?>
