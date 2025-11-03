<?php

class Question extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'content','answer_a','answer_b','answer_c','answer_d','correct_answer',
        'explanation','image_url','time_limit','created_by','created_at'
    ];
    protected $casts = [
        'id' => 'int',
        'time_limit' => 'int',
        'created_by' => 'int',
    ];

    private $id;
    private $content;
    private $answer_a;
    private $answer_b;
    private $answer_c;
    private $answer_d;
    private $correct_answer;
    private $explanation;
    private $image_url;
    private $time_limit;
    private $created_by;
    private $created_at;

    public function __construct(?array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getContent() { return $this->content; }
    public function setContent($v): void { $this->content = $v; }
    public function getAnswerA() { return $this->answer_a; }
    public function setAnswerA($v): void { $this->answer_a = $v; }
    public function getAnswerB() { return $this->answer_b; }
    public function setAnswerB($v): void { $this->answer_b = $v; }
    public function getAnswerC() { return $this->answer_c; }
    public function setAnswerC($v): void { $this->answer_c = $v; }
    public function getAnswerD() { return $this->answer_d; }
    public function setAnswerD($v): void { $this->answer_d = $v; }
    public function getCorrectAnswer() { return $this->correct_answer; }
    public function setCorrectAnswer($v): void { $this->correct_answer = $v; }
    public function getExplanation() { return $this->explanation; }
    public function setExplanation($v): void { $this->explanation = $v; }
    public function getImageUrl() { return $this->image_url; }
    public function setImageUrl($v): void { $this->image_url = $v; }
    public function getTimeLimit() { return (int)$this->time_limit; }
    public function setTimeLimit($v): void { $this->time_limit = (int)$v; }
    public function getCreatedBy() { return (int)$this->created_by; }
    public function setCreatedBy($v): void { $this->created_by = (int)$v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
}


