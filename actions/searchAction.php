<?php 

// 
//  searchAction.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-22.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 

/**
* 
*/
class searchAction 
{
    
    function __construct( $request = null )
    {
        $this->_search = $_POST['search'];
        if( null == $this->_search){
            throw new Exception("Missing parameter search", 1);
        }
    }
    
    public function run()
    {
        DBO::getAdapter();
        $searchStr                  = DBO::escape( $this->_search );
        $sql                        = "SELECT id,url, domain,domain_auth,page_auth, rank, backlinks, dt_last_crawl FROM urls ";
        $sql                        .= "WHERE url LIKE '%$searchStr%' LIMIT 300;";
        $results                    = DBO::getAdapter()->query($sql);
        if( $results->num_rows > 0 ){
            while ($r = $results->fetch_assoc()) {
                $data['entries'][]  = $r;
            }
        }
        return $data;
    }
}
