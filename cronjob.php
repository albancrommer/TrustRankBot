<?php

if( 'cli' != PHP_SAPI ) die('Not running from CLI');

include('bootstrap.php');
include("library/botManager.php");
try {
    
    $botManager             = new botManager();
    
    $botManager->setLockFile($config['lockfile']);
    
    $nInstances             = $botManager->getInstancesCount();
    
    $nRequiredBots          = $config["instances"] - $nInstances;
    
    if( $nRequiredBots < 1 ){
        LOG::getSingleton()->alert("Enough instances already");
        echo("Enough instances already\n");
        die();
    }
    
    for ($i=0; $i < $nRequiredBots; $i++) { 
        
        $output             = null;
        $cmd = $config["php"]." ".APPLICATION_PATH."/bot.php >> /dev/null 2>&1 & echo $!";
        exec($cmd, $output, $return_var );
        echo($cmd."\n");
        $pid                = $output[0];
        echo( date( 'ymd h:i:s')." Spawning bot with pid #$pid errno. $return_var ".print_r($output,1)."\n");
        if( 0 != $return_var){
            throw new Exception("Process returned an error", 1);
            $msg = "Cronjob failed : errno. $return_var ".print_r($output,1);
            LOG::getSingleton()->alert($msg);
            echo( $msg );
        }elseif( $pid > 0 ){
            $botManager->add( array(
                "pid"       => $pid
            ));
        }
        
    }
    
} catch (Exception $e) {
    
    $message   .= "###########\n";
    $message   .= $e->getMessage()." ";
    $message   .= $e->getTraceAsString();
    $message   .= "\n###########\n";

    print $message;
    LOG::getSingleton()->alert($message);
}

