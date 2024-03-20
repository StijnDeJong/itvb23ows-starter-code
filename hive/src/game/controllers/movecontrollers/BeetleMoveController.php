<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class BeetleMoveController extends MoveController {
    // Inherited attributes: 
    //     $database_service
    //     $game

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }

    public function conforms_piece_specific_move_rules($from, $to) {
        $board = $this->game->get_board();
        if (!$board->are_neighbours($from, $to))
            $_SESSION['error'] = 'To position does not neighbour from position';
        else {
            $common_occupied_neighbours = $board->get_occupied_common_neighbour_count($from, $to);
            if ($common_occupied_neighbours == 0 && !$board->is_position_occupied($to))
                $_SESSION['error'] = "Piece loses contact with the hive during movement";
            elseif ($common_occupied_neighbours == 2 && !$this->can_beetle_slide($from, $to))
                $_SESSION['error'] = 'Tile must slide';
            else
                return True;
        }
        return False;
    }

    private function can_beetle_slide($from, $to) {
        $board = $this->game->get_board();
        $common = $board->get_common_neighbouring_positions($from, $to);

        $stack_heights = [];
        foreach (array_merge([$from, $to], $common) as $position) {
            $stack_heights[] = $board->get_stack_height($position);
        }
        list($a, $b, $c, $d) = $stack_heights;
        return max($a -1, $b) >= min($c, $d);
    
    }   
}
?>