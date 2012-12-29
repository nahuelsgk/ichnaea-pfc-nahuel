<?php

class DBi extends MySQL{
  public function __construct(){
    parent::__construct(true, 'ichnaea', 'localhost', 'root', 'qwerasdf', 'utf8');
  }
}
?>
