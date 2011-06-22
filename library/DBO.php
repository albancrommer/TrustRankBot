<?php 
// 
//  DBO.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-20.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 

class DBO {
    private static $mysqli;
    private function __construct(){} 

    static function getAdapter() {
        if( !self::$mysqli ) {
            $conf=parse_ini_file('config/config.ini');
            $mysqli=new mysqli($conf['host'],$conf['user'],$conf['pwd'],$conf['db']);
            if($mysqli->connect_error){
                die('connection_error '.$mysqli->connect_errno.' : '.$mysqli->connect_error );
            }
            self::$mysqli = $mysqli;
        }
        return self::$mysqli;
    }
    
    static function escape($inp='')
    {
        if(is_array($inp)) 
            return array_map(__METHOD__, $inp); 

        if(!empty($inp) && is_string($inp)) { 
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
        } 

        return $inp;    
    }
    
    
}  