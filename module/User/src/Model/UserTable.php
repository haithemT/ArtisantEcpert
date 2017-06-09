<?php
namespace User\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;

class UserTable
{
    private $tableGateway;

    public $id;
    public $username;
    public $firstname;
    public $lastname;
    public $email;
    public $enabled;
    public $password;
    public $last_login;
    public $locked;
    public $roles;
    public $expired;
    public $expires_at;
    public $confirmation_token;
    public $password_requested_at;
    public $credentials_expired;
    public $credentials_expire_at;
    public $ip;
    public $subscription_date;
    public $facebook_id;
    public $linkedin_id;
    public $avatar_path;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }
    public function saveUser(User $user)
    {
        $data = [
            'username'              => $user->username,
            'firstname'             => $user->firstname,
            'lastname'              => $user->lastname,
            'email'                 => $user->email,
            'enabled'               => ($user->enabled==null) ? 0:$user->enabled,
            'password'              => $user->password,
            'last_login'            => new Expression('NOW()'),
            'locked'                => ($user->locked==null) ? 0:$user->locked,
            'expired'               => ($user->expired==null) ? 0:$user->expired,
            'expires_at'            => $user->expires_at,
            'confirmation_token'    => $user->confirmation_token,
            'password_requested_at' => $user->password_requested_at,
            'credentials_expired'   => ($user->credentials_expired==null) ? 0:$user->credentials_expired,
            'credentials_expire_at' => $user->credentials_expire_at,
            'ip'                    => $user->ip,
            'subscription_date'     => new Expression('NOW()'),
            'facebook_id'           => $user->facebook_id,
            'linkedin_id'           => $user->linkedin_id,
            'avatar_path'           => $user->avatar_path,
            //'roles'                 => (!isset($user->roles) || $user->roles==null) ? 'user':$user->roles,
        ];
        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getUser($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function checkUserExists($email)
    {
        $rowset = $this->tableGateway->select(['email' => $email]);
        $row = $rowset->current();
        
        if (! $row) {
            return 0;
        }
        return 1;
    }
    
     
    public function getUserByToken($token)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['confirmation_token' => $token]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }
        return $row;
    }
    
    public static function verifyHashedPassword(User $user, $passwordGiven)
    {
        $bcrypt = new Bcrypt(array('cost' => 10));
        return $bcrypt->verify($passwordGiven, $user->password);
    }
    
    public static function encryptPassword($password)
    {
        $bcrypt = new Bcrypt(array('cost' => 10));
        return $bcrypt->create($password);
    }
    
    public function generateToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->confirmation_token=$token;

        $user->expires_at=(new DateTime('+1 day'))->format('Y-m-d H:i:s');
        
        $this->tableGateway->saveUser($user);

        $subject = 'Password Reset';

        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password/' . $token;

        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";

        // Send email to user.
       // mail($user->email, $subject, $body);
    }
    
    public function validateToken($token)
    {
        $user = $this->tableGateway->getUserByToken($token);

        if($user==null) {
            return false;
        }
        $currentDate = strtotime('now');

        if ($currentDate > $tokenCreationDate) {
            return false; // expired
        }

        return true;
    }

}