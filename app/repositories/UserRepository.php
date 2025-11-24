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

    public function exists(array $conditions = []): bool
    {
        $row = $this->model->findOne($conditions);
        return !empty($row);
    }

    public function getById($id): User
    {
        return $this->model->find($id);
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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }

    public function countBy(array $conditions = []): int
    {
        return (int)$this->model->count($conditions);
    }

    public function filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy){
        return $this->model->paginateWithSearch($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy);
    }

}