<?php
class PlayerAnswer extends Model
{
    protected $table = 'player_answers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'session_id','user_id','question_id','selected_answer','is_correct',
        'response_time','points_earned','answered_at'
    ];
    protected $casts = [
        'id' => 'int',
        'session_id' => 'int',
        'user_id' => 'int',
        'question_id' => 'int',
        'is_correct' => 'bool',
        'response_time' => 'float',
        'points_earned' => 'int',
    ];

    private $id;
    private $session_id;
    private $user_id;
    private $question_id;
    private $selected_answer;
    private $is_correct;
    private $response_time;
    private $points_earned;
    private $answered_at;

    public function __construct(?array $data = null)
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
    public function getQuestionId() { return (int)$this->question_id; }
    public function setQuestionId($v): void { $this->question_id = (int)$v; }
    public function getSelectedAnswer() { return $this->selected_answer; }
    public function setSelectedAnswer($v): void { $this->selected_answer = $v; }
    public function isCorrect() { return (bool)$this->is_correct; }
    public function setIsCorrect($v): void { $this->is_correct = (int)$v; }
    public function getResponseTime() { return (float)$this->response_time; }
    public function setResponseTime($v): void { $this->response_time = (float)$v; }
    public function getPointsEarned() { return (int)$this->points_earned; }
    public function setPointsEarned($v): void { $this->points_earned = (int)$v; }
    public function getAnsweredAt() { return $this->answered_at; }
    public function setAnsweredAt($v): void { $this->answered_at = $v; }
}


