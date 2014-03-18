<?php
/**
* ohrenbaer-rss.php
* released under MIT License(see extra file)
*
* version:  0.9
* author:   repat, <repat[at]repat[dot]de>, http://repat.de
* date:     March 2014
*
* Uses SUIN RSS Writer: https://github.com/suin/php-rss-writer
* and SimpleHTMLDOM: http://simplehtmldom.sourceforge.net/
*/

include 'simple_html_dom.php';

mb_internal_encoding("UTF-8"); 

spl_autoload_register(function($c) { @include_once strtr($c, '\\_', '//').'.php'; });

use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

$html = file_get_html('http://www.ohrenbaer.de/start/podcast/podcast.html');

$currentDate = date('D, j M o H:i:s O');

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">', LIBXML_NOERROR|LIBXML_ERR_NONE|LIBXML_ERR_FATAL);

$xml->addChild('title', "Ohrenbär");
$xml->addChild('link', 'http://ohrenbaer.de');
$xml->addChild('description', "Radiogeschichten für kleine Leute");
$xml->addChild('language','de-DE');
$xml->addChild('copyright', 'rbb-online, Rundfunk Berlin-Brandenburg, Germany');
$xml->addChild('pubDate',strtotime($currentDate));
$xml->addChild('lastBuildDate',strtotime($currentDate));
$xml->addChild('ttl','60');
$itunesImage = $xml->addChild('itunes:image', null, 'http://www.itunes.com/dtds/podcast-1.0.dtd');
$itunesImage->addAttribute('href', 'http://repat.de/Bilder/repat200.png');

foreach($html->find('.download') as $element) {
	$url = $element->href;

    // parse URL to proper titles
	preg_match('/rbb_.*$/i', $url, $names);
    $names[0] = preg_replace('/rbb_/i', '', $names[0]);
    $names[0] = preg_replace('/\.mp3/i', '', $names[0]);
    $names[0] = preg_replace('/_/i', ' ', $names[0]);
    
    foreach ($names as $e) {
	    $item = new Item();
	    $item
	    ->title($e)
	    ->description($e)
	    ->url("http://www.ohrenbaer.de/start/podcast/podcast.html")
	    ->enclosure($url)
	    ->pubDate(strtotime($currentDate))
	    ->guid($url, true)
	    ->appendTo($xml);       
    }
}

$result = $xml->asXML();

echo $result;
?>

