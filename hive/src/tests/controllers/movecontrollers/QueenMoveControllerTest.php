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

class QueenMoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private QueenMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new QueenMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "\n";
        echo 'QueenMoveController tests:';
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
    public function test_move_given_occupied_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('0,0', '0,1'));
        assertEquals($_SESSION['error'], 'To position is occupied');
    }
    public function test_move_given_non_neighbouring_from_and_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play('Q', '0,0');
        $this->play_controller->play('Q', '0,1');
        assertFalse($this->move_controller->move('0,0', '0,2'));
        assertEquals($_SESSION['error'], 'To position does not neighbour from position');
    }
    public function test_move_given_move_loses_contact_with_hive_mid_move_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,-1' => [[0, 'Q']],
            '-1,0' => [[1, 'S']],
            '-1,1' => [[0, 'B']],
            '0,1' => [[1, 'B']],
            '1,0' => [[0, 'B']],
        ]);
        // Board will look like this, Q moves to -
        //   Q -
        //  S   B
        //   B B
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,-1', '1,-1'));
        assertEquals($_SESSION['error'], "Piece loses contact with the hive during movement");
    }

    public function test_move_given_cannot_slide_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '0,0' => [[0, 'Q']],
            '-1,0' => [[1, 'S']],
            '-1,1' => [[0, 'B']],
            '0,1' => [[1, 'B']],
            '1,0' => [[0, 'B']],
            '0,-1' => [[0, 'B']],
        ]);
        // Board will look like this, Q moves to -
        //   B -
        //  S Q B
        //   B B
        $game->get_player_white()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);

        assertFalse($this->move_controller->move('0,0', '1,-1'));
        assertEquals($_SESSION['error'], 'Tile must slide');
    }
}
?>