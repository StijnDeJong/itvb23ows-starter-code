<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class PlayControllerTest extends TestCase {

    private PlayController $playcontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo 'PlayController tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }

    public function test_play_given_valid_piece_gets_added_to_board() {
        $this->restart_controller->restart();
        assertTrue($this->play_controller->play('Q', '0,0'));
        assertEquals(unserialize($_SESSION['game'])->get_board()->pop_piece('0,0'), [0, 'Q']);
    }
    public function test_play_given_needs_to_play_queen_plays_queen_is_valid() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->set_turn_number(7);
        $_SESSION['game'] = serialize($game);
        assertTrue($this->play_controller->play('Q', '0,0'));
        assertEquals(unserialize($_SESSION['game'])->get_board()->pop_piece('0,0'), [0, 'Q']);
    }
    public function test_play_given_needs_to_play_queen_does_not_play_queen_is_invalid() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->set_turn_number(7);
        $_SESSION['game'] = serialize($game);
        assertFalse($this->play_controller->play('B', '0,0'));
        assertEquals($_SESSION['error'], 'Must play queen bee');
    }
    public function test_play_given_plays_piece_they_do_not_have_is_invalid() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->get_active_player()->remove_piece('Q');
        $_SESSION['game'] = serialize($game);
        assertFalse($this->play_controller->play('Q', '0,0'));
        assertEquals($_SESSION['error'], "Player does not have piece");
    }
    public function test_play_given_plays_piece_on_occupied_position_is_invalid() {
        $this->restart_controller->restart();
        assertTrue($this->play_controller->play('Q', '0,0'));
        assertFalse($this->play_controller->play('Q', '0,0'));
        assertEquals($_SESSION['error'], 'Board position is already occupied');
    }
    public function test_play_given_plays_piece_outside_hive_is_invalid() {
        $this->restart_controller->restart();
        assertTrue($this->play_controller->play('Q', '0,0'));
        assertFalse($this->play_controller->play('Q', '2,2'));
        assertEquals($_SESSION['error'], "Piece is played outside the hive");
    }
    public function test_play_given_plays_next_to_opponents_piece_is_invalid() {
        $this->restart_controller->restart();
        assertTrue($this->play_controller->play('Q', '0,0'));
        assertTrue($this->play_controller->play('Q', '0,1'));
        assertFalse($this->play_controller->play('B', '0,2'));
        assertEquals($_SESSION['error'], "Board position has opposing neighbour");
    }
}
?>