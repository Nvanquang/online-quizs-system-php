<?php

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';
    protected $primaryKey = null; // composite key
    protected $fillable = [
        'quiz_id','question_id','order_number','time_limit'
    ];
    protected $casts = [
        'quiz_id' => 'int',
        'question_id' => 'int',
        'order_number' => 'int',
        'time_limit' => 'int',
    ];

    private $quiz_id;
    private $question_id;
    private $order_number;
    private $time_limit;

    public function __construct(?array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getQuizId() { return (int)$this->quiz_id; }
    public function setQuizId($v): void { $this->quiz_id = (int)$v; }
    public function getQuestionId() { return (int)$this->question_id; }
    public function setQuestionId($v): void { $this->question_id = (int)$v; }
    public function getOrderNumber() { return (int)$this->order_number; }
    public function setOrderNumber($v): void { $this->order_number = (int)$v; }
    public function getTimeLimit() { return $this->time_limit !== null ? (int)$this->time_limit : null; }
    public function setTimeLimit($v): void { $this->time_limit = $v !== null ? (int)$v : null; }
}


