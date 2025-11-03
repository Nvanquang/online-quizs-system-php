<?php

interface QuestionService
{
    public function getAll();
    public function findById($id);
    public function findByQuiz(int $quizId);
}
