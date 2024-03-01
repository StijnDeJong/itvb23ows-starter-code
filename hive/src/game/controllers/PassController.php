<?php
namespace controllers;

use controllers\Controller;
use objects\Game;
use objects\Board;
use objects\Player;
use database\DatabaseService;

class PassController extends Controller{

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }

    public function pass() {
        $this->load_game_from_session();
        if (!$this->is_valid_pass()) 
            return;
        $this->game->pass();
        $this->save_game_to_session();
        $this->database_service->pass();  
    }

    private function is_valid_pass() {
        if ($this->needs_to_play_queen()) {
            $_SESSION['error'] = 'Must play queen bee';
            return FALSE;
        }
        return TRUE;
    }
}

?>