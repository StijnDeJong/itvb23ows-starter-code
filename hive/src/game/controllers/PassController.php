<?php
namespace controllers;

use controllers\Controller;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\movecontrollers\AntMoveController;
use controllers\movecontrollers\BeetleMoveController;
use controllers\movecontrollers\GrasshopperMoveController;
use controllers\movecontrollers\QueenMoveController;
use controllers\movecontrollers\SpiderMoveController;
use database\DatabaseService;

class PassController extends Controller{

    public DatabaseService $database_service;

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
        $this->database_service = $database_service;
    }

    public function pass() {
        $this->load_game_from_session();
        if (!$this->is_valid_pass()) 
            return False;
        $this->game->pass();
        $this->save_game_to_session();
        $this->database_service->pass();
        return True;
    }

    private function is_valid_pass() {
        if ($this->does_player_have_any_moves())
            $_SESSION["error"] = "Can only pass in stalemate";
        else
            return True;
        return False;
    }

    private function does_player_have_any_moves() {
        $play_controller = new PlayController($this->database_service);
        if ($play_controller->can_player_play())
            return True;

        $board = $this->game->get_board();
        $active_player_id = $this->game->get_active_player_id();

        foreach ($board->get_board() as $position => $pieces) {
            $piece = end($pieces);
            // Remove opponents pieces from the loop
            if ($piece[0] != $active_player_id)
                continue;

            $move_controller = $this->get_movecontroller($piece[1]);
            if ($move_controller->does_piece_have_moves($position))
                return True;
        }
        return False;
    }

    function get_movecontroller($piece) {
        switch ($piece) {
            case "A":
                return new AntMoveController($this->database_service);
            case "B":
                return new BeetleMoveController($this->database_service);
            case "G":
                return new GrasshopperMoveController($this->database_service);
            case "Q":
                return new QueenMoveController($this->database_service);
            case "S":
                return new SpiderMoveController($this->database_service);
            default:
                                                            
        }    
    }
}

?>