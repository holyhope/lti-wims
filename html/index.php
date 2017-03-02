<?php

require_once "../Router.php";

$router = new LTI\Router();

$router->setup();

$router->handle_request( $_GET, $_POST );
