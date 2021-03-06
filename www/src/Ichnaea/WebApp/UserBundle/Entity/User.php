<?php

namespace Ichnaea\WebApp\UserBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\MatrixBundle\Entity\Matrix", mappedBy="owner")  
     */
    protected $matrixs;
    
    /**
     * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix", mappedBy="owner")
     */
    protected $predictions;
    
    /**
     * @ORM\OneToMany(targetEntity="Ichnaea\WebApp\TrainingBundle\Entity\Training", mappedBy="trainer")  
     */
    protected $trainings;
    
    /**
     * @ORM\ManyToMany(targetEntity="Ichnaea\WebApp\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add matrixs
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrixs
     * @return User
     */
    public function addMatrix(\Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrixs)
    {
        $this->matrixs[] = $matrixs;

        return $this;
    }

    /**
     * Remove matrixs
     *
     * @param \Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrixs
     */
    public function removeMatrix(\Ichnaea\WebApp\MatrixBundle\Entity\Matrix $matrixs)
    {
        $this->matrixs->removeElement($matrixs);
    }

    /**
     * Get matrixs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMatrixs()
    {
        return $this->matrixs;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add trainings
     *
     * @param \Ichnaea\WebApp\TrainingBundle\Entity\Training $trainings
     * @return User
     */
    public function addTraining(\Ichnaea\WebApp\TrainingBundle\Entity\Training $trainings)
    {
        $this->trainings[] = $trainings;

        return $this;
    }

    /**
     * Remove trainings
     *
     * @param \Ichnaea\WebApp\TrainingBundle\Entity\Training $trainings
     */
    public function removeTraining(\Ichnaea\WebApp\TrainingBundle\Entity\Training $trainings)
    {
        $this->trainings->removeElement($trainings);
    }

    /**
     * Get trainings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainings()
    {
        return $this->trainings;
    }

    /**
     * Add predictions
     *
     * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $predictions
     * @return User
     */
    public function addPrediction(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $predictions)
    {
        $this->predictions[] = $predictions;

        return $this;
    }

    /**
     * Remove predictions
     *
     * @param \Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $predictions
     */
    public function removePrediction(\Ichnaea\WebApp\PredictionBundle\Entity\PredictionMatrix $predictions)
    {
        $this->predictions->removeElement($predictions);
    }

    /**
     * Get predictions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPredictions()
    {
        return $this->predictions;
    }

}
