<?php
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
    echo ( APPLICATION_PATH );
require(APPLICATION_PATH.'./library/Log/Log.php');
require(APPLICATION_PATH.'./library/crawlBot.php');
require_once(APPLICATION_PATH.'./library/DBO.php');
require_once(APPLICATION_PATH."./library/site.php");

$config = parse_ini_file('config/config.ini');

$conf = array('mode' => 0600, 'timeFormat' => '%X %x');
$logger = &Log::singleton('file', 'logs/crawler.log', 'ident', $conf, PEAR_LOG_INFO);

function url( $relative = null ){
    $root               = dirname($_SERVER['SCRIPT_NAME']);
    $r                  = $root.$relative;
    return $r;
}

function printTable($d){
    $cycle = 1 ;
    $html ='<table class="results-list tablesorter">';
    $html.='<thead>';
    $html.='<tr>';
    $html.='<th class="tooltip" rel="url">Subdomain data <a class="out">&#x2192;&nbsp;Direct Link</a></th>';
    $html.='<th class="tooltip" rel="domain">Domain</th>';
    $html.='<th class="tooltip" rel="page_auth">Page Authority</th>';
    $html.='<th class="tooltip" rel="domain_auth">Domain Authority</th>';
    $html.='<th class="tooltip" rel="rank">Rank</th>';
    $html.='<th class="tooltip" rel="backlinks">Juicy backlinks</th>';
    $html.='</tr>';
    $html.='</thead>';
    $html.='<tbody>';
    foreach ($d as $entry) : 
        $url=$entry['url'];
        $html.='<tr class="t'. ++$cycle%2 .'">';
        $html.='<td class="tooltip" rel="url">
            <a title="View details" href="'.url("/read/".$entry['id'].'/'.$url).' "> '.$url.'</a> 
            <a class="out" title="Open in new window" href="http://'.$url.'" target="_blank"> &#x2192;&nbsp;'.$url.'</a></td>';
        $html.='<td class="tooltip" rel="domain">'.$entry['domain'].'</td>';
        $html.='<td class="tooltip" rel="page_auth">'.$entry['page_auth'].'</td>';
        $html.='<td class="tooltip" rel="domain_auth">'.$entry['domain_auth'].'</td>';
        $html.='<td class="tooltip" rel="rank">'.$entry['rank'].'</td>';
        $html.='<td class="tooltip" rel="backlinks">'.$entry['backlinks'].'</td>';
        $html.='</tr>';
    endforeach;      
    $html.='</tbody>';
    $html.='</table>';
    echo( $html );
}
