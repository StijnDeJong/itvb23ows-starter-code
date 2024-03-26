<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

abstract class MoveController extends Controller {
    // Inherited attributes: 
    //     $database_service
    //     $game

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);
    }

    public function move($from, $to) {
        $this->load_game_from_session();

        if (!$this->is_valid_move($from, $to))
            return False;
        $this->game->move($from, $to);

        $this->save_game_to_session();
        $this->database_service->move($from, $to);
        return True;
    }

    protected function is_valid_move($from, $to) {
        return (
            $this->conforms_general_move_rules($from, $to)
            && $this->conforms_piece_specific_move_rules($from, $to)
        );
    }
    
    protected function conforms_general_move_rules($from, $to) {
        $board = $this->game->get_board();
        $active_player = $this->game->get_active_player();
        $active_player_id = $this->game->get_active_player_id();

        if ($this->needs_to_play_queen())
            $_SESSION["error"] = "Must play queen bee";
        elseif ($from == $to)
            $_SESSION["error"] = "From cannot equal to";
        elseif (!$board->is_position_occupied($from))
            $_SESSION["error"] = "From position is empty";
        elseif (!$active_player->has_played_queen())
            $_SESSION["error"] = "Queen bee has not been played yet";
        elseif (
            !$board->has_neighbour($to, $from) 
            && !$board->is_position_occupied($to) 
            && $board->get_stack_height($from) < 2
        )
            $_SESSION["error"] = "Piece moved outside the hive";
        elseif ($this->would_move_split_hive($from)) 
            $_SESSION["error"] = "Move would split hive";
        elseif ($board->get_id_of_owner_of_piece($from) != $active_player_id)
            $_SESSION["error"] = "Piece is not owned by player";
        else
            return TRUE;
        return FALSE;
    }

    abstract protected function conforms_piece_specific_move_rules($from, $to);

    protected function would_move_split_hive($from) {
        $board = $this->game->get_board();
        $occupied_positions = array_keys($board->get_board());

        // Removes piece from the board so we can check whether the hive is split when moving it,
        //  but not when moving down a stack
        if ($board->get_stack_height($from) < 2)
            $occupied_positions = array_diff($occupied_positions, array($from));

        $queue = [array_shift($occupied_positions)];

        while ($queue) {
            $next = explode(",", array_shift($queue));
            foreach ($GLOBALS["OFFSETS"] as $pq) {
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

    // Get single steps for multi-step pieces like ant and spider
    protected function get_sub_moves($position, $excluded = []) {
        $board = $this->game->get_board();
        $neighbouring_positions = $board->get_neighbouring_positions($position);
        $sub_moves = [];
        foreach ($neighbouring_positions as $neighbouring_position) {
            if (in_array($neighbouring_position, $excluded)) {
                continue;
            }
            if ($board->is_position_occupied($neighbouring_position)) {
                continue;
            }
            if ($board->get_occupied_common_neighbour_count($position, $neighbouring_position) != 1) {
                continue;
            }
            $sub_moves[] = $neighbouring_position;
        }
        return $sub_moves;
    }
}
