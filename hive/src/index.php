<?php
    session_start();

    require_once 'vendor/autoload.php';

    use database\DatabaseService;
    use controllers\PassController;
    use controllers\RestartController;
    use controllers\PlayController;
    use controllers\UndoController;
    use controllers\movecontrollers\AntMoveController;
    use controllers\movecontrollers\BeetleMoveController;
    use controllers\movecontrollers\GrasshopperMoveController;
    use controllers\movecontrollers\QueenMoveController;
    use controllers\movecontrollers\SpiderMoveController;
    use objects\Game;
    use objects\Board;
    use objects\Player;
    use ai\AiService;

    $database = new DatabaseService();
    $GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

    // Initial restart to set session vars
    if (!isset($_SESSION['game'])) {
        $restart_controller = new RestartController($database);
        $restart_controller->restart();
    }
    $game = unserialize($_SESSION['game']);

    if (array_key_exists('restart', $_POST)) {
        $restart_controller = new RestartController($database);
        $restart_controller->restart();
    }
    elseif (array_key_exists('undo', $_POST)) {
        $undo_controller = new UndoController($database);
        $undo_controller->undo();
    }
    elseif ($game->is_game_finished())
        $_SESSION['error'] = "Game has ended";

    elseif (array_key_exists('pass', $_POST)) {
        $pass_controller = new PassController($database);
        $pass_controller->pass();
    }
    elseif (array_key_exists('play', $_POST)) {
        if (array_key_exists('piece', $_POST)) {
            $play_controller = new PlayController($database);
            $play_controller->play($_POST['piece'], $_POST['to']);
        } else
            $_SESSION['error'] = 'No piece selected';
    }
    elseif (array_key_exists('move', $_POST)) {
        if (array_key_exists('from', $_POST)) {
            $move_controller = get_movecontroller($_POST['from'], $database);
            $move_controller->move($_POST['from'], $_POST['to']);
        } else
            $_SESSION['error'] = 'No from position selected';
    }
    elseif (array_key_exists('ai', $_POST)) {
        $ai = new AiService($database);
        $ai->make_move();
    }

    $game = unserialize($_SESSION['game']);  
    $board = $game->get_board();
    $player_white = $game->get_player_white();
    $player_black = $game->get_player_black();
    $active_player = $game->get_active_player();
    $active_player_id = $game->get_active_player_id();
    $move_positions = $board->get_move_positions();
    $play_positions = $board->get_play_positions();

    function get_movecontroller($position, $database) {
        $game = unserialize($_SESSION['game']);
        $board = $game->get_board();
        switch ($board->get_piece_type($position)) {
            case 'A':
                return new AntMoveController($database);
            case 'B':
                return new BeetleMoveController($database);
            case 'G':
                return new GrasshopperMoveController($database);
            case 'Q':
                return new QueenMoveController($database);
            case 'S':
                return new SpiderMoveController($database);
            default:
                
        }    
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div class="board">
            <?php
                $min_p = 1000;
                $min_q = 1000;
                foreach ($board->get_board() as $pos => $tile) {
                    $pq = explode(',', $pos);
                    if ($pq[0] < $min_p) $min_p = $pq[0];
                    if ($pq[1] < $min_q) $min_q = $pq[1];
                }
                foreach (array_filter($board->get_board()) as $pos => $tile) {
                    $pq = explode(',', $pos);
                    $pq[0];
                    $pq[1];
                    $h = count($tile);
                    echo '<div class="tile player';
                    echo $tile[$h-1][0];
                    if ($h > 1) echo ' stacked';
                    echo '" style="left: ';
                    echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
                    echo 'em; top: ';
                    echo ($pq[1] - $min_q) * 4;
                    echo "em;\">($pq[0],$pq[1])<span>";
                    echo $tile[$h-1][1];
                    echo '</span></div>';
                }
            ?>
        </div>
        
        <div class="hand">
            White:
            <?php
                foreach ($player_white->get_hand() as $tile => $tile_count) {
                    for ($i = 0; $i < $tile_count; $i++) {
                        echo '<div class="tile player0"><span>'.$tile."</span></div> ";
                    }
                }
            ?>
        </div>
        <div class="hand">
            Black:
            <?php
            
            foreach ($player_black->get_hand() as $tile => $tile_count) {
                for ($i = 0; $i < $tile_count; $i++) {
                    echo '<div class="tile player1"><span>'.$tile."</span></div> ";
                }
            }
            ?>
        </div>
        <div class="turn">
            Turn: <?php if ($active_player === $player_white) echo "White"; else echo "Black"; ?>
        </div>
        <form method="post">
            <select name="piece">
                <?php
                    foreach ($active_player->get_hand() as $tile => $tile_count) {
                        if ($tile_count == 0) {
                            continue;
                        }
                        echo "<option value=\"$tile\">$tile</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($play_positions as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" name="play" value="Play">
        </form>

        <form method="post">
            <select name="from">
                <?php
                    foreach (array_keys($board->get_board()) as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <select name="to">
                <?php
                    foreach ($move_positions as $pos) {
                        echo "<option value=\"$pos\">$pos</option>";
                    }
                ?>
            </select>
            <input type="submit" name="move" value="Move">
        </form>

        <form method="post">
            <input type="submit" name="pass" value="Pass">
        </form>
        <form method="post">
            <input type="submit" name="ai" value="AI">
        </form>
        <form method="post">
            <input type="submit" name="undo" value="Undo">
        </form>
        <form method="post">
            <input type="submit" name="restart" value="Restart">
        </form>
        <strong><?php if (isset($_SESSION['error'])) {
            echo($_SESSION['error']);
            unset($_SESSION['error']);
        } ?></strong>
        <strong><?php if (isset($_SESSION['message'])) {
            echo($_SESSION['message']);
            unset($_SESSION['message']);
        } ?></strong>
        <ul>
            <?php
                $result = $database->get_previous_turns();
                $counter = 0;
                while ($row = $result->fetch_array()) {
                    $counter++;
                    $color = ($counter % 2 == 1) ? '⚪' : '⚫';
                    echo '<li>'.$counter.'. '.$color.' '.$row[2].' '.$row[3].' '.$row[4].'</li>';
                }
            ?>
        </ul>
    </body>
</html>

