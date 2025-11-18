<?php

class QuestionServiceImpl implements QuestionService
{
    private static $instant = null;
    private $questionRepository;

    public function __construct()
    {
        $this->questionRepository = QuestionRepository::getInstance();
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
        return QuestionRepository::getInstance();
    }

    public function create(array $data)
    {
        return $this->questionRepository->create($data);
    }

    public function getAll(){
        return $this->questionRepository->findAll();
    }

    public function getTotalQuestions() {
        return $this->questionRepository->countBy([]); 
    }

    public function findById($id){
        $question = $this->questionRepository->findById($id);
        if (!$question) throw new Exception("Câu hỏi không tồn tại!");
        return $question;
    }

    public function findByQuiz(int $quizId): array{
        return $this->questionRepository->findByQuiz($quizId);
    }

    public function update($id, array $data){
        $question = $this->questionRepository->findById($id);
        if (!$question) {
            throw new Exception("Câu hỏi không tồn tại!");
        }

        if($data['content'] != null) {
            $question->setContent($data['content']);
        }
        if($data['answer_a'] != null) {
            $question->setAnswerA($data['answer_a']);
        }
        if($data['answer_b'] != null) {
            $question->setAnswerB($data['answer_b']);
        }
        if($data['answer_c'] != null) {
            $question->setAnswerC($data['answer_c']);
        }
        if($data['answer_d'] != null) {
            $question->setAnswerD($data['answer_d']);
        }
        if($data['correct_answer'] != null) {
            $question->setCorrectAnswer($data['correct_answer']);
        }
        if($data['explanation'] != null) {
            $question->setExplanation($data['explanation']);
        }
        if($data['image_url'] != null) {
            $question->setImageUrl($data['image_url']);
        }
        if($data['time_limit'] != null) {
            $question->setTimeLimit($data['time_limit']);
        }
        return $this->questionRepository->update($id, $question->toArray());
    }

    public function delete($id){
        if(!$this->questionRepository->exists(['id' => $id])){
            throw new Exception("Câu hỏi không tồn tại!");
        }
        return $this->questionRepository->delete($id);
    }

    public function findAllWithPagination($page, $perPage){
        return $this->questionRepository->findAllWithPagination($page, $perPage);
    }
}