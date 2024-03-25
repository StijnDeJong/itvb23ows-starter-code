<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class GrasshopperMoveController extends MoveController {
    // Inherited attributes: 
    //     $database_service
    //     $game

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }

    public function conforms_piece_specific_move_rules($from, $to) {
        $board = $this->game->get_board();
        if ($board->is_position_occupied($to))
            $_SESSION["error"] = "To position is occupied";
        elseif (!in_array($to, $this->get_grasshopper_moves($from)))
            $_SESSION["error"] = "Not a valid grasshopper move";
        else
            return True;
        return False;
    }

    private function get_grasshopper_moves($position) {
        $board = $this->game->get_board();
        $position = explode(",", $position);
        $moves = [];       
        foreach ($GLOBALS["OFFSETS"] as $pq) {
            $p = $position[0] + $pq[0];
            $q = $position[1] + $pq[1];
            $has_neighbour_in_direction = False;

            while ($board->is_position_occupied($p.",".$q)) {
                $p += $pq[0];
                $q += $pq[1];
                $has_neighbour_in_direction = True;
            }
            if ($has_neighbour_in_direction) {
                $moves[] = $p.",".$q;
            }
        }
        return $moves;    
    }

    public function does_piece_have_moves($grasshoppper_position) {
        $this->load_game_from_session();
        if ($this->would_move_split_hive($grasshoppper_position))
            return False;
        if (count($this->get_grasshopper_moves($grasshoppper_position)) > 0)
            $_SESSION["message"] = $grasshoppper_position . ': can still move';
        return count($this->get_grasshopper_moves($grasshoppper_position)) > 0;
    }
}
?>