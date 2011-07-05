<?php 

// 
//  indexAction.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-21.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 
/**
* 
*/
class indexAction 
{
    
    function __construct($request = null )
    {
        # code...
    }
    
    public function run()
    {
        
        $sql                        = "SELECT id,url, domain,domain_auth,page_auth, rank, backlinks FROM urls ORDER BY page_auth DESC LIMIT 300";
        $results                    = DBO::getAdapter()->query($sql);
        while ($entry = $results->fetch_assoc()) {
            $entries[]              = $entry;
        }
        $data['title']              = "Accueil";
        $data['entries']            = $entries;
        return $data;
    }
}
