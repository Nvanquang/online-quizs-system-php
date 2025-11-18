<?php

class QuizServiceImpl implements QuizService
{
    private static $instant = null;
    private $quizRepository;

    public function __construct()
    {
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
        $quiz = $this->quizRepository->findById($id);
        if(!$quiz){
            throw new Exception("Trò chơi không tồn tại!");
        }
        return $quiz;
    }

    public function create($data){
        return $this->quizRepository->create($data);
    }

    public function update($id, $data){
        $quiz = $this->findById($id);
        if(!$quiz){
            throw new Exception("Trò chơi không tồn tại!");
        }
        if($data['title']){
            $quiz->setTitle($data['title']);
        }
        if($data['image']){
            $quiz->setImage($data['image']);
        }
        if($data['total_questions']){
            $quiz->setTotalQuestions($data['total_questions']);
        }
        
        $quiz->setIsPublic($data['is_public']);

        if($data['updated_at']){
            $quiz->setUpdatedAt($data['updated_at']);
        }
        return $this->quizRepository->update($id, $quiz->toArray());
    }

    public function findAllByUserId($id){
        $quiz = $this->quizRepository->findAllByUserId($id);
        if(!$quiz){
            throw new Exception("Trò chơi không tồn tại!");
        }
        return $quiz;
    }

    public function delete($id){
        if(!$this->quizRepository->exists(['id' => $id])){
            throw new Exception("Trò chơi không tồn tại!");
        }
        return $this->quizRepository->delete($id);
    }

    public function findAllWithPagination($page, $perPage){
        return $this->quizRepository->findAllWithPagination($page, $perPage);
    }

    public function getTotalQuizzes() {
        return $this->quizRepository->countBy([]); 
    }
}