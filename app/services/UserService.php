<?php

/**
 * UserService - Business logic for User management
 */
class UserService extends BaseService
{
    protected function getRepositoryInstance()
    {
        return new UserRepository();
    }

    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail($identifier)
    {
        // Try to find by username first
        $user = $this->repository->findByUsername($identifier);
        
        if (!$user) {
            // If not found by username, try email
            $user = $this->repository->findByEmail($identifier);
        }
        
        return $user;
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        return $this->repository->findByUsername($username);
    }

    /**
     * Create new user with validation
     */
    public function create(array $data)
    {
        // Validate required fields
        $this->validateCreateData($data);
        
        // Check if username already exists
        if ($this->repository->findByUsername($data['username'])) {
            throw new Exception('Username already exists');
        }
        
        // Check if email already exists
        if ($this->repository->findByEmail($data['email'])) {
            throw new Exception('Email already exists');
        }
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Set default values
        $data['is_admin'] = $data['is_admin'] ?? 0;
        $data['total_points'] = $data['total_points'] ?? 0;
        $data['games_played'] = $data['games_played'] ?? 0;
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        
        return parent::create($data);
    }

    /**
     * Update user with validation
     */
    public function update($id, array $data)
    {
        // Validate update data
        $this->validateUpdateData($id, $data);
        
        // Check if email is being changed and already exists
        if (isset($data['email'])) {
            $existingUser = $this->repository->findByEmail($data['email']);
            if ($existingUser && $existingUser->getId() != $id) {
                throw new Exception('Email already exists');
            }
        }
        
        // Check if username is being changed and already exists
        if (isset($data['username'])) {
            $existingUser = $this->repository->findByUsername($data['username']);
            if ($existingUser && $existingUser->getId() != $id) {
                throw new Exception('Username already exists');
            }
        }
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return parent::update($id, $data);
    }

    /**
     * Authenticate user
     */
    public function authenticate($username, $password)
    {
        $user = $this->findByUsernameOrEmail($username);
        
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user->getPassword())) {
            return false;
        }
        
        return $user;
    }

    /**
     * Update user points
     */
    public function updatePoints($userId, $points)
    {
        $user = $this->findById($userId);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        $newPoints = $user->getTotalPoints() + $points;
        return $this->update($userId, ['total_points' => $newPoints]);
    }

    /**
     * Increment games played
     */
    public function incrementGamesPlayed($userId)
    {
        $user = $this->findById($userId);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        $newCount = $user->getGamesPlayed() + 1;
        return $this->update($userId, ['games_played' => $newCount]);
    }

    /**
     * Get top users by points
     */
    public function getTopUsers($limit = 10)
    {
        return $this->repository->findAll([], 'total_points DESC', $limit);
    }

    /**
     * Get users with pagination
     */
    public function getUsersPaginated($page = 1, $perPage = 10, $search = null)
    {
        $conditions = [];
        
        if ($search) {
            $conditions['search'] = $search; // This would need custom implementation in repository
        }
        
        return $this->repository->paginate($page, $perPage, $conditions, 'created_at DESC');
    }

    /**
     * Get user statistics
     */
    // public function getUserStats($userId)
    // {
    //     $user = $this->findById($userId);
    //     if (!$user) {
    //         throw new Exception('User not found');
    //     }
        
    //     // Get additional stats from other services if needed
    //     $gameHistoryService = new GameHistoryService();
    //     $recentGames = $gameHistoryService->getUserRecentGames($userId, 5);
        
    //     return [
    //         'user' => $user,
    //         'total_points' => $user->getTotalPoints(),
    //         'games_played' => $user->getGamesPlayed(),
    //         'recent_games' => $recentGames,
    //         'average_score' => $this->calculateAverageScore($userId),
    //         'rank' => $this->getUserRank($userId)
    //     ];
    // }

    /**
     * Calculate user's average score
     */
    // private function calculateAverageScore($userId)
    // {
    //     $gameHistoryService = new GameHistoryService();
    //     $games = $gameHistoryService->getUserHistory($userId);
        
    //     if (empty($games)) {
    //         return 0;
    //     }
        
    //     $totalScore = 0;
    //     foreach ($games as $game) {
    //         $totalScore += $game['score'] ?? 0;
    //     }
        
    //     return round($totalScore / count($games), 2);
    // }

    /**
     * Get user's rank based on points
     */
    private function getUserRank($userId)
    {
        $user = $this->findById($userId);
        if (!$user) {
            return 0;
        }
        
        $sql = "SELECT COUNT(*) + 1 as rank FROM users WHERE total_points > ?";
        $result = $this->repository->fetch($sql, [$user->getTotalPoints()]);
        
        return $result['rank'] ?? 0;
    }

    /**
     * Validate create data
     */
    protected function validateCreateData(array $data): void
    {
        $required = ['username', 'email', 'password'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // Validate username length
        if (strlen($data['username']) < 3) {
            throw new Exception('Username must be at least 3 characters');
        }
        
        // Validate password length
        if (strlen($data['password']) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
    }

    /**
     * Validate update data
     */
    protected function validateUpdateData($id, array $data): void
    {
        // Check if user exists
        if (!$this->findById($id)) {
            throw new Exception('User not found');
        }
        
        // Validate email format if provided
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // Validate username length if provided
        if (isset($data['username']) && strlen($data['username']) < 3) {
            throw new Exception('Username must be at least 3 characters');
        }
        
        // Validate password length if provided
        if (isset($data['password']) && strlen($data['password']) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
    }

    /**
     * Validate delete
     */
    protected function validateDelete($id): void
    {
        $user = $this->findById($id);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // Check if user is admin (prevent deleting admin users)
        if ($user->isAdmin()) {
            throw new Exception('Cannot delete admin user');
        }
        
        // Check if user has active games (optional business rule)
        // This would need to be implemented based on your game logic
    }

    /**
     * Search users
     */
    public function searchUsers($query, $limit = 10)
    {
        $sql = "SELECT * FROM users WHERE 
                username LIKE ? OR 
                email LIKE ? OR 
                full_name LIKE ? 
                ORDER BY username ASC 
                LIMIT ?";
        
        $searchTerm = "%{$query}%";
        return $this->repository->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
    }

    /**
     * Get user count by role
     */
    public function getUserCountByRole()
    {
        $sql = "SELECT 
                    CASE WHEN is_admin = 1 THEN 'admin' ELSE 'user' END as role,
                    COUNT(*) as count 
                FROM users 
                GROUP BY is_admin";
        
        return $this->repository->fetchAll($sql);
    }

    /**
     * Get recently registered users
     */
    public function getRecentUsers($limit = 10)
    {
        return $this->repository->findAll([], 'created_at DESC', $limit);
    }
}
