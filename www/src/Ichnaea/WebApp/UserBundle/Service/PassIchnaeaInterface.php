<?php
namespace Ichnaea\WebApp\UserBundle\Service;
          
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PassIchnaeaInterface implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt = null)
    {
        return md5($raw."ipfc"); // Custom function for encrypt
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw);
    }
}
?>