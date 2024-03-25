<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class QueenMoveController extends MoveController {
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
        elseif (!$board->are_neighbours($from, $to))
            $_SESSION["error"] = "To position does not neighbour from position";
        else {
            $common_occupied_neighbours = $board->get_occupied_common_neighbour_count($from, $to);
            if ($common_occupied_neighbours == 0)
                $_SESSION["error"] = "Piece loses contact with the hive during movement";
            elseif ($common_occupied_neighbours == 2)
                $_SESSION["error"] = "Tile must slide";
            else
                return True;
        }
        return False;
    }

    public function does_piece_have_moves($queen_position) {
        $this->load_game_from_session();
        if ($this->would_move_split_hive($queen_position))
            return False;
        $board = $this->game->get_board();
        $neighbouring_positions = $board->get_neighbouring_positions($queen_position);
        foreach ($neighbouring_positions as $position) {
            if ($board->is_position_occupied($position))
                continue;
            
            $common_occupied_neighbours = $board->get_occupied_common_neighbour_count($queen_position, $position);
            if ($common_occupied_neighbours == 1)
                $_SESSION["message"] = $position + ': can still move';
            if ($common_occupied_neighbours == 1)
                return True;
        }
        return False;
    }
}
?>