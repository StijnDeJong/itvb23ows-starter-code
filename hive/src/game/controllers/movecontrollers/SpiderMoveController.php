<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class SpiderMoveController extends MoveController {
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
        elseif (!in_array($to, $this->get_spider_moves($from)))
            $_SESSION["error"] = "Not a valid spider move";
        else
            return True;
        return False;
    }

    private function get_spider_moves($position, $iteration = 3, $previous_moves=[]) {
        if ($iteration == 0)
            return [$position];

        $board = $this->game->get_board();
        if ($iteration == 3)
            $board->pop_piece($position);

        $previous_moves[] = $position;
        $moves = [];

        $sub_moves = $this->get_sub_moves($position, $previous_moves);
        foreach ($sub_moves as $sub_move) {
            $moves = array_merge($moves, $this->get_spider_moves($sub_move, $iteration-1, $previous_moves));
        }
        
        if ($iteration == 3)
            $this->load_game_from_session();
        return $moves;
    }

    public function does_piece_have_moves($spider_position) {
        $this->load_game_from_session();
        if ($this->would_move_split_hive($spider_position))
            return False;
        if (count($this->get_spider_moves($spider_position)) > 0)
            $_SESSION["message"] = $spider_position . ': can still move';
        return count($this->get_spider_moves($spider_position)) > 0;
    }
}
?>