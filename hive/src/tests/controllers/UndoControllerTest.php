<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\UndoController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class UndoControllerTest extends TestCase {

    private UndoController $undo_controller;
    private DatabaseService $database;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->database = $database;
        $this->undo_controller = new UndoController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "\n";
        echo 'UndoController tests:';
        echo "\n";
    }
    public function test_undo_given_start_of_game_cannot_undo() {
        $this->restart_controller->restart();
        $this->undo_controller->undo();
        assertEquals($_SESSION['error'], 'Cannot undo at the start of the game');
    }
    public function test_undo_given_not_start_of_game_can_undo() {
        $this->restart_controller->restart();
        $play_controller = new PlayController($this->database);

        assertEquals(unserialize($_SESSION['game'])->get_board()->get_board(), []);

        $play_controller->play('Q', '0,0');

        assertEquals(unserialize($_SESSION['game'])->get_board()->get_board(), ['0,0' => [[0, 'Q']]]);

        $this->undo_controller->undo();

        assertEquals(unserialize($_SESSION['game'])->get_board()->get_board(), []);
    }
}
?>