<?php

namespace Ichnaea\WebApp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

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
     * 
     */
    protected $matrixs;
    
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
}
