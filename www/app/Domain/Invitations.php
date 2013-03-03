<?php 

includeLib("Domain/Matrix");

class Invitation{

  #Fields Containing the columns for the database
  private static $FIELDS = array (
    'created' => DBi::SQLVALUE_DATETIME);
  private static $TABLE = '';

  public function __construct(array $attributes)
  {
    $this->name = DBi::SQLValue($attributes['name']);
    $this->user_owner = DBi::SQLValue($attributes['user_owner'], DBi::SQLVALUE_NUMBER);
  }
}
?>
