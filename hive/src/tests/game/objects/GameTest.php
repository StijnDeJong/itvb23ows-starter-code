<?php

namespace tests\objects;

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

class GameTest extends TestCase {

    private AntMoveController $movecontroller;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $database = new DatabaseService();
        $this->move_controller = new AntMoveController($database);
        $this->restart_controller = new RestartController($database);
    }

    public static function setUpBeforeClass(): void {
        echo 'Game tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }
    

    public function test_play_given_game_plays_piece() {
        $game = new Game();
        $game->play('Q', '0,0');

        assertEquals($game->get_board()->pop_piece('0,0'), [0, 'Q']);
    }
    public function test_play_given_game_removes_piece_from_hand() {
        $game = new Game();
        $player = $game->get_player_white();

        assertTrue($player->get_piece_count('Q') == 1);

        $game->play('Q', '0,0');

        assertTrue($player->get_piece_count('Q') == 0);
    }
    public function test_play_given_game_advances_turn_number() {
        $game = new Game();

        assertTrue($game->get_turn_number() == 1);

        $game->play('Q', '0,0');

        assertTrue($game->get_turn_number() == 2);
    }
    public function test_play_given_game_switches_active_player() {
        $game = new Game();

        assertTrue($game->get_active_player_id() == 0);

        $game->play('Q', '0,0');

        assertTrue($game->get_active_player_id() == 1);

        $game->play('Q', '0,1');

        assertTrue($game->get_active_player_id() == 0);
    }
    public function test_end_game_if_finished_given_black_queen_is_surrounded_is_white_win() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '1,-1' => [[0, 'S']],
            '0,0' => [[1, 'Q']],
            '2,0' => [[0, 'A']],
            '-1,1' => [[0, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
        ]);
        // Board will look like this, A moves to -
        //   S S
        //  - Q S A
        //   Q S
        $game->get_player_white()->remove_piece('Q');
        $game->set_queen_positions(['-1,1', '0,0']);
        $_SESSION['game'] = serialize($game);

        assertTrue($this->move_controller->move('2,0', '-1,0'));
        assertEquals($_SESSION['error'], "Black's queen surrounded, white wins");
    }
    public function test_end_game_if_finished_given_white_queen_is_surrounded_is_black_win() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '1,-1' => [[0, 'S']],
            '0,0' => [[0, 'Q']],
            '2,0' => [[0, 'A']],
            '-1,1' => [[1, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
        ]);
        // Board will look like this, A moves to -
        //   S S
        //  - Q S A
        //   Q S
        $game->get_player_white()->remove_piece('Q');
        $game->set_queen_positions(['0,0', '-1,1']);
        $_SESSION['game'] = serialize($game);

        assertTrue($this->move_controller->move('2,0', '-1,0'));
        assertEquals($_SESSION['error'], "White's queen surrounded, black wins");
    }
    public function test_end_game_if_finished_given_both_queens_are_surrounded_is_draw() {
        $this->restart_controller->restart();

        $game = unserialize($_SESSION['game']);
        $game->get_board()->set_board([
            '1,-1' => [[0, 'S']],
            '0,0' => [[0, 'Q']],
            '2,0' => [[0, 'A']],
            '-1,1' => [[1, 'Q']],
            '0,1' => [[1, 'S']],
            '1,0' => [[0, 'S']],
            '0,-1' => [[0, 'S']],
            '-2,2' => [[1, 'B']],
            '-1,2' => [[1, 'B']],
            '-2,1' => [[1, 'B']],
        ]);
        // Board will look like this, A moves to -
        //    S S
        //   - Q S A
        //  B Q S
        //   B B
        $game->get_player_white()->remove_piece('Q');
        $game->set_queen_positions(['0,0', '-1,1']);
        $_SESSION['game'] = serialize($game);

        assertTrue($this->move_controller->move('2,0', '-1,0'));
        assertEquals($_SESSION['error'], "Both queens surrounded, game finished in a draw");
    }
}
?>