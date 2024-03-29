<?php

namespace database;
use mysqli;

class DatabaseService
{
    private $database;

    public function __construct() {
        $this->database = new mysqli('db', 'root', 'root', 'hive_db', '3306');
    }

    public function get_state() {
        return $_SESSION["game"];
    }

    public function set_state($state) {
        $_SESSION['game'] = $state;
    }

    public function start() {
        $state = $this->get_state();
        $stmt = $this->database->prepare(
            'insert into moves (game_id, type, move_from, move_to, previous_id, state)
             values (?, "init", null, null, null, ?)'
        );
        $stmt->bind_param('is', $_SESSION['game_id'], $state);
        $stmt->execute();
        $_SESSION['last_move'] = $this->database->insert_id;
    }

    public function pass() {
        $state = $this->get_state();
        $stmt = $this->database->prepare(
            'insert into moves (game_id, type, move_from, move_to, previous_id, state)
             values (?, "pass", null, null, ?, ?)'
        );
        $stmt->bind_param('iis', $_SESSION['game_id'], $_SESSION['last_move'], $state);
        $stmt->execute();
        $_SESSION['last_move'] = $this->database->insert_id;
    }

    public function play($piece, $to) {
        $state = $this->get_state();
        $stmt = $this->database->prepare(
            'insert into moves (game_id, type, move_from, move_to, previous_id, state)
             values (?, "play", ?, ?, ?, ?)'
        );
        $stmt->bind_param('issis', $_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], $state);
        $stmt->execute();
        $_SESSION['last_move'] = $this->database->insert_id;
    }

    public function move($from, $to) {
        $state = $this->get_state();
        $stmt = $this->database->prepare(
            'insert into moves (game_id, type, move_from, move_to, previous_id, state)
             values (?, "move", ?, ?, ?, ?)'
        );
        $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $_SESSION['last_move'], $state);
        $stmt->execute();
        $_SESSION['last_move'] = $this->database->insert_id;
    }

    public function undo() {
        $previous_last_id = $this->get_previous_last_id();
        $this->delete_last_row();
        $stmt = $this->database->prepare('SELECT * FROM moves WHERE id = ?');
        $stmt->bind_param('i', $previous_last_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        $this->set_state($result[6]);
        $_SESSION['last_move'] = $previous_last_id;
    } 

    private function delete_last_row() {
        $last_id = $_SESSION['last_move'];
        $stmt = $this->database->prepare('DELETE FROM moves WHERE id = ?');
        $stmt->bind_param('i', $last_id);
        $stmt->execute();
    }

    /**
     * Returns id of turn 2 turns ago
     */
    private function get_previous_last_id() {
        $stmt = $this->database->prepare('SELECT previous_id FROM moves WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['last_move']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array()[0];
    }

    public function get_previous_turns() {
        $stmt = $this->database->prepare('SELECT * FROM moves WHERE game_id = ? AND NOT type = "init"');
        $stmt->bind_param('i', $_SESSION['game_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function get_last_game_id()
    {
        $this->database->prepare('INSERT INTO games VALUES ()')->execute();
        return $this->database->insert_id;
    }
}

?>