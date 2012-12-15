<?php

/*
*  Return an associative array with the list of the users
*/
function getUsersList(){
  $db = new MySQL();
  $sql = "SELECT id,name,login FROM users";
  $ret = $db->QueryArray($sql,MYSQL_BOTH);
  return $ret;
}
?>
