<?php
interface GameHistoryService
{
    public function create($data);

    public function findByUser(int $userId);
}
