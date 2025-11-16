<?php

interface QuizService
{
    public function getAll();
    public function getTotalQuizzes();
    public function findById($id);
    public function findAllByUserId($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function findAllWithPagination($page, $perPage);
}