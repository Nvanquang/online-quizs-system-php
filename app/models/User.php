<?php
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username','email','password','full_name','avatar_url','is_admin',
        'created_at', 'updated_at'
    ];
    protected $casts = [
        'id' => 'int',
        'is_admin' => 'int'
    ];

    private $id;
    private $username;
    private $email;
    private $password;
    private $full_name;
    private $avatar_url;
    private $is_admin;
    private $created_at;
    private $updated_at;

    public function __construct(?array $data = null)
    {
        parent::__construct();
        if ($data) { $this->fill($data); }
    }



    public function getId() { return $this->id; }
    public function setId($id): void { $this->id = (int)$id; }
    public function getUsername() { return $this->username; }
    public function setUsername($v): void { $this->username = $v; }
    public function getEmail() { return $this->email; }
    public function setEmail($v): void { $this->email = $v; }
    public function getPassword() { return $this->password; }
    public function setPassword($v): void { $this->password = $v; }
    public function getFullName() { return $this->full_name; }
    public function setFullName($v): void { $this->full_name = $v; }
    public function getAvatarUrl() { return $this->avatar_url; }
    public function setAvatarUrl($v): void { $this->avatar_url = $v; }
    public function isAdmin() { return (int)$this->is_admin; }
    public function setIsAdmin($v): void { $this->is_admin = (int)$v; }
    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($v): void { $this->created_at = $v; }
    public function getUpdatedAt() { return $this->updated_at; }
    public function setUpdatedAt($v): void { $this->updated_at = $v; }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'full_name' => $this->getFullName(),
            'avatar_url' => $this->getAvatarUrl(),
            'is_admin' => $this->isAdmin(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }

}


