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
    
    public function __construct(?string $name = null, array $data = [], $dataName = "") {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->pass_controller = new PassController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {        
        echo "PassController tests:";
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }

    public function test_pass_given_no_moves_is_valid() {
        assertFalse(isset($_SESSION["error"]));
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "-1,0" => [[1, "Q"]],
            "0,0" => [[0, "Q"]],
            "1,0" => [[1, "B"]],
        ]);
        // Board will look like this
        //   
        //    Q Q B
        //
        $game->get_player_white()->remove_piece("Q");
        $_SESSION["game"] = serialize($game);

        assertTrue($this->pass_controller->pass());
    }

    public function test_pass_given_playable_piece_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "-1,0" => [[1, "Q"]],
            "0,0" => [[0, "Q"]],
            "1,0" => [[0, "B"]],
            "2,0" => [[1, "B"]],
        ]);
        // Board will look like this
        //   
        //    Q Q B B
        //
        $game->get_player_white()->remove_piece("Q");
        $_SESSION["game"] = serialize($game);

        assertFalse($this->pass_controller->pass());
        assertEquals($_SESSION["error"], "Can only pass in stalemate");
    }

    public function test_pass_given_moveable_piece_is_invalid() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION["game"]);
        $game->get_board()->set_board([
            "-1,0" => [[1, "Q"]],
            "0,0" => [[0, "Q"]],
            "1,0" => [[1, "B"]],
            "2,0" => [[0, "G"]],
        ]);
        // Board will look like this
        //   
        //    Q Q B B
        //
        $game->get_player_white()->set_hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]);
        $_SESSION["game"] = serialize($game);

        assertFalse($this->pass_controller->pass());
        assertEquals($_SESSION["error"], "Can only pass in stalemate");
    }
}
?>