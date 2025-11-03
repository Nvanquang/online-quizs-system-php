<?php

class QuizServiceImpl extends BaseService implements QuizService
{
    private $quizRepository;

    public function __construct()
    {
        parent::__construct();
        $this->quizRepository = QuizRepository::getInstance();
    }

    protected function getRepositoryInstance()
    {
        return QuizRepository::getInstance();
    }

    public function getAll(){
        return $this->quizRepository->findAll();
    }

    public function findById($id){
        if(!$id){
            throw new Exception("Quiz ID is required");
        }
        if(!is_numeric($id)){
            throw new Exception("Quiz ID must be a number");
        }
        if($id < 1){
            throw new Exception("Quiz ID must be greater than 0");
        }
        return $this->quizRepository->findById($id);
    }

}