<?php 
namespace Ichnaea\WebApp\PredictionBundle\Entity;

/**
 * Matrix
 *
 * @ORM\Table(name="prediction_matrix")
 * @ORM\Entity
 */

class PredictionMatrix
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;
	
	/**
	 * @ORM\ManyToOne
	 */
	private $training;
}
?>
