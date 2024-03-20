<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class AntMoveController extends MoveController {
    // Inherited attributes: 
    //     $database_service
    //     $game
    
    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }

    public function conforms_piece_specific_move_rules($from, $to) {
        $board = $this->game->get_board();        
        if ($board->is_position_occupied($to))
            $_SESSION['error'] = 'To position is occupied';
        elseif (!in_array($to, $this->get_ant_moves($from)))
            $_SESSION['error'] = 'Not a valid ant move';
        else
            return True;
        return False;
    }

    private function get_ant_moves($position) {
        $board = $this->game->get_board();
        $moves = [$position];
        $queue = [$position];
        $board->pop_piece($position);
        while ($queue) {
            $position = array_shift($queue);
            $sub_moves = $this->get_sub_moves($position, $moves);
            $moves = array_merge($moves, $sub_moves);
            $queue = array_merge($queue, $sub_moves);
        }
        // Undo the popping of ant
        $this->load_game_from_session();
        // Remove the first position as it would otherwise allow passing the turn
        array_shift($moves);
        return $moves;    
    } 

}
?>