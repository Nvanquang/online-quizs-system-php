<?php

class SessionPlayerRepository extends BaseRepository
{
    protected function getModelInstance()
    {
        return new SessionPlayer();
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

    public function exists(array $conditions = []): bool
    {
        return $this->model->exists($conditions);
    }

    public function findBySession(int $sessionId, $orderBy = 'total_score DESC', $limit = null)
    {
        return $this->model->findAll(['session_id' => $sessionId], $orderBy, $limit);
    }
}


