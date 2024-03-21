<?php

namespace tests\objects;

use objects\Board;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class BoardTest extends TestCase {

    public static function setUpBeforeClass(): void {        
        echo 'Board tests:';
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }

    public function test_pop_piece_returns_piece() {
        $board = new Board();
        $board->set_board(['0,0' => [[0, 'Q']]]);
        $piece = $board->pop_piece('0,0');

        assertTrue($piece == [0, 'Q']);
    }
    public function test_pop_piece_returns_only_top_piece() {
        $board = new Board();
        $board->set_board(['0,0' => [[0, 'Q'],[1, 'B']]]);
        $piece = $board->pop_piece('0,0');

        assertTrue($piece == [1, 'B']);
    }
    public function test_pop_piece_removes_piece() {
        $board = new Board();
        $board->set_board(['0,0' => [[0, 'Q']]]);
        $board->pop_piece('0,0');

        assertTrue($board->get_board() == []);
    }
    public function test_pop_piece_removes_only_top_piece() {
        $board = new Board();
        $board->set_board(['0,0' => [[0, 'Q'],[1, 'B']]]);
        $board->pop_piece('0,0');

        assertTrue($board->get_board() == ['0,0' => [[0, 'Q']]]);
    }

    public function test_has_neighbour_given_no_neigbours_equals_false() {
        $board = new Board();
        $board->set_board(['0,0' => [[0, 'Q']]]);
        $board->has_neighbour('0,0');

        assertFalse($board->has_neighbour('0,0'));
    }
    public function test_has_neighbour_given_neigbours_equals_true() {
        $board = new Board();
        $board->set_board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']]
        ]);
        $board->has_neighbour('0,0');

        assertTrue($board->has_neighbour('0,0'));
        assertTrue($board->has_neighbour('0,1'));
    }

    public function test_are_neighbours_given_no_neigbour_equals_false() {
        $board = new Board();
        $board->set_board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '0,-1' => [[0, 'B']]
        ]);
        
        assertFalse($board->are_neighbours('0,1','0,-1'));
        assertFalse($board->are_neighbours('0,-1','0,1'));
    }
    public function test_are_neighbours_given_neigbour_equals_true() {
        $board = new Board();
        $board->set_board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '0,-1' => [[0, 'B']]
        ]);
        
        assertTrue($board->are_neighbours('0,0','0,1'));
        assertTrue($board->are_neighbours('0,0','0,-1'));
    }
    
    public function test_get_common_neighbouring_positions_given_non_neigbouring_positions_returns_empty_list() {
        $board = new Board();
        $a = $board->get_common_neighbouring_positions('0,2','0,-1');
        $b = [];

        asort($a);
        asort($b);
        
        assertEquals($a, $b);
    }
    public function test_get_common_neighbouring_positions_given_neigbouring_positions_returns_correct_positions() {
        $board = new Board();
        $a = $board->get_common_neighbouring_positions('0,0','0,1');
        $b = ['1,0', '-1,1'];

        sort($a);
        sort($b);
        
        assertEquals($a, $b);
    }
    
    public function test_are_neighbours_same_color_given_same_colors_equals_true() {
        $board = new Board();
        $board->set_board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '0,-1' => [[0, 'B']]
        ]);
        
        assertTrue($board->are_neighbours_same_color(0, '0,-1'));
    }
    public function test_are_neighbours_same_color_given_different_colors_equals_false() {
        $board = new Board();
        $board->set_board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '0,-1' => [[0, 'B']]
        ]);
        
        assertFalse($board->are_neighbours_same_color(0, '0,0'));
    }
}    
?>