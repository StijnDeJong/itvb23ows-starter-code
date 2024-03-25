<?php

namespace objects;

class Board 
{
    private array $board;
    
    public function __construct() {
        $this->board = [];
    }

    public function set_board($board) {
        $this->board = $board;
    }

    public function get_board() {
        return $this->board;
    }

    public function place_piece($piece, $to, $active_player_number) {
        $this->board[$to][] = [$active_player_number, $piece];
    }

    public function move($from, $to) {
        $this->board[$to][] = $this->pop_piece($from);
    }

    public function is_empty() {
        return !$this->board;
    }

    public function get_piece($position) {
        return end($this->board[$position]);
    }

    public function is_piece_surrounded($position) {
        $position = explode(",", $position);
        foreach ($GLOBALS["OFFSETS"] as $pq) {
            $p = $position[0] + $pq[0];
            $q = $position[1] + $pq[1];
            if (!$this->is_position_occupied($p.",".$q))
                return FALSE;
        }
        return True;
    }

    public function pop_piece($position) {
        $top_piece = array_pop($this->board[$position]);
        if ($this->get_stack_height($position) == 0) unset($this->board[$position]);
        return $top_piece;
    }

    public function is_position_occupied($position) {
        return isset($this->board[$position]);
    }

    public function get_id_of_owner_of_piece($position) {
        return end($this->board[$position])[0];
    }

    public function has_neighbour($a, $excluded = null) {
        $occupied_board_position = array_keys($this->board);
        if ($excluded != null) {
            $key = array_search($excluded, $occupied_board_position);
            unset($occupied_board_position[$key]);
        }
        foreach ($occupied_board_position as $b) {
            if ($this->are_neighbours($a, $b)) return true;
        }
        return FALSE;
    }

    public function are_neighbours($a, $b) {
        $a = explode(",", $a);
        $b = explode(",", $b);

        foreach ($GLOBALS["OFFSETS"] as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($a == [$p, $q])
                return True;
        }
        return FALSE;
    }
    
    public function get_occupied_common_neighbour_count($from, $to) {
        $common = $this->get_common_neighbouring_positions($from, $to);
        return (
            $this->is_position_occupied($common[0])
            + $this->is_position_occupied($common[1])
        );    
    }

    public function get_neighbouring_positions($position) {
        $position = explode(",", $position);
        $neighbour_positions = [];
        foreach ($GLOBALS["OFFSETS"] as $pq) {
            $p = $position[0] + $pq[0];
            $q = $position[1] + $pq[1];
            $neighbour_positions[] = $p.",".$q;
        }
        return $neighbour_positions;
    }

    public function get_common_neighbouring_positions($a, $b) {
        return array_values(
            array_intersect(
                $this->get_neighbouring_positions($a),
                $this->get_neighbouring_positions($b)
            )
        );
    }

    public function are_neighbours_same_color($player_number, $a) {
        foreach ($this->board as $b => $st) {
            if (!$st) continue;
            $c = $st[count($st) - 1][0];
            if ($c != $player_number && $this->are_neighbours($a, $b)) return false;
        }
        return true;
    }

    public function get_move_positions() {
        $positions = [];
        foreach ($GLOBALS["OFFSETS"] as $pq) {
            foreach (array_keys($this->board) as $pos) {
                $pq2 = explode(",", $pos);
                $positions[] = ($pq[0] + $pq2[0]).",".($pq[1] + $pq2[1]);
            }
        }
        $positions = array_unique($positions);
        if (!$positions) $positions[] = "0,0";
        return $positions;
    }

    public function get_play_positions() {
        $occupied_board_position = array_keys($this->board);
        $play_positions = array_diff($this->get_move_positions(), $occupied_board_position);
        return $play_positions;
    }

    public function get_piece_type($position) {
        return end($this->board[$position])[1];
    }

    public function get_stack_height($position) {
        if (isset($this->board[$position]))
            return count($this->board[$position]);
        return 0;
    }

    public function does_neighbour_an_occupied_position($position, $excluded = null) {
        $neighbouring_positions = $this->get_neighbouring_positions($position);
        foreach ($neighbouring_positions as $neighbouring_position) {
            if ($position == $excluded) {
                continue;
            }
            if ($this->is_occupied($neighbouring_position)) {
                return True;
            }
        }
        return False;
    }
}

?>