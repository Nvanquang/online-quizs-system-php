<?php

class GameSession extends Model
{
    protected $table = 'game_sessions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'quiz_id','session_code','host_id','status','current_question','total_players','started_at','ended_at','created_at'
    ];
    protected $casts = [
        'id' => 'int',
        'quiz_id' => 'int',
        'host_id' => 'int',
        'current_question' => 'int',
        'total_players' => 'int',
    ];

    private $id;
    private $quiz_id;
    private $session_code;
    private $host_id;
    private $status;
    private $current_question;
    private $total_players;
    private $started_at;
    private $ended_at;
    private $created_at;

    public function __construct(array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getQuizId() { return (int)$this->quiz_id; }
    public function setQuizId($v): void { $this->quiz_id = (int)$v; }
    public function getSessionCode() { return $this->session_code; }
    public function setSessionCode($v): void { $this->session_code = $v; }
    public function getHostId() { return (int)$this->host_id; }
    public function setHostId($v): void { $this->host_id = (int)$v; }
    public function getStatus() { return $this->status; }
    public function setStatus($v): void { $this->status = $v; }
    public function getCurrentQuestion() { return (int)$this->current_question; }
    public function setCurrentQuestion($v): void { $this->current_question = (int)$v; }
    public function getTotalPlayers() { return (int)$this->total_players; }
    public function setTotalPlayers($v): void { $this->total_players = (int)$v; }
    public function getStartedAt() { return $this->started_at; }
    public function setStartedAt($v): void { $this->started_at = $v; }
    public function getEndedAt() { return $this->ended_at; }
    public function setEndedAt($v): void { $this->ended_at = $v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
}


