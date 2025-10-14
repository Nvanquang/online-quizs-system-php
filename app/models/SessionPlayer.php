<?php

class SessionPlayer extends Model
{
    protected $table = 'session_players';
    protected $primaryKey = 'id';
    protected $fillable = [
        'session_id','user_id','nickname','total_score','rank_position','is_ready','joined_at'
    ];
    protected $casts = [
        'id' => 'int',
        'session_id' => 'int',
        'user_id' => 'int',
        'total_score' => 'int',
        'rank_position' => 'int',
        'is_ready' => 'bool',
    ];

    private $id;
    private $session_id;
    private $user_id;
    private $nickname;
    private $total_score;
    private $rank_position;
    private $is_ready;
    private $joined_at;

    public function __construct(array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getSessionId() { return (int)$this->session_id; }
    public function setSessionId($v): void { $this->session_id = (int)$v; }
    public function getUserId() { return (int)$this->user_id; }
    public function setUserId($v): void { $this->user_id = (int)$v; }
    public function getNickname() { return $this->nickname; }
    public function setNickname($v): void { $this->nickname = $v; }
    public function getTotalScore() { return (int)$this->total_score; }
    public function setTotalScore($v): void { $this->total_score = (int)$v; }
    public function getRankPosition() { return (int)$this->rank_position; }
    public function setRankPosition($v): void { $this->rank_position = (int)$v; }
    public function isReady() { return (bool)$this->is_ready; }
    public function setIsReady($v): void { $this->is_ready = (int)$v; }
    public function getJoinedAt() { return $this->joined_at; }
    public function setJoinedAt($v): void { $this->joined_at = $v; }
}


