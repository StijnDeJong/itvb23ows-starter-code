<?php

namespace controllers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class PlayController extends Controller {

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);

    }

    public function play($piece, $to) {
        $this->load_game_from_session();

        if (!$this->is_valid_play($piece, $to))
            return False;
        $this->game->play($piece, $to);

        $this->save_game_to_session();
        $this->database_service->play($piece, $to);
        return True;
    }

    private function is_valid_play($piece, $to) {

        $board = $this->game->get_board();
        $active_player = $this->game->get_active_player();
        $active_player_id = $this->game->get_active_player_id();

        if ($this->needs_to_play_queen() && $piece != "Q")
            $_SESSION["error"] = "Must play queen bee";
        elseif ($active_player->has_piece_in_hand($piece))
            $_SESSION["error"] = "Player does not have piece";
        elseif ($board->is_position_occupied($to))
            $_SESSION["error"] = "Board position is already occupied";
        elseif (!$board->is_empty() && !$board->has_neighbour($to))
            $_SESSION["error"] = "Piece is played outside the hive";
        elseif (!$active_player->is_hand_full() && !$board->are_neighbours_same_color($active_player_id, $to))
            $_SESSION["error"] = "Board position has opposing neighbour";
        else return TRUE;
        return FALSE;
    }

    public function can_player_play() {
        $this->load_game_from_session();
        $active_player = $this->game->get_active_player();
        if (!$active_player->has_pieces_left())
            return False;
        if ($active_player->is_hand_full())
            return True;

        $board = $this->game->get_board();
        $active_player_id = $this->game->get_active_player_id();
        $play_positions = $board->get_play_positions();
        foreach ($play_positions as $positions) {
            if ($board->are_neighbours_same_color($active_player_id, $positions))
                return True;
        }
        return False;

    }
}

?>