<?php

interface UserService
{
    public function findAllWithPagination($page, $perPage);
    public function findByEmail($email);
    public function findByUsername($username);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function authenticate($username, $password);
    public function filterAllWithPagination($searchField, $keyword, $page, $perPage, $extraConditions, $orderBy);
}

    

