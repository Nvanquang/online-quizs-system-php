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
        $user = $this->userRepository->findByEmail($email);
        if(!$user) {
            throw new Exception("Người dùng không tồn tại!");
        }
        return $user;
    }

    public function findByUsername($username)
    {
        $user = $this->userRepository->findByUsername($username);
        if(!$user) {
            throw new Exception("Người dùng không tồn tại!");
        }
        return $user;
    }

    public function findById($id)
    {
        $user = $this->userRepository->findById($id);
        if(!$user) {
            throw new Exception("Người dùng không tồn tại!");
        }
        return $user;
    }

    public function create(array $data)
    {
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
        if($data['is_admin'] === 1){
            $auth = Auth::getInstance();
            if(!$auth->isAdmin()){
                throw new Exception('Bạn không có đủ quyền hạn!');
            }
        }
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        return $this->userRepository->create($data);
    }

    public function update($id, array $data)
    {       

        $user = $this->userRepository->getById($id);
        if ($user && $user->getId() != $id) {
            throw new Exception("User không tồn tại!");
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
        if($data['is_admin'] === 1){
            $auth = Auth::getInstance();
            if(!$auth->isAdmin()){
                throw new Exception('Bạn không có đủ quyền hạn!');
            }
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
        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new Exception('Tên đăng nhập hoặc mật khẩu không đúng!');
        }
        if($user->isAdmin() != 1){
            throw new Exception('Bạn không có quyền truy cập trang quản trị!');
        }
        return $user;
    }


    protected function validateDelete($id): void
    {
        $user = $this->userRepository->getById($id);
        if (!$user) {
            throw new Exception('User không tìm thấy!');
        }
        if ($user->isAdmin()) {
            throw new Exception('Bạn không có đủ quyền hạn để xóa!');
        }
    }
    
    public function filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy){
        return $this->userRepository->filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy);
    }
}


