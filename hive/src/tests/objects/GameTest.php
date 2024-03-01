<?php

namespace tests\objects;

use objects\Game;
use objects\Player;
use objects\Board;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class GameTest extends TestCase {

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
}
?>