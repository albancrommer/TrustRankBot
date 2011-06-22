<?php
include('bootstrap.php');


$bot    = new crawlBot(array(
    "seomoz-user"   => $config['seomoz-user'],
    "seomoz-key"    => $config['seomoz-key'],
));
$loop = 0;
while(1){
    $loop++;
    echo("\n############ LOOP #$loop ##########\n");            
    try {
        
        if( $bot->acquire() ){
            echo("target acquired\n");            
            $bot->process();
        }else{
            echo("sleep\n");
            sleep(10);
            $logger->log(".");
        }
        
    } catch (Exception $e) {
        echo $e->getMessage();
        $logger->alert("EXCEPTION : ".$e->getMessage()."\n");
    }
}
