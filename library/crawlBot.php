<?php

include 'library/Seomoz/bootstrap.php';

// 
//  crawlBot.php
//  trustrankbot
//  
//  Created by Alban on 2011-06-20.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
// 
/**
* 
*/
class crawlBot
{
    private $_db;
    private $_site;
    private $_crawl_delay = 3500;
    private $_response;
    private $_urls;
    private $_curlOptions;
    private $_logger;
    private $_validTlds;
    private $_seomoz;
    private $_urlMetricsService;
    
    function __construct( $options = null )
    {
        
        $AccessID           = $options['seomoz-user'];
        $SecretKey          = $options['seomoz-key'];
        $this->_seomoz  = new Authenticator();
        $this->_seomoz->setAccessID($AccessID);
    	$this->_seomoz->setSecretKey($SecretKey);
        $this->_urlMetricsService = new urlMetricsService($this->_seomoz);
        $this->_validTlds = 'fr';
        $this->_curlOptions = array(
            CURLOPT_RETURNTRANSFER  =>  1,
            CURLOPT_FOLLOWLOCATION  =>  1,
            CURLOPT_NOPROGRESS      =>  1,
            CURLOPT_TIMEOUT         => 10
        );

    }
    
    public function acquire()
    {
        $crawl_delay = $this->_crawl_delay;
        $newTarget;
        
        while( $newTarget->num_rows < 1 AND $crawl_delay > 1 ){
        
            $query  ="SELECT * FROM `urls` U ";
            $query  .="WHERE TIMESTAMPDIFF( MINUTE, U.`dt_last_crawl`, NOW() ) > $crawl_delay ";
            $query  .="ORDER BY dt_last_crawl ASC, U.domain_auth DESC, U.page_auth DESC ";
            $query  .="LIMIT 1";
            $newTarget = DBO::getAdapter()->query( $query );
            $crawl_delay *= .5;
        }
        // Log::getSingleton()->debug( $query );
        if( $newTarget->num_rows == 0 ){
            return FALSE;
        }
        while( $s = $newTarget->fetch_assoc() ){
            $this->_site = new site( $s );
        }
        LOG::getSingleton()->alert($this->_site->_url);
        
echo( "Crawling ".$this->_site->_url."\n");
        return true;
        
    }
    
    public function process()
    {
        $this->_site->updateCrawlDt();
        if( ! $this->_getResponse() ) return; 
        $this->_parseResponse();
        $this->_qualifyUrls();
        $this->_addUrls();
    }
    
    private function _getResponse()
    {
        $this->_response    = "";
        if(!$c = curl_init()){
            return FALSE;
        }
        
        foreach ($this->_curlOptions as $key => $value) {
            curl_setopt( $c, $key, $value );
        }
        curl_setopt($c, CURLOPT_URL, $this->_site->_url);
        $this->_response    = curl_exec($c);
        if (FALSE === $this->_response) {
            throw new curlException("crawlBot::_getResponse failed", 1);
            return FALSE;
        }
        curl_close($c);
        return TRUE;
    }
    
    private function _parseResponse()
    {
        $result;
        preg_match_all('/href="([htp]+.*?)"/',$this->_response,$result);
        $this->_urls    = $result[1];
        return FALSE;
    }
    
    private function _qualifyUrls()
    {
     
        $urlStack = array();
        
        foreach ($this->_urls as $rawUrl) {
            $r  = "^(?:(?P<protocol>(?P<scheme>\w+)://))?";
            // $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
            $r .= "(?P<host>(?:(?P<subdomain>[-\w\.]+)\.)?" . "(?P<domain>[-\w]+\.(?P<extension>($this->_validTlds))))";
            $r .= "(?::(?P<port>\d+))?";
            $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
            $r .= "(?:\?(?P<arg>[\w=&]+))?";
            $r .= "(?:#(?P<anchor>\w+))?";
            $r = "!$r!";
            preg_match( $r, $rawUrl, $urlData);
            if( count($urlData) > 1  ){
                $url = $urlData['host'];
                if( !array_key_exists( $url, $urlStack )){
echo "parsing $url\n";         
                    $site = new site(array("url"=>$url));
                    if( $site->checkIsSeomozQueryRequired()){
                        if( !$urlSeoInfo = $this->_seomozCall( $url )){
                            Log::getSingleton()->alert("seomoz failed on : $url");
                            $urlSeoInfo = array('domain_auth' => null, 'page_auth' => null, 'backlinks' => null, 'rank' => null);
                        }
                    }else{
echo "no seomoz update required\n";                          
                    }
                    $urlStack[$url]['domain']       = $urlData['domain'];
                    $urlStack[$url]['domain_auth']  = $urlSeoInfo->pda;
                    $urlStack[$url]['page_auth']    = $urlSeoInfo->upa;
                    $urlStack[$url]['backlinks']    = $urlSeoInfo->ueid;
                    $urlStack[$url]['rank']         = $urlSeoInfo->fmrp;
                    $urlStack[$url]['url']          = $url;
                }
            }else{
                Log::getSingleton()->debug($rawUrl.' is invalid.');
            }
        }
        $this->_urls = $urlStack;
        return TRUE;
    }
    
    private function _seomozCall( $url = null )
    {
        if( null == $url ){
            throw( new Exception("crawlBot:_seomozCall missing parameter : url."));}
        $response = $this->_urlMetricsService->getUrlMetrics(urldecode($url));
	    return $response;
    }
    
    public function _addUrls()
    {
        if( count($this->_urls) < 1 ) return;
        foreach ($this->_urls as $key => $urlArray) {
            $site = new site(array(
                'url'           => $urlArray['url'],
                'domain'        => $urlArray['domain'],
                'domain_auth'   => $urlArray['domain_auth'],
                'page_auth'     => $urlArray['page_auth'],
                'backlinks'     => $urlArray['backlinks'],
                'rank'          => $urlArray['rank']
            ));
            $siteID = $site->save();
            $site->saveLink( $this->_site->_id, $siteID );
        }
    }
}