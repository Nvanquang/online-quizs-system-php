<?php


class UserRepository extends BaseRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * @return User  // Thêm return type hint để match abstract
     */
    protected function getModelInstance(): User  // Fix: Thêm return statement
    {
        $this->model = new User();
        return $this->model;  // Đây là fix chính: return instance
    }

    // Chỉ giữ các method custom
    public function findByEmail(string $email)
    {
        return $this->model->findOne(['email' => $email]);
    }

    public function findByUsername(string $username)
    {
        return $this->model->findOne(['username' => $username]);
    }

    /**
     * Find user by username or email
     */
    // public function findByUsernameOrEmail(string $identifier)
    // {
    //     // Try username first
    //     $user = $this->findByUsername($identifier);
        
    //     if (!$user) {
    //         // Try email
    //         $user = $this->findByEmail($identifier);
    //     }
        
    //     return $user;
    // }
}