<?php
if( 'cli' != PHP_SAPI ) die('Not running from CLI');
include('bootstrap.php');


class curlException extends exception
{
}

// $bot    = new crawlBot(array(
//     "seomoz-user"       => $config['seomoz-user'],
//     "seomoz-key"        => $config['seomoz-key'],
//     "file-last-crawled" => $config['file-last-crawled']
// ));
$bot    = new crawlBot($config);


LOG::getSingleton()->alert("START process pid ".getmypid()."\n");

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
        
        
        // throw new curlException("Test", 1);
        
    } catch (curlException $e) {
        LOG::getSingleton()->alert("EXCEPTION : ".$e->getMessage()."\n");
        LOG::getSingleton()->alert("KILL PROCESS\n");
        exec($config['php'].' cronjob.php >> /dev/null 2>&1 &');
        echo("\n############ END ##########\n");            
        die('end.');
    } catch (exception $e) {
        echo $e->getMessage();
        LOG::getSingleton()->alert("EXCEPTION : ".$e->getMessage()."\n");
    }
}
