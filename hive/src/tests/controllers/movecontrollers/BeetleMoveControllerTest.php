<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\movecontrollers\BeetleMoveController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class BeetleMoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private BeetleMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new BeetleMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo 'BeetleMoveController tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }
    
    public function test_move_given_valid_move_move_gets_made() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('B', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertTrue($this->move_controller->move('0,-1', '1,-1'));
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,1' => [[1, 'Q']],
            '0,0' => [[0, 'Q']],
            '1,-1' => [[0, 'B']],
            '0,2' => [[1, 'B']]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_non_neighbouring_from_and_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('B', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,-1', '0,3'));
        assertEquals($_SESSION['error'], 'To position does not neighbour from position');
    }
    public function test_move_given_occupied_to_position_is_beetle_climbs() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('B', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertTrue($this->move_controller->move('0,-1', '0,0'));
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,1' => [[1, 'Q']],
            '0,0' => [[0, 'Q'], [0, 'B']],
            '0,2' => [[1, 'B']]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_move_loses_contact_with_hive_mid_move_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,-1' => [[0, 'B']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
        ]);
        // Board will look like this, B moves to -
        //   B -
        //  Q   S
        //   Q S
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,-1', '1,-1'));
        assertEquals($_SESSION['error'], "Piece loses contact with the hive during movement");
    }

    public function test_move_given_cannot_slide_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'B']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
        ]);
        // Board will look like this, B moves to -
        //   S -
        //  Q B S
        //   Q S
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,0', '1,-1'));
        assertEquals($_SESSION['error'], 'Tile must slide');
    }
    public function test_move_given_can_slide_up_when_only_1_common_neighbouring_stack_is_taller_is_valid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'Q'],[0, 'B']],
            '1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'S'],[1, 'B'],[1, 'B']],
            '0,1' => [[0, 'B']],
        ]);
        // Board will look like this, bottom right B moves to upper B
        //   
        //   B Q
        //  B B
        // 
        // Stack heights for above position
        // 
        //   2 1
        //  3 1
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);
        assertTrue($this->move_controller->move('0,1', '0,0'));

        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,0' => [[0, 'Q'],[0, 'B'],[0, 'B']],
            '1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'S'],[1, 'B'],[1, 'B']],
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_can_slide_up_when_both_common_neighbouring_stacks_are_taller_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q'],[0, 'B']],
            '-1,1' => [[0, 'S'],[1, 'B'],[1, 'B']],
            '0,1' => [[0, 'B']],
        ]);
        // Board will look like this, bottom right B moves to Q
        //   
        //   Q B
        //  B B
        // 
        // Stack heights for above position
        // 
        //   1 2
        //  3 1
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);
        assertFalse($this->move_controller->move('0,1', '0,0'));
        assertEquals($_SESSION['error'], 'Tile must slide');
    }
}
?>