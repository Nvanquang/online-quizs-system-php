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
    public function findById($id): object | null
    {
        return $this->model->find($id);
    }

    /**
     * @param array $conditions
     * @return T|null
     */
    public function findOneBy(array $conditions = []): object
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
}