<?php

namespace tests\controllers;

use database\DatabaseService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\movecontrollers\GrasshopperMoveController;
use controllers\PlayController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class GrasshopperMoveControllerTest extends TestCase {

    private PlayController $playcontroller;
    private GrasshopperMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = "") {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->play_controller = new PlayController($database);
        $this->move_controller = new GrasshopperMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "GrasshopperMoveController tests:";
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }
    
    public function test_move_given_valid_move_move_gets_made() {
        $this->restart_controller->restart();
        $this->play_controller->play("Q", "0,0");
        $this->play_controller->play("Q", "0,1");
        $this->play_controller->play("G", "0,-1");
        $this->play_controller->play("B", "0,2");
        assertTrue($this->move_controller->move("0,-1", "0,3"));
        $board = unserialize($_SESSION["game"])->get_board()->get_board();
        asort($board);
        $expected_board = [
            "0,1" => [[1, "Q"]],
            "0,0" => [[0, "Q"]],
            "0,3" => [[0, "G"]],
            "0,2" => [[1, "B"]]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_move_given_occupied_to_position_is_invalid() {
        $this->restart_controller->restart();
        $this->play_controller->play("Q", "0,0");
        $this->play_controller->play("Q", "0,1");
        $this->play_controller->play("G", "0,-1");
        $this->play_controller->play("B", "0,2");
        assertFalse($this->move_controller->move("0,-1", "0,2"));
        assertEquals($_SESSION["error"], "To position is occupied");
    }
    public function test_move_given_move_does_not_jump_over_a_piece_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "0,-1" => [[0, "G"]],
            "0,0" => [[1, "Q"]],
            "0,1" => [[0, "Q"]],
        ]);
        // Board will look like this, G moves to -
        //   G
        //    Q
        //     Q
        //      -
        $game->get_player_white()->remove_piece("Q");
        $_SESSION["game"] = serialize($game);

        assertFalse($this->move_controller->move("0,-1", "1,-1"));
        assertEquals($_SESSION["error"], "Not a valid grasshopper move");
    }

    public function test_move_given_move_jumps_over_a_hole_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "-2,0" => [[0, "G"]],
            "-1,0" => [[1, "Q"]],
            "-1,1" => [[0, "Q"]],
            "0,1" => [[1, "B"]],
            "1,0" => [[0, "B"]],
            "0,-1" => [[0, "B"]],
        ]);
        // Board will look like this, G moves to -
        //   
        //  G Q   B -
        //     Q B
        $game->get_player_white()->remove_piece("Q");
        $_SESSION["game"] = serialize($game);

        assertFalse($this->move_controller->move("-2,0", "2,0"));
        assertEquals($_SESSION["error"], "Not a valid grasshopper move");
    }

    public function test_move_given_move_towards_an_empty_position_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "-1,0" => [[0, "G"]],
            "-1,1" => [[0, "Q"]],
            "0,1" => [[1, "q"]],
            "1,0" => [[1, "B"]],
        ]);
        // Board will look like this, G moves to -
        //   
        //  G   B -
        //   Q B
        $game->get_player_white()->remove_piece("Q");
        $_SESSION["game"] = serialize($game);

        assertFalse($this->move_controller->move("-1,0", "2,0"));
        assertEquals($_SESSION["error"], "Not a valid grasshopper move");
    }
}
?>