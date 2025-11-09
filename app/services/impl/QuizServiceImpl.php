<?php

class QuizServiceImpl extends BaseService implements QuizService
{
    private static $instant = null;
    private $quizRepository;

    public function __construct()
    {
        parent::__construct();
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
        return QuizRepository::getInstance();
    }

    public function getAll(){
        return $this->quizRepository->findAll();
    }

    public function findById($id): object | null {
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

    public function update($id, $data){
        $quiz = $this->findById($id);
        if(!$quiz){
            throw new Exception("Quiz not found");
        }
        if($data['title']){
            $quiz->setTitle($data['title']);
        }
        if($data['description']){
            $quiz->setDescription($data['description']);
        }
        if($data['cover_image']){
            $quiz->setImage($data['cover_image']);
        }
        if($data['total_questions']){
            $quiz->setTotalQuestions($data['total_questions']);
        }
        return $this->quizRepository->update($id, $quiz->toArray());
    }

}