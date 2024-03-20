<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\movecontrollers\SpiderMoveController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class SpiderMoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private SpiderMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new SpiderMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "\n";
        echo 'SpiderMoveController tests:';
        echo "\n";
    }
    public function test_move_given_valid_move_move_gets_made() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('S', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertTrue($this->move_controller->move('0,-1', '-1,2'));
        // Board will look like this, S moves to -
        //   S
        //    Q  
        //     Q
        //    - B
        $board = unserialize($_SESSION['game'])->get_board()->get_board();
        asort($board);
        $expected_board = [
            '0,1' => [[1, 'Q']],
            '0,0' => [[0, 'Q']],
            '-1,2' => [[0, 'S']],
            '0,2' => [[1, 'B']]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_position_1_step_away_is_spider_cannot_repeat_moves_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('S', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,-1', '-1,1'));
        // Board will look like this, S moves to -
        //   S
        //  - Q  
        //     Q
        //      B
        assertEquals($_SESSION['error'], 'Not a valid spider move');
    }
    public function test_move_given_position_4_steps_away_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('S', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,-1', '-1,3'));
        // Board will look like this, S moves to -
        //   S
        //    Q  
        //     Q
        //      B
        //     -
        assertEquals($_SESSION['error'], 'Not a valid spider move');
    }
    public function test_move_given_occupied_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        $this->play_controller->play('S', '0,-1');
        $this->play_controller->play('B', '0,2');
        assertFalse($this->move_controller->move('0,-1', '0,1'));
        assertEquals($_SESSION['error'], 'To position is occupied');
    }
    public function test_move_given_cannot_slide_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'S']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'A']],
            '1,0' => [[0, 'A']],
            '0,-1' => [[0, 'A']],
        ]);
        // Board will look like this, A moves to -
        //   A
        //  Q S A -
        //   Q A
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,0', '2,0'));
        assertEquals($_SESSION['error'], 'Not a valid spider move');
    }
    public function test_move_given_cannot_slide_during_one_of_the_submoves_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '2,0' => [[0, 'S']],
            '-1,0' => [[1, 'Q']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'A']],
            '1,0' => [[0, 'A']],
            '0,-1' => [[0, 'A']],
        ]);
        // Board will look like this, A moves to -
        //   A 
        //  Q - A S
        //   Q A
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('2,0', '0,0'));
        assertEquals($_SESSION['error'], 'Not a valid spider move');
    }
    public function test_move_given_hole_between_from_and_to_is_4_steps_away_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'Q']],
            '0,-1' => [[1, 'Q']],
            '1,0' => [[1, 'A']],
            '3,-2' => [[0, 'S']],
            '2,-1' => [[1, 'A']],
            '-1,0' => [[0, 'A']],
            '-2,1' => [[1, 'B']],
            '0,1' => [[0, 'B']],
        ]);
        // Board will look like this, S moves to -
        //         S
        //  - Q   A 
        //   A Q A 
        //  B   B
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('3,-2', '-1,-1'));
        assertEquals($_SESSION['error'], 'Not a valid spider move');
    }
}
?>