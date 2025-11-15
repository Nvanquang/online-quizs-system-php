<?php

interface UserService
{
    public function findAllWithPagination($page, $perPage);
    public function findByEmail($email);
    public function findByUsername($username);

    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    public function authenticate($username, $password);
    // public function updatePoints($userId, $points);
    // public function incrementGamesPlayed($userId);

    // public function getTopUsers($limit = 10);
    // public function getUsersPaginated($page = 1, $perPage = 10, $search = null);

    // public function searchUsers($query, $limit = 10);
    // public function getUserCountByRole();
    // public function getRecentUsers($limit = 10);
}

    

