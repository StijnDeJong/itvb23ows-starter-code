<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class MoveController extends Controller {

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);

    }

    public function move($from, $to) {
        $this->load_game_from_session();

        if (!$this->is_valid_move($from, $to))
            return;
        $this->game->move($from, $to);

        $this->save_game_to_session();
        $this->database_service->move($from, $to);
    }

    protected function is_valid_move($from, $to) {
        return $this->conforms_general_move_rules($from, $to)
        //  && conforms_piece_specific_move_rules()
         ;
    }
    
    protected function conforms_general_move_rules($from, $to) {    
        $board = $this->game->get_board();
        $active_player = $this->game->get_active_player();
        $active_player_id = $this->game->get_active_player_id();

        if ($this->needs_to_play_queen())
            $_SESSION['error'] = 'Must play queen bee';
        elseif (!$board->is_position_occupied($from))
            $_SESSION['error'] = 'From position is empty';
        elseif (!$active_player->has_played_queen())
            $_SESSION['error'] = "Queen bee has not been played yet";
        elseif (!$board->has_neighBour($to, $from))
            $_SESSION['error'] = "Move breaks the hive";
        elseif ($this->would_move_split_hive($from)) 
            $_SESSION['error'] = "Move would split hive";
        elseif ($board->get_id_of_owner_of_piece($from) != $active_player_id)
            $_SESSION['error'] = "Piece is not owned by player";
        else
            return TRUE;
        return FALSE;
    }

    // abstract function conforms_piece_specific_move_rules();

    protected function would_move_split_hive($from) {
        $board = $this->game->get_board();
        $occupied_positions = array_keys($board->get_board());

        // Removes piece from the board so we can check whether the hive is split when mving it
        unset($occupied_positions[$from]);
        $queue = [array_shift($occupied_positions)];

        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach ($GLOBALS['OFFSETS'] as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $occupied_positions)) {
                    $queue[] = "$p,$q";
                    $occupied_positions = array_diff($occupied_positions, ["$p,$q"]);
                }
            }
        }
        return !empty($occupied_positions);    
    } 
}
