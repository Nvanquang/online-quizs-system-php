<?php

interface SessionPlayerService
{
    public function create($data);

    public function findByUserId(int $userId);

    public function update($sessionPlayerId, $data);
}
