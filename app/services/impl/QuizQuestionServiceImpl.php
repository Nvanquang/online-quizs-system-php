<?php

class QuizQuestionServiceImpl implements QuizQuestionService
{
    private static $instant = null;
    private $quizQuestionRepository;

    public function __construct()
    {
        $this->quizQuestionRepository = QuizQuestionRepository::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instant === null) {
            self::$instant = new self();
        }
        return self::$instant;
    }

    protected function getRepositoryInstance()
    {
        return QuizQuestionRepository::getInstance();
    }

    public function create($data)
    {
        return $this->quizQuestionRepository->create($data);
    }

    public function findLastQuestionByQuizId(int $quizId): object | null
    {
        return $this->quizQuestionRepository->findLastQuestionByQuizId($quizId);
    }
}