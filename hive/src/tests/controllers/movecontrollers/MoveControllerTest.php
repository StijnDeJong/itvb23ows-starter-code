<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\movecontrollers\QueenMoveController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class MoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private MoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new QueenMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo 'MoveController tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }
    
    public function test_move_given_valid_move_move_gets_made() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertTrue($this->move_controller->move('0,0', '1,0'));
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,1' => [[1, 'Q']],
            '1,0' => [[0, 'Q']]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_empty_from_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('1,0', '1,1'));
        assertEquals($_SESSION['error'], 'From position is empty');
    }
    public function test_move_given_no_queen_played_by_active_player_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('B', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('0,0', '1,0'));
        assertEquals($_SESSION['error'], "Queen bee has not been played yet");
    }
    public function test_move_given_move_outside_hive_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('0,0', '0,-1'));
        assertEquals($_SESSION['error'], "Piece moved outside the hive");
    }
    public function test_move_given_move_splits_hive_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('B', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,0', '1,0'));
        assertEquals($_SESSION['error'], "Move would split hive");
    }
    public function test_move_given_player_moves_opponents_piece_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('0,1', '1,0'));
        assertEquals($_SESSION['error'], "Piece is not owned by player");
    }

    
}
?>