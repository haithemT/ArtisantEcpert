<?php
namespace User\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable
{
    private $tableGateway;

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
    public function saveUser(User $user)
    {
        $data = [
            'username'              => $user->username,
            'firstname'             => $user->firstname,
            'username'              => $user->username,
            'lastname'              => $user->lastname,
            'email'                 => $user->email,
            'enabled'               => ($user->enabled==null) ? 0:$user->enabled,
            'password'              => $user->password,
            'last_login'            => $user->last_login,
            'locked'                => ($user->locked==null) ? 0:$user->locked,
            'expired'               => ($user->expired==null) ? 0:$user->expired,
            'expires_at'            => $user->expires_at,
            'confirmation_token'    => $user->confirmation_token,
            'password_requested_at' => $user->password_requested_at,
            'credentials_expired'   => ($user->credentials_expired==null) ? 0:$user->credentials_expired,
            'credentials_expire_at' => $user->credentials_expire_at,
            'ip'                    => $user->ip,
            'subscription_date'     => $user->subscription_date,
            'facebook_id'           => $user->facebook_id,
            'linkedin_id'           => $user->linkedin_id,
            'avatar_path'           => $user->avatar_path,
            'roles'                 => ($user->roles==null) ? 'user':$user->roles,
        ];

        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
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
}