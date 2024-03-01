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

    public function __construct() {
        $this->players = [new Player(), new Player()];
        $this->active_player_id = 0;
        $this->board = new Board();
        $this->turn_number = 1;
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

    public function play($piece, $to) {
        $this->board->place_piece($piece, $to, $this->active_player_id);
        $this->get_active_player()->remove_piece($piece);
        $this->advance_turn();
    }

    public function move($from, $to) {
        $this->board->move($from, $to);
        $this->advance_turn();
    }

    public function pass() {
        $this->advance_turn();
    }

    private function advance_turn() {
        $this->switch_active_player();
        $this->turn_number++;
    }

    private function switch_active_player() {
        $this->active_player_id = 1 - $this->active_player_id;
    }
}

?>