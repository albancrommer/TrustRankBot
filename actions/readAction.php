<?php 

// 
//  readAction.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-22.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 
/**
* 
*/
class readAction 
{
    
    function __construct( $request = null )
    {
        
        $this->_id = $request['1'];
        
        if( null == $this->_id){
            throw new Exception("Missing parameter id", 1);
            
        }
    }
    
    public function run()
    {
        $escId                      = mysql_escape_string( $this->_id );
        $sql                        = "SELECT id,url, domain,domain_auth,page_auth, rank, backlinks, dt_last_crawl FROM urls WHERE id = $escId";
        $results                    = DBO::getAdapter()->query($sql);
        if( $results->num_rows > 0 )
        while ($r = $results->fetch_assoc()) {
            $entry              = $r;
        }
        $data['urlData']     = $entry;
        
        $sql                        = "SELECT U.* FROM links L join urls U ON L.`fr` = U.`id` WHERE `to` = $escId ORDER BY page_auth DESC";
        $results                    = DBO::getAdapter()->query($sql);
        if( $results->num_rows > 0 )
        while ($r = $results->fetch_assoc()) {
            $linksTo[]              = $r;}
        $data['linksTo']            = $linksTo;
        
        $sql                        = "SELECT U.* FROM links L join urls U ON L.`to` = U.`id` WHERE `fr` = $escId ORDER BY page_auth DESC";
        $results                    = DBO::getAdapter()->query($sql);
        if( $results->num_rows > 0 )
        while ($r = $results->fetch_assoc()) {
            $linksFrom[]            = $r;}
        $data['linksFrom']          = $linksFrom;
        $data['title']              = "Infos sur ".$entry['url'];
        
        return $data;
    }
}
