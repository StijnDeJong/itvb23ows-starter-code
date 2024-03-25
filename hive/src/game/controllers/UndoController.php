<?php

namespace controllers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class UndoController extends Controller {

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }
    
    public function undo() {
        $this->load_game_from_session();
        if ($this->game->get_turn_number() == 1) {            
            $_SESSION["error"] = "Cannot undo at the start of the game";
            return;
        }
        $this->database_service->undo();
        
    }

}
?>
