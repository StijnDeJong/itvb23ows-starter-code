<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\PassController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class PassControllerTest extends TestCase {

    private PassController $pass_controller;
    
    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->pass_controller = new PassController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo 'PassController tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }

    public function test_needs_to_play_queen_given_queenless_board_at_turn_7_returns_true() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->set_turn_number(7);
        $_SESSION['game'] = serialize($game);
        $this->pass_controller->pass();
        assertEquals($_SESSION['error'], 'Must play queen bee');
    }
    public function test_needs_to_play_queen_given_opponents_queen_board_at_turn_7_returns_true() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->play('Q', '0,0');
        $game->set_turn_number(7);     
        $_SESSION['game'] = serialize($game);
        $this->pass_controller->pass();
        assertEquals($_SESSION['error'], 'Must play queen bee');
    }
    public function test_needs_to_play_queen_given_queenless_board_at_turn_6_returns_false() {
        $this->restart_controller->restart();
        $game = unserialize($_SESSION['game']);
        $game->play('Q', '0,0');
        $game->set_turn_number(6);     
        $_SESSION['game'] = serialize($game);
        $this->pass_controller->pass();
        assertFalse(isset($_SESSION['error']));
    }
}
?>