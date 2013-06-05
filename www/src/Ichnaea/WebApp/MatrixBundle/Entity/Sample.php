<?php

namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matrix's sample. It's a row of the matrix
 *
 * @ORM\Table(name="sample")
 * @ORM\Entity
 */
class Sample
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