<?php

interface QuestionService
{
    public function create(array $data);
    public function getAll();
    public function getTotalQuestions();
    public function findById($id);
    public function findByQuiz(int $quizId);
    public function delete($id);
    public function findAllWithPagination($page, $perPage);
    public function filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy);
}
