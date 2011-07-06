<?php

if( 'cli' != PHP_SAPI ) die('Not running from CLI');
include('bootstrap.php');
include("library/botManager.php");

$botManager             = new botManager();

$botManager->setLockFile($config['lockfile']);

$botManager->killAll();
