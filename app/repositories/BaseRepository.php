<?php

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelInstance();
    }

    /**
     * Child classes must return a new Model instance
     */
    abstract protected function getModelInstance();

    
    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findOneBy(array $conditions = [])
    {
        return $this->model->findOne($conditions);
    }

    public function findAll(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit);
    }

    public function findBy(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit);
    }

    public function exists(array $conditions = []): bool
    {
        $row = $this->model->findOne($conditions);
        return !empty($row);
    }

    public function countBy(array $conditions = []): int
    {
        return (int)$this->model->count($conditions);
    }

    public function paginate(int $page = 1, int $perPage = 10, array $conditions = [], $orderBy = null)
    {
        return $this->model->paginate($page, $perPage, $conditions, $orderBy);
    }

    
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }

    
    public function query(string $sql, array $params = [])
    {
        return $this->model->query($sql, $params);
    }

    public function fetch(string $sql, array $params = [])
    {
        return $this->model->fetch($sql, $params);
    }

    public function fetchAll(string $sql, array $params = [])
    {
        return $this->model->fetchAll($sql, $params);
    }
}


