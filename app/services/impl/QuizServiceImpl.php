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
        if($data['rating']){
            $quiz->setRatingSum($quiz->getRatingSum() + $data['rating']);
            $quiz->setRatingCount($quiz->getRatingCount() + 1);
        }
        if(isset($data['is_public'] )){
            $quiz->setIsPublic($data['is_public']);
        }
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

    public function findByCondition($conditions, $orderBy, $limit){
        return $this->quizRepository->findAll($conditions, $orderBy, $limit);
    }

    public function delete($id){
        $quiz = $this->quizRepository->findById($id);
        if(!$this->quizRepository->exists(['id' => $id])){
            throw new Exception("Trò chơi không tồn tại!");
        }
        if(!Auth::getInstance()->isAdmin() || $_COOKIE['user_id'] !== $quiz->getCreatedBy()){
            throw new Exception("Bạn không có quyền xóa!");
        }
        return $this->quizRepository->delete($id);
    }

    public function findAllWithPagination($page, $perPage){
        return $this->quizRepository->findAllWithPagination($page, $perPage);
    }

    public function getTotalQuizzes() {
        return $this->quizRepository->countBy([]); 
    }

    public function filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy){
        return $this->quizRepository->filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy);
    }
}