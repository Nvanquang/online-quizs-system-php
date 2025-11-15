<?php


class UserRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): User  
    {
        $this->model = new User();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getById($id): User
    {
        return $this->findById($id);
    }

    public function findByEmail(string $email)
    {
        return $this->model->findOne(['email' => $email]);
    }

    public function findByUsername(string $username)
    {
        return $this->model->findOne(['username' => $username]);
    }

    public function findAllWithPagination($page, $perPage){
        return $this->model->paginate($page, $perPage);
    }
}