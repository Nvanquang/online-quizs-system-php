<?php

class QuizQuestionServiceImpl implements QuizQuestionService
{
    private static $instant = null;
    private $quizQuestionRepository;
    private $questionRepository;
    private $quizRepository;

    public function __construct()
    {
        $this->quizQuestionRepository = QuizQuestionRepository::getInstance();
        $this->questionRepository = QuestionRepository::getInstance();
        $this->quizRepository = QuizRepository::getInstance();
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
        if(!$this->quizRepository->exists(['id' => $data['quiz_id']])){
            throw new Exception("Trò chơi không tồn tại!");
        }
        if(!$this->questionRepository->exists(['id' => $data['question_id']])){
            throw new Exception("Câu hỏi không tồn tại!");
        }
        return $this->quizQuestionRepository->create($data);
    }

    public function findLastQuestionByQuizId(int $quizId): object | null
    {
        return $this->quizQuestionRepository->findLastQuestionByQuizId($quizId);
    }
}