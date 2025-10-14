<?php

class QuizRepository extends BaseRepository
{
    protected function getModelInstance()
    {
        return new Quiz();
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

    public function findByCode(string $quizCode)
    {
        return $this->model->findOne(['quiz_code' => $quizCode]);
    }

    public function findByPin(?string $pinCode)
    {
        if ($pinCode === null) return null;
        return $this->model->findOne(['pin_code' => $pinCode]);
    }
}


