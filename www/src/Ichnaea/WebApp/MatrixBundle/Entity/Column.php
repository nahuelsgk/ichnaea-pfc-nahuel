<?php

namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Column
{
	/**
     * @var integer
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
}
?>