<?php

interface GameSessionService
{
    public function createSession($hostId, $quizCode, $actualMode = null);

    public function findBySessionCode($sessionCode);

    public function update($sessionCode, $data);
}
