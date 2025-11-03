<?php

class QuestionServiceImpl extends BaseService implements QuestionService
{
    private $questionRepository;

    public function __construct()
    {
        parent::__construct();
        $this->questionRepository = QuestionRepository::getInstance();
    }

    protected function getRepositoryInstance()
    {
        return QuestionRepository::getInstance();
    }

    public function getAll(){
        return $this->questionRepository->findAll();
    }

    public function findById($id){
        if(!$id){
            throw new Exception("Question ID is required");
        }
        return $this->questionRepository->findById($id);
    }

    public function findByQuiz(int $quizId): array{
        return $this->questionRepository->findByQuiz($quizId);
    }
}