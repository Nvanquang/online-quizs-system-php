<?php

class UserServiceImpl implements UserService
{
    private static $instant = null;
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instant === null) {
            self::$instant = new self();
        }
        return self::$instant;
    }

    protected function getRepositoryInstance()
    {
        return UserRepository::getInstance();
    }

    public function findAllWithPagination($page, $perPage){
        return $this->userRepository->findAllWithPagination($page, $perPage);
    }

    public function findByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findByUsername($username)
    {
        return $this->userRepository->findByUsername($username);
    }

    public function findById($id)
    {
        return $this->userRepository->findById($id);
    }

    public function create(array $data)
    {
        $this->validateCreateData($data);
        if ($this->userRepository->findByUsername($data['username'])) {
            throw new Exception('Username đã tồn tại!');
        }
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new Exception('Email đã tồn tại!');
        }
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $data['is_admin'] = $data['is_admin'] ?? 0;
        $data['total_points'] = $data['total_points'] ?? 0;
        $data['games_played'] = $data['games_played'] ?? 0;
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        return $this->userRepository->create($data);
    }

    public function update($id, array $data)
    {       

        $user = $this->userRepository->getById($id);
        if ($user && $user->getId() != $id) {
            throw new Exception("User không tìm thấy!");
        }
        
        if($data['username'] != null) {
            $user->setUsername($data['username']);
        }
        if($data['email'] != null) {
            $user->setEmail($data['email']);
        }
        if($data['full_name'] != null) {
            $user->setFullName($data['full_name']);
        }
        $user->setIsAdmin($data['is_admin']);
        return $this->userRepository->update($id, $user->toArray());
    }

    public function getTotalUsers(): int {
        return $this->userRepository->countBy([]); 
    }

    public function delete($id)
    {
        $this->validateDelete($id);
        return $this->userRepository->delete($id);
    }
    
    public function authenticate($username, $password)
    {
        $user = $this->findByEmail($username);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user->getPassword())) {
            return false;
        }
        return $user;
    }

    protected function validateCreateData(array $data): void
    {
        $required = ['username', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ!');
        }
        if (strlen($data['username']) < 3) {
            throw new Exception('Username ít nhất 3 ký tự!');
        }
        if (strlen($data['password']) < 6) {
            throw new Exception('Password ít nhất 6 ký tự!');
        }
    }


    protected function validateDelete($id): void
    {
        $user = $this->userRepository->getById($id);
        if (!$user) {
            throw new Exception('User không tìm thấy!');
        }
        if ($user->isAdmin()) {
            throw new Exception('Không thể xóa user quản trị viên!');
        }
    }
}


