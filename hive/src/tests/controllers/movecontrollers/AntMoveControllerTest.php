<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\movecontrollers\AntMoveController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class AntMoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private AntMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new AntMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "\n";
        echo 'AntMoveController tests:';
        echo "\n";
    }

    public function test_move_given_valid_move_move_gets_made() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('A', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertTrue($this->move_controller->move('0,-1', '0,3'));
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,1' => [[1, 'Q']],
            '0,0' => [[0, 'Q']],
            '0,3' => [[0, 'A']],
            '0,2' => [[1, 'B']]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_occupied_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('A', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,-1', '0,2'));
        assertEquals($_SESSION['error'], 'To position is occupied');
    }
    public function test_move_given_cannot_slide_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'A']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
        ]);
        // Board will look like this, A moves to -
        //   S -
        //  Q A S
        //   Q S
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,0', '1,-1'));
        assertEquals($_SESSION['error'], 'Not a valid ant move');
    }
    public function test_move_given_cannot_slide_during_one_of_the_submoves_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '1,1' => [[0, 'A']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
        ]);
        // Board will look like this, A moves to -
        //   S 
        //  Q - S
        //   Q S A
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('1,1', '0,0'));
        assertEquals($_SESSION['error'], 'Not a valid ant move');
    }
    public function test_move_given_hole_between_from_and_to_is_ant_will_take_the_extra_move_to_go_into_hole_first_is_valid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'Q']],
            '0,-1' => [[1, 'Q']],
            '1,0' => [[1, 'S']],
            '2,0' => [[0, 'A']],
            '1,-2' => [[1, 'S']],
            '-1,0' => [[0, 'S']],
            '-2,1' => [[1, 'B']],
            '0,1' => [[0, 'B']],
        ]);
        // Board will look like this, A moves to -
        //      
        //    Q   S
        // - S Q S A
        //  B   B
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertTrue($this->move_controller->move('2,0', '-2,0'));
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,0' => [[0, 'Q']],
            '0,-1' => [[1, 'Q']],
            '1,0' => [[1, 'S']],
            '-2,0' => [[0, 'A']],
            '1,-2' => [[1, 'S']],
            '-1,0' => [[0, 'S']],
            '-2,1' => [[1, 'B']],
            '0,1' => [[0, 'B']],
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
}
?>