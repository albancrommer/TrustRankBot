<?php

if( 'cli' != PHP_SAPI ) die('Not running from CLI');
include('bootstrap.php');
include("library/botManager.php");
try {
    
    $botManager             = new botManager();
    
    $botManager->setLockFile($config['lockfile']);
    
    $nInstances             = $botManager->getInstancesCount();
    if( $botManager->getInstancesCount() >= $config["instances"]){
        echo("Enough instances already\n");
        die();
    }
    
    exec($config["php"]." bot.php >> logs/botmanager.log 2>&1 & echo $!", $output, $return_var );
    
    $pid    = $output[0];
    
    if( 0 != $return_var){
        
        throw new Exception("Process returned an error", 1);
        
    }elseif( $pid > 0 ){
        
        $botManager->add( array(
            "pid"       => $pid
        ));

    }
    
} catch (Exception $e) {
    
    $message   .= "###########\n";
    $message   .= $e->getMessage()." ";
    $message   .= $e->getTraceAsString();
    $message   .= "\n###########\n";

    print $message;
    LOG::getSingleton()->alert($message);
}
