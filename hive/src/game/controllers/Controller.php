<?php
namespace controllers;

use database\DatabaseService;
use objects\Game;

abstract class Controller {
    public DatabaseService $database_service;
    public Game $game;

    public function __construct(DatabaseService $database_service) {
        $this->database_service = $database_service;
    }

    protected function load_game_from_session() {
        $this->game = unserialize($_SESSION["game"]);
    }
    protected function save_game_to_session() {
        $_SESSION["game"] = serialize($this->game);       
    }
    protected function needs_to_play_queen() {
        // Because we force both players to play the queen at their 4th turn,
        //  after their 5th turn the check is no longer needed
        return (
            $this->game->get_turn_number() <= 8 &&
            $this->game->get_turn_number() >= 7 &&
            !$this->game->get_active_player()->has_played_queen()
        );        
    }

}

?>