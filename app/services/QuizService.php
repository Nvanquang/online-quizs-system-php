<?php

interface QuizService
{
    public function getAll();
    public function findById($id);
    public function findAllByUserId($id);
    public function update($id, $data);
}