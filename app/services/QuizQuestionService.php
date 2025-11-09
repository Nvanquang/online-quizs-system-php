<?php

interface QuizQuestionService
{
    public function create($data);
    public function findLastQuestionByQuizId(int $quizId);
}