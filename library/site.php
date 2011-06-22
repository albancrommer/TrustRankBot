<?php 
// 
//  site.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-20.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 
/**
* 
*/
class site
{
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        return $this;
    }
    /**
     * Sets the properties of object using request parameters 
     *
     * @param    $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $prop           = "_".$key;
            $this->$prop    = $value;
        }
        return $this;
    }
    
    public function checkIsSeomozQueryRequired()
    {
        $url = DBO::escape( $this->_url );
        $sql.="SELECT ( CASE  WHEN ( SELECT `id` FROM `urls` ";
        $sql.="WHERE `url` = '$url' ) THEN TRUE ELSE FALSE END ) AS `exists`,";
        $sql.="( CASE  WHEN ( SELECT `id` FROM `urls` WHERE `url` = '$url' AND ";
        $sql.="TIMESTAMPDIFF( HOUR, `dt_last_crawl`, NOW() ) > 480 ) ";
        $sql.="THEN TRUE ELSE FALSE END ) AS `required`";
        Log::getSingleton()->debug($sql);
        $r  = DBO::getAdapter()->query( $sql );
        if( null == $r ) {
            LOG::getSingleton()->debug( "checkIsSeomozQueryRequired returns FALSE" );            
            return FALSE;
        }
        $result = $r->fetch_assoc();
        LOG::getSingleton()->debug( $sql );
        // LOG::getSingleton()->debug( "checkIsSeomozQueryRequired : ".print_r($result,1) );
        if( !$result['exists']) return TRUE;
        return $result['required'];
        
    }
    
    public function updateCrawlDt()
    {
        if( null == $this->_id ){
            throw( new Exception("site:updateCrawlDt missing parameter : id.\n"));}
        $id  = DBO::escape( $this->_id );
        $sql ="UPDATE urls set `dt_last_crawl`='".date('Y-m-d H:i:s')."'";
        $sql.='WHERE id = '.$this->_id;
        DBO::getAdapter()->query( $sql );
        return FALSE;
    }
    
    public function exists($value='')
    {
        $sql = "SELECT id FROM `urls` WHERE url = '$this->_url'";
        $result = DBO::getAdapter()->query( $sql );
        if( $result->num_rows < 1 )
            return FALSE;
        $entry      = $result->fetch_array();
        $ID         = $entry[0];
        Log::getSingleton()->debug("site:exists returns existing ID : $ID.");
        return $ID;
        
    }
    
    public function saveLink($id1 = null , $id2 = null )
    {
        if( null == $id1 ){
            throw( new Exception("site:saveLink missing parameter : id1."));}
        
        if( null == $id2 ){
            throw( new Exception("site:saveLink missing parameter : id2."));}

        Log::getSingleton()->debug("site:saveLink Linking  $id1 $id2" ); 
        
        if(  $id1 == $id2){
        Log::getSingleton()->debug("site:saveLink exit" );             
            return FALSE;
        }
        if(  $existingID = $this->existsLink( $id1, $id2 ) ){ 
            return $existingID;
        }
            
        $dt = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `links` ( `fr`,`to`, dt)";
        $sql.= "VALUES ($id1,$id2,'$dt')";
        Log::getSingleton()->debug($this->_url.' saved.');
        DBO::getAdapter()->query( $sql );
        $ID         = DBO::getAdapter()->insert_id;
        Log::getSingleton()->debug('site:saveLink returns with ID '.$ID ); 
        return $ID;
        
    }
    
    
    public function existsLink($id1 = null , $id2 = null )
    {
        if( null == $id1 ){
            throw( new Exception("site:existsLink missing parameter : id1."));}
        
        if( null == $id2 ){
            throw( new Exception("site:existsLink missing parameter : id2."));}
        
        $sql = "SELECT id FROM `links` WHERE `fr` = $id1 AND `to` = $id2";
        $result = DBO::getAdapter()->query( $sql );
        if( $result->num_rows < 1 )
            return FALSE;
        $entry      = $result->fetch_array();
        $ID         = $entry[0];
        Log::getSingleton()->debug("site:existsLink returns existing ID : $ID.");
        return $ID;
    }
    
    
    
    public function save()
    {
        Log::getSingleton()->debug( $this->_url ); 
        if( $existingID = $this->exists() ){
            Log::getSingleton()->debug('site:save returns with existing ID '.$existingId ); 
            // don't update if no SEO info
            if( null != $this->_rank && null != $this->_domain_auth ){
echo( "updating URL $this->_url\n");
                $this->_id = $existingID;  
                $this->update();
            }
            else{
echo( "not updating URL : no SEO info $this->_url\n");                
            }
            return $existingID;  
        } 
echo( "inserting URL $this->_url\n");                
        $dt_added = date('Y-m-d H:i:s');
        $dt_crawl = date('Y-m-d H:i:s', time()-3600*24*30);
        $sql = "INSERT INTO `urls` ( url,domain,rank,domain_auth,page_auth,backlinks,dt_last_crawl,dt_added )";
        $sql.= "VALUES ('$this->_url','$this->_domain','$this->_rank','$this->_domain_auth','$this->_page_auth','$this->_backlinks','$dt_crawl','$dt_added')";
        //Log::getSingleton()->debug( $sql ); 
        DBO::getAdapter()->query( $sql );
        $ID         = DBO::getAdapter()->insert_id;
        Log::getSingleton()->debug('site:save returns with ID '.$ID ); 
        return $ID;
    }
    
    public function update()
    {
        if( null == $this->_id ){
            throw( new Exception("site:update missing parameter : id."));}
        $sql = "UPDATE `urls` SET ";
        $sql.= "domain_auth = '$this->_domain_auth',page_auth = '$this->_page_auth',";
        $sql.= "backlinks = '$this->_backlinks', rank ='$this->_rank'";
        $sql.= "WHERE `id` = '$this->_id'";
        // Log::getSingleton()->debug( "site:update $sql "); 
        DBO::getAdapter()->query( $sql );
    }
}
