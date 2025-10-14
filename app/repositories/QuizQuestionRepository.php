<?php

class QuizQuestionRepository extends BaseRepository
{
    protected function getModelInstance()
    {
        return new QuizQuestion();
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

    public function paginate(int $page = 1, int $perPage = 10, array $conditions = [], $orderBy = null)
    {
        return $this->model->paginate($page, $perPage, $conditions, $orderBy);
    }

    public function findByQuiz(int $quizId, $orderBy = 'order_number ASC')
    {
        return $this->model->findAll(['quiz_id' => $quizId], $orderBy);
    }
}


