<?php

namespace controllers\movecontrollers;

use database\DatabaseService;
use objects\Game;
use objects\Board;
use objects\Player;
use controllers\Controller;

class BaseMoveController extends MoveController {

    public function __construct(DatabaseService $database_service) {
        parent::__construct($database_service);

    }
}
?>