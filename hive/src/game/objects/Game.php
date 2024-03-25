<?php

namespace objects;

use objects\Player;
use objects\Board;

class Game 
{
    private array $players;
    private int $active_player_id;
    private Board $board;
    private int $turn_number;
    private array $queen_positions;
    private bool $is_game_finished;

    public function __construct() {
        $this->players = [new Player(), new Player()];
        $this->active_player_id = 0;
        $this->board = new Board();
        $this->turn_number = 1;
        $this->queen_positions = [NULL, NULL];
        $this->is_game_finished = False;
    }

    public function get_board() {
        return $this->board;
    }
    public function get_player_white() {
        return $this->players[0];
    }
    public function get_player_black() {
        return $this->players[1];
    }
    public function get_active_player() {
        return $this->players[$this->active_player_id];
    }
    public function get_active_player_id() {
        return $this->active_player_id;
    }
    public function get_turn_number() {
        return $this->turn_number;
    }
    public function set_turn_number($turn_number) {
        $this->turn_number = $turn_number;
    }
    public function get_queen_positions() {
        return $this->queen_positions;
    }
    public function set_queen_positions($positions) {
        $this->queen_positions = $positions;
    }

    public function is_game_finished() {
        return $this->is_game_finished;
    }

    public function play($piece, $to) {
        if ($piece == "Q") 
            $this->queen_positions[$this->get_active_player_id()] = $to;
        $this->board->place_piece($piece, $to, $this->active_player_id);
        $this->get_active_player()->remove_piece($piece);
        $this->advance_turn();
    }

    public function move($from, $to) {
        if ($this->board->get_piece($from) == "Q")
            $this->queen_positions[$this->get_active_player_id()] = $to;
        $this->board->move($from, $to);
        $this->advance_turn();
    }

    public function pass() {
        $this->advance_turn();
    }

    private function advance_turn() {
        $this->switch_active_player();
        $this->turn_number++;
        $this->end_game_if_finished();
    }

    private function switch_active_player() {
        $this->active_player_id = 1 - $this->active_player_id;
    }

    private function end_game_if_finished() {
        $result = [];
        if ($this->queen_positions[0] != null)
            $result[] = $this->board->is_piece_surrounded($this->queen_positions[0]);
        if ($this->queen_positions[1] != null)
            $result[] = $this->board->is_piece_surrounded($this->queen_positions[1]);

        // Neither queen surrounded is game not finished yet
        if (array_sum($result) == 0)
            return False;

        $this->is_game_finished = True;
        // Both queens surrounded is draw
        if (array_sum($result) == 2) 
            $_SESSION["error"] = "Both queens surrounded, game finished in a draw";

        // White"s queen surrounded is black win 
        elseif ($result[0]) 
            $_SESSION["error"] = "White's queen surrounded, black wins";

        // Black"s queen surrounded is white win
        else 
            $_SESSION["error"] = "Black's queen surrounded, white wins";
        return True;
    }
}

?>