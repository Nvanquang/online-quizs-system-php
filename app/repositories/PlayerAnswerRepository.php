<?php

class PlayerAnswerRepository extends BaseRepository
{
    protected function getModelInstance()
    {
        return new PlayerAnswer();
    }

    // CRUD wrappers
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

    public function findById($id)
    {
        return $this->model->findById($id);
    }

    public function findAll(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->model->findAll($conditions, $orderBy, $limit);
    }

    public function exists(array $conditions = []): bool
    {
        return $this->model->exists($conditions);
    }
    
    public function findBySessionAndUser(int $sessionId, int $userId)
    {
        return $this->model->findAll(['session_id' => $sessionId, 'user_id' => $userId]);
    }
}


