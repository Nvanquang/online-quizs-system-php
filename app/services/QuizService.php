<?php

interface QuizService
{
    public function getAll();
    public function findById($id);
    public function update($id, $data);
}