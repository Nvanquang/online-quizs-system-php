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
        if($data['image']){
            $quiz->setImage($data['image']);
        }
        
        $quiz->setIsPublic($data['is_public']);

        if($data['updated_at']){
            $quiz->setUpdatedAt($data['updated_at']);
        }
        return $this->quizRepository->update($id, $quiz->toArray());
    }

    public function findAllByUserId($id){
        if(!$id){
            throw new Exception("User ID is required");
        }
        if(!is_numeric($id)){
            throw new Exception("User ID must be a number");
        }
        if($id < 1){
            throw new Exception("User ID must be greater than 0");
        }
        return $this->quizRepository->findAllByUserId($id);
    }

    public function findAllWithPagination($page, $perPage){
        return $this->quizRepository->findAllWithPagination($page, $perPage);
    }

    public function getTotalQuizzes() {
        return $this->quizRepository->countBy([]); 
    }
}