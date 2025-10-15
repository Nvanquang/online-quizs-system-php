<?php

/**
 * @template T of Model
 * @property-read T $model
 */
abstract class BaseRepository
{
    /**
     * @var T
     */
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelInstance();
    }

    /**
     * Child classes must return a new Model instance
     *
     * @return T
     */
    abstract protected function getModelInstance(): Model;  // Thêm return type hint

    
    /**
     * @param mixed $id
     * @return T|null
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param array $conditions
     * @return T|null
     */
    public function findOneBy(array $conditions = [])
    {
        return $this->model->findOne($conditions);
    }

    /**
     * @param array $conditions
     * @return T[]
     */
    public function findAll(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit);
    }

    /**
     * @param array $conditions
     * @return T[]
     */
    public function findBy(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit);
    }

    /**
     * @param array $conditions
     */
    public function exists(array $conditions = []): bool
    {
        $row = $this->model->findOne($conditions);
        return !empty($row);
    }

    /**
     * @param array $conditions
     */
    public function countBy(array $conditions = []): int
    {
        return (int)$this->model->count($conditions);
    }

    /**
     * @param array $conditions
     * @return array{data: T[], total: int, page: int, per_page: int, total_pages: int}
     */
    public function paginate(int $page = 1, int $perPage = 10, array $conditions = [], $orderBy = null)
    {
        return $this->model->paginate($page, $perPage, $conditions, $orderBy);
    }

    
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param mixed $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->delete($id);
    }

    
    /**
     * @param array $params
     * @return mixed
     */
    public function query(string $sql, array $params = [])
    {
        return $this->model->query($sql, $params);
    }

    /**
     * @param array $params
     * @return object|null
     */
    public function fetch(string $sql, array $params = [])
    {
        return $this->model->fetch($sql, $params);
    }

    /**
     * @param array $params
     * @return object[]
     */
    public function fetchAll(string $sql, array $params = [])
    {
        return $this->model->fetchAll($sql, $params);
    }
}