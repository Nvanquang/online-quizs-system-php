<?php

class GameHistory extends Model
{
    protected $table = 'game_history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'session_id','quiz_id','user_id','final_score','final_rank','correct_answers','total_questions','played_at'
    ];
    protected $casts = [
        'id' => 'int',
        'session_id' => 'int',
        'quiz_id' => 'int',
        'user_id' => 'int',
        'final_score' => 'int',
        'final_rank' => 'int',
        'correct_answers' => 'int',
        'total_questions' => 'int',
    ];

    private $id;
    private $session_id;
    private $quiz_id;
    private $user_id;
    private $final_score;
    private $final_rank;
    private $correct_answers;
    private $total_questions;
    private $played_at;

    public function __construct(?array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getSessionId() { return (int)$this->session_id; }
    public function setSessionId($v): void { $this->session_id = (int)$v; }
    public function getQuizId() { return (int)$this->quiz_id; }
    public function setQuizId($v): void { $this->quiz_id = (int)$v; }
    public function getUserId() { return (int)$this->user_id; }
    public function setUserId($v): void { $this->user_id = (int)$v; }
    public function getFinalScore() { return (int)$this->final_score; }
    public function setFinalScore($v): void { $this->final_score = (int)$v; }
    public function getFinalRank() { return (int)$this->final_rank; }
    public function setFinalRank($v): void { $this->final_rank = (int)$v; }
    public function getCorrectAnswers() { return (int)$this->correct_answers; }
    public function setCorrectAnswers($v): void { $this->correct_answers = (int)$v; }
    public function getTotalQuestions() { return (int)$this->total_questions; }
    public function setTotalQuestions($v): void { $this->total_questions = (int)$v; }
    public function getPlayedAt() { return $this->played_at; }
    public function setPlayedAt($v): void { $this->played_at = $v; }
}


