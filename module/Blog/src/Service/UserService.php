<?php
namespace User\Service;
 
use Zend\Crypt\Password\Bcrypt;
use User\Model\User;
 
class UserService
{
   /**
    * verify user password
    * @param User $user
    * @param string $passwordGiven
    * @return hash
    */
    public static function verifyHashedPassword(User $user, $passwordGiven)
    {
        $bcrypt = new Bcrypt(array('cost' => 10));
        return $bcrypt->verify($passwordGiven, $user->password);
    }
    
    /**
     * Encrypt Password
     *
     * Creates a Bcrypt password hash
     *
     * @return String
     */
    public static function encryptPassword($password)
    {
        $bcrypt = new Bcrypt(array('cost' => 10));
        return $bcrypt->create($password);
    }
}