<?php

class UserServiceImpl extends BaseService implements UserService
{
    private static $instant = null;
    private $userRepository;

    public function __construct()
    {
        parent::__construct();
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

    // public function updatePoints($userId, $points)
    // {
    //     $user = $this->findById($userId);
    //     if (!$user) {
    //         throw new Exception('User không tìm thấy!');
    //     }
    //     $newPoints = $user->getTotalPoints() + $points;
    //     return $this->update($userId, ['total_points' => $newPoints]);
    // }

    // public function incrementGamesPlayed($userId)
    // {
    //     $user = $this->findById($userId);
    //     if (!$user) {
    //         throw new Exception('User không tìm thấy!');
    //     }
    //     $newCount = $user->getGamesPlayed() + 1;
    //     return $this->update($userId, ['games_played' => $newCount]);
    // }

    // public function getTopUsers($limit = 10)
    // {
    //     return $this->repository->findAll([], 'total_points DESC', $limit);
    // }

    // public function getUsersPaginated($page = 1, $perPage = 10, $search = null)
    // {
    //     $conditions = [];
    //     if ($search) {
    //         $conditions['search'] = $search;
    //     }
    //     return $this->repository->paginate($page, $perPage, $conditions, 'created_at DESC');
    // }

    // public function searchUsers($query, $limit = 10)
    // {
    //     $sql = "SELECT * FROM users WHERE 
    //             username LIKE ? OR 
    //             email LIKE ? OR 
    //             full_name LIKE ? 
    //             ORDER BY username ASC 
    //             LIMIT ?";
    //     $searchTerm = "%{$query}%";
    //     return $this->repository->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
    // }

    // public function getUserCountByRole()
    // {
    //     $sql = "SELECT 
    //                 CASE WHEN is_admin = 1 THEN 'admin' ELSE 'user' END as role,
    //                 COUNT(*) as count 
    //             FROM users 
    //             GROUP BY is_admin";
    //     return $this->repository->fetchAll($sql);
    // }

    // public function getRecentUsers($limit = 10)
    // {
    //     return $this->repository->findAll([], 'created_at DESC', $limit);
    // }

    // private function getUserRank($userId)
    // {
    //     $user = $this->findById($userId);
    //     if (!$user) {
    //         return 0;
    //     }
    //     $sql = "SELECT COUNT(*) + 1 as rank FROM users WHERE total_points > ?";
    //     $result = $this->repository->fetch($sql, [$user->getTotalPoints()]);
    //     return $result['rank'] ?? 0;
    // }

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
        $user = $this->findById($id);
        if (!$user) {
            throw new Exception('User không tìm thấy!');
        }
        if ($user->isAdmin()) {
            throw new Exception('Không thể xóa user quản trị viên!');
        }
    }
}


