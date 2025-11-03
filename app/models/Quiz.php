<?php
class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title','quiz_code','thumbnail_url','created_by','is_public','total_questions','created_at','author','image'
    ];
    protected $casts = [
        'id' => 'int',
        'created_by' => 'int',
        'is_public' => 'bool',
        'total_questions' => 'int',
        'created_at' => 'datetime',
    ];

    private $id;
    private $title;
    private $quiz_code;
    private $thumbnail_url;
    private $created_by;
    private $is_public;
    private $total_questions;
    private $created_at;
    private $author;
    private $image;

    public function __construct(?array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }


    public function getId() { return $this->id; }
    public function setId($v): void { $this->id = (int)$v; }
    public function getTitle() { return $this->title; }
    public function setTitle($v): void { $this->title = $v; }
    public function getQuizCode() { return $this->quiz_code; }
    public function setQuizCode($v): void { $this->quiz_code = $v; }
    public function getThumbnailUrl() { return $this->thumbnail_url; }
    public function setThumbnailUrl($v): void { $this->thumbnail_url = $v; }
    public function getCreatedBy() { return (int)$this->created_by; }
    public function setCreatedBy($v): void { $this->created_by = (int)$v; }
    public function isPublic() { return (bool)$this->is_public; }
    public function setIsPublic($v): void { $this->is_public = (int)$v; }
    public function getTotalQuestions() { return (int)$this->total_questions; }
    public function setTotalQuestions($v): void { $this->total_questions = (int)$v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
    public function getAuthor() { return $this->author; }
    public function setAuthor($v): void { $this->author = $v; }
    public function getImage() { return $this->image; }
    public function setImage($v): void { $this->image = $v; }
}

    