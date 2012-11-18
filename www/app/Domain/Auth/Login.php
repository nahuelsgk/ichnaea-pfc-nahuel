<?php

class Login
{
  
  public function checkLogin($email, $password)
  {
    $db = new MySQL(); 
    $sql = "SELECT * FROM users WHERE login='".$email."' AND passwd='".$password."'";
    $db->Query($sql); 
    if ($db->RowCount() > 0) return true;
    return false;
  }
}
?>
