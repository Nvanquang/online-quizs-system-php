<?php

interface QuestionService
{
    public function getAll();
    public function getTotalQuestions();
    public function findById($id);
    public function findByQuiz(int $quizId);
    public function delete($id);
    public function findAllWithPagination($page, $perPage);
}
