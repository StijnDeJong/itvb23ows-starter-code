<?php

namespace tests\ai;

use database\DatabaseService;
use ai\AiService;
use objects\Game;
use objects\Player;
use objects\Board;
use controllers\PassController;
use controllers\RestartController;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertEquals;

class AiServiceTest extends TestCase {

    private AiService $ai_service;
    private RestartController $restart_controller;
    
    public function __construct(?string $name = null, array $data = [], $dataName = "") {
        parent::__construct($name, $data, $dataName);
        $this->database = new DatabaseService();
        $this->ai_service = new AiService($this->database);
        $this->restart_controller = new RestartController($this->database);
    }

    public static function setUpBeforeClass(): void {        
        echo "AiService tests:";
        echo "\n";
    }
    public static function tearDownAfterClass(): void {
        echo "\n";
    }

    public function test_ai_connection() {
        $this->restart_controller->restart();
        assertTrue($this->ai_service->make_move());
    }

    public function test_make_move_given_ai_move_is_move_gets_made() {
        $this->restart_controller->restart();

        // Create a mock object of AiService
        $mock = $this->getMockBuilder(AiService::class)
                     ->setConstructorArgs([$this->database])
                     ->onlyMethods(['get_move']) // Specify the method to be replaced
                     ->getMock();

        // Set the behavior of the replaced method
        $mock->method('get_move')
             ->willReturn(["play", "Q", "0,0"]);
        
        $move = $mock->make_move();

        $board = unserialize($_SESSION["game"])->get_board()->get_board();
        asort($board);
        $expected_board = [
            "0,0" => [[0, "Q"]]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
    public function test_make_move_given_ai_move_is_ilegal_is_move_gets_made() {
        $this->restart_controller->restart();

        // Create a mock object of AiService
        $mock = $this->getMockBuilder(AiService::class)
                     ->setConstructorArgs([$this->database])
                     ->onlyMethods(['get_move']) // Specify the method to be replaced
                     ->getMock();

        // Set the behavior of the replaced method
        $mock->method('get_move')
             ->willReturn(["play", "Q", "50,50"]);
        
        $move = $mock->make_move();

        $board = unserialize($_SESSION["game"])->get_board()->get_board();
        asort($board);
        $expected_board = [
            "50,50" => [[0, "Q"]]
        ];
        asort($expected_board);
        assertEquals($board, $expected_board);
    }
}
?>