<?php


namespace controllers;

use database\DatabaseService;
use objects\Game;
use controllers\Controller;


class RestartController extends Controller
{

    public function __construct(DatabaseService $database_service)
    {
        parent::__construct($database_service);
    }

    public function restart()
    {
        unset($_SESSION['error']);
        $_SESSION['game'] = serialize(new Game());
        $_SESSION['game_id'] = $this->database_service->get_last_game_id();
        $this->database_service->start();
    }
}

?>