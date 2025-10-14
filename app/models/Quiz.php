<?php

class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title','description','quiz_code','pin_code','thumbnail_url','created_by',
        'game_mode','is_public','is_live','total_questions','total_players','play_count',
        'created_at','started_at','ended_at'
    ];
    protected $casts = [
        'id' => 'int',
        'created_by' => 'int',
        'is_public' => 'bool',
        'is_live' => 'bool',
        'total_questions' => 'int',
        'total_players' => 'int',
        'play_count' => 'int',
    ];

    private $id;
    private $title;
    private $description;
    private $quiz_code;
    private $pin_code;
    private $thumbnail_url;
    private $created_by;
    private $game_mode;
    private $is_public;
    private $is_live;
    private $total_questions;
    private $total_players;
    private $play_count;
    private $created_at;
    private $started_at;
    private $ended_at;

    public function __construct(array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getTitle() { return $this->title; }
    public function setTitle($v): void { $this->title = $v; }
    public function getDescription() { return $this->description; }
    public function setDescription($v): void { $this->description = $v; }
    public function getQuizCode() { return $this->quiz_code; }
    public function setQuizCode($v): void { $this->quiz_code = $v; }
    public function getPinCode() { return $this->pin_code; }
    public function setPinCode($v): void { $this->pin_code = $v; }
    public function getThumbnailUrl() { return $this->thumbnail_url; }
    public function setThumbnailUrl($v): void { $this->thumbnail_url = $v; }
    public function getCreatedBy() { return (int)$this->created_by; }
    public function setCreatedBy($v): void { $this->created_by = (int)$v; }
    public function getGameMode() { return $this->game_mode; }
    public function setGameMode($v): void { $this->game_mode = $v; }
    public function isPublic() { return (bool)$this->is_public; }
    public function setIsPublic($v): void { $this->is_public = (int)$v; }
    public function isLive() { return (bool)$this->is_live; }
    public function setIsLive($v): void { $this->is_live = (int)$v; }
    public function getTotalQuestions() { return (int)$this->total_questions; }
    public function setTotalQuestions($v): void { $this->total_questions = (int)$v; }
    public function getTotalPlayers() { return (int)$this->total_players; }
    public function setTotalPlayers($v): void { $this->total_players = (int)$v; }
    public function getPlayCount() { return (int)$this->play_count; }
    public function setPlayCount($v): void { $this->play_count = (int)$v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
    public function getStartedAt() { return $this->started_at; }
    public function setStartedAt($v): void { $this->started_at = $v; }
    public function getEndedAt() { return $this->ended_at; }
    public function setEndedAt($v): void { $this->ended_at = $v; }
}

    