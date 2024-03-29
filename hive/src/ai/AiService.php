<?php

namespace ai;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player; 

class AiService {

    private DatabaseService $database_service;
    private string $url;

    public function __construct(DatabaseService $database_service) {
        $this->database_service = $database_service;
        $this->url = "http://hive-ai:5000/";
    }

    public function make_move() {
        $game = unserialize($_SESSION["game"]);
        $move = $this->get_move();
        if ($move == NULL) {            
            $_SESSION["error"] = "Could not establish a connection with the AI server. Server might be offline";
            var_dump($_SESSION["error"]);
            return False;
        }
        $move_type = $move[0];
        switch ($move_type) {
            case "play":
                $game->play($move[1],$move[2]);
                $this->database_service->play($move[1],$move[2]);
                break;

            case "move":
                $game->move($move[1],$move[2]);
                $this->database_service->move($move[1],$move[2]);
                break;

            case "pass":
                $game->pass();
                $this->database_service->pass();
                break;

            default:
                $_SESSION["error"] = "AI encountered an error";
                var_dump($_SESSION["error"]);
                return False;
        }
        $_SESSION["game"] = serialize($game);
        return True;
    }
    
    public function get_move() {

        $game = unserialize($_SESSION["game"]);
        $data = [];
        // Ai starts on move 0 while the game starts on 1 so we subtract 1
        $data["move_number"] = $game->get_turn_number() - 1;
        $data["hand"] = [
            $game->get_player_white()->get_hand(),
            $game->get_player_black()->get_hand()
        ];
        $data["board"] = $game->get_board()->get_board();
        
        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        // Throws warning when it cannot reach the ai server, result will then be null
        $result = @file_get_contents($this->url, false, $context);
        $result = json_decode($result);
        
        return $result;        
    }
}
?>
