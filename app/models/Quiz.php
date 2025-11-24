<?php
class Quiz extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title','quiz_code','created_by','is_public','total_questions','rating_sum', 'rating_count','created_at','updated_at','author','image'
    ];
    protected $casts = [
        'id' => 'int',
        'created_by' => 'int',
        'is_public' => 'int',
        'total_questions' => 'int',
        'rating_sum' => 'int',
        'rating_count' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    private $id;
    private $title;
    private $quiz_code;
    private $is_public;
    private $total_questions;
    private $rating_sum;
    private $rating_count;
    private $created_at;
    private $created_by;
    private $updated_at;
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
    public function getCreatedBy() { return (int)$this->created_by; }
    public function setCreatedBy($v): void { $this->created_by = (int)$v; }
    public function isPublic() { return (int)$this->is_public; }
    public function setIsPublic($v): void { $this->is_public = (int)$v; }
    public function getTotalQuestions() { return (int)$this->total_questions; }
    public function setTotalQuestions($v): void { $this->total_questions = (int)$v; }
    public function getRatingSum() { return (int)$this->rating_sum; }
    public function setRatingSum($v): void { $this->rating_sum = (int)$v; }
    public function getRatingCount() { return (int)$this->rating_count; }
    public function setRatingCount($v): void { $this->rating_count = (int)$v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
    public function getUpdatedAt() { return $this->updated_at; }
    public function setUpdatedAt($v): void { $this->updated_at = $v; }
    public function getAuthor() { return $this->author; }
    public function setAuthor($v): void { $this->author = $v; }
    public function getImage() { return $this->image; }
    public function setImage($v): void { $this->image = $v; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'quiz_code' => $this->getQuizCode(),
            'created_by' => $this->getCreatedBy(),
            'is_public' => $this->isPublic(),
            'total_questions' => $this->getTotalQuestions(),
            'rating_count' => $this->getRatingCount(),
            'rating_sum' => $this->getRatingSum(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'author' => $this->getAuthor(),
            'image' => $this->getImage(),
        ];
    }
}