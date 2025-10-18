<?php

class GameController extends Controller
{
    public function lobby($code)
    {
        echo $this->renderPartial('game/lobby', ['code' => $code]);
    }

    public function waiting($code)
    {
        echo $this->renderPartial('game/waiting', ['code' => $code]);
    }

    public function doJoin($code)
    {
        echo $this->renderPartial('game/waiting', ['code' => $code]);
    }

    public function play($sessionId)
    {
        echo $this->renderPartial('game/play', ['sessionId' => $sessionId]);
    }

    public function submitAnswer($sessionId)
    {
        echo $this->renderPartial('game/submitAnswer', ['sessionId' => $sessionId]);
    }
}
