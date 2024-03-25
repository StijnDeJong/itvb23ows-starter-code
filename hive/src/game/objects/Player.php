<?php

namespace objects;

class Player 
{
    private array $hand;

    public function __construct() {
        $this->hand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
    }

    public function get_hand() {
        return $this->hand;
    }

    public function set_hand($hand) {
        $this->hand = $hand;
    }

    public function remove_piece($piece) {
        $this->hand[$piece]--;
    }

    public function has_piece_in_hand($piece) {
        return $this->hand[$piece] == 0;
    }

    public function is_hand_full() {
        return $this->hand == ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
    }

    public function get_piece_count($piece) {
        return $this->hand[$piece];
    }

    public function has_played_queen() {
        return $this->hand["Q"] == 0;
    }

    public function has_pieces_left() {
        foreach ($this->hand as $piece => $count) {
            if ($count > 0)
                return True;
        }
        return False;
    }

}

?>