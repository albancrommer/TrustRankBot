<?php 
require_once ('bootstrap.php');
$root               = dirname($_SERVER['SCRIPT_NAME']);
$query              = str_replace( $root.'/', '', $_SERVER['REQUEST_URI']);
$request            = explode('/',$query);
$view               = ( null != $view = $request[0] )?$view:'index';
$template           = "templates/template.phtml";
try {
    $actionName     = $view.'Action';
    include("actions/$actionName.php");
    $action         = new $actionName($request);
    $data           = $action->run();
} catch (Exception $e) {
    $view           = "error";
}
$view           = "views/$view.phtml";
include( $template );