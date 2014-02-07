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
	 * @ORM\ManyToOne(targetEntity="Ichnaea\WebApp\TrainingBundle\Entity\Training", inversedBy="predictionMatrix")
	 */
	private $training;
	
	/**
	 * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\Sample", mappedBy="matrix", cascade={"persist"})
	 */
	private $rows;
	
}
?>
