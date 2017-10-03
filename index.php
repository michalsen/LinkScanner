<?php


require('vendor/autoload.php');
use PHPHtmlParser\Dom;


$urls = ['http://www.natomassmiles.com/about-us/our-team/',
         'http://www.royalreportingservices.com/our-story/our-team/'];


foreach ($urls as $url) {


$client = new GuzzleHttp\Client();
$res = $client->request('GET', $url);

$body = $res->getBody();


$dom = new Dom;
$dom->load($body);


$contents = $dom->find('.staff-name');

$fail = 0;
foreach ($contents as $content)
  {

    $class = $content->getAttribute('class');
    $html = $content->find('a')[0];

    if (!preg_match("/[A-Za-z]/", $html->text)) {
      //print $html->text . "\n";
      $fail++;
    }
  }


  //print 'fail: ' .  $fail . "\n";
  if ($fail > 0) {
    // print $url . "\n";
    // print $html . "\n";
    mail('', 'Missing Title Data', $url . "\n" . $html);
  }

}


