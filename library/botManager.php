<?php
/**
*  
*/
class botManager
{
    
    function __construct( $options = null )
    {
        if( null != $options['lockfile'] ){
            $this->setLockFile( $options['lockfile']);
        }
    }
    
    public function add( $options = null)
    {
        $handle     = fopen($this->_lockfile,"a");
        fwrite($handle,implode(',',$options)."\n");
        fclose($handle);
        
    }
    public function dumpFile()
    {
        $arFile     = file($this->_lockfile);
        echo ("lockfile : $this->_lockfile\n");
        var_dump( $arFile );
    }
    
    public function kill( $pid = null )
    {
        $cmd = "kill $pid";
        exec($cmd,$output,$result);
        if( $result == 0)
            return true;
        return false;

    }

    public function killAll()
    {
        $arFile     = file($this->_lockfile);
        if(count($arFile) < 1 ) return 0;
        foreach ($arFile as $bot) {
            $botStatus=explode(',',$bot);
            if($this->kill( $botStatus[0] ))
                echo( "Successfully killed pid #".$botStatus[0]."\n");
            else 
                echo( "Failed killing pid #".$botStatus[0]."\n");
        }
        $this->save();
    }    
    
    public function getInstancesCount()
    {
        $arFile     = file($this->_lockfile);
        if(count($arFile) < 1 ) return 0;
        $nInstances = 0;
        foreach ($arFile as $bot) {
            $botStatus=explode(',',$bot);
            if( $this->running( $botStatus[0] )){
                $nInstances++;
                $current[]=$bot;
            }
        }
        $this->save($current);
        return $nInstances;
        
    }
    public function running( $pid = null )
    {
        $cmd ="ps $pid";
        exec($cmd,$output,$result);
        if( count($output)>1)
            return true;
        return false;
    }
    
    public function save( $bots = null )
    {
        if( null == $bots)          $c="";
        elseif( count($bots) < 1 )  $c="";
        else foreach ($bots as $key => $value) {
            $c  .= $value;
        }
        file_put_contents($this->_lockfile,$c);
        
    }
    
    public function setLockFile($lockfile = null)
    {
        if( null == $lockfile ){
            throw( new Exception("botManager:setLockFile missing parameter : lockfile."));}

        if(!touch($lockfile))
            throw new Exception("botManager::setLockFile NOT A VALID FILE : NO TOUCH", 1);

        if( !is_file($lockfile)){
            throw new Exception("botManager::setLockFile NOT A VALID FILE : NO FILE", 1);
        }
        if( !is_writable($lockfile)){
            throw new Exception("botManager::setLockFile NOT WRITABLE ", 1);
        }
        $this->_lockfile = $lockfile;
    }
    
}
            
