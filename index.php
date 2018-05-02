<?php
/**
 *  LinkScannerv1.0
 *  Scans a series of pages provided in an array in .urls.php
 *  and searches for specific content.
 */


require('vendor/autoload.php');
use PHPHtmlParser\Dom;

if (getenv('APP_ENV') != 'test') {
  $dotenv = new Dotenv\Dotenv(__DIR__);
  $dotenv->load();
}


$env = getenv('APP_ENV');
$slack['hook'] = getenv('SLACK_HOOK');
$slack['channel'] = getenv('SLACK_CHANNEL');

if ($env !='test') {
  include '.urls.php';
  $settings = ['channel' => $slack['channel']];
  $slackClient = new Maknz\Slack\Client($slack['hook'], $settings);
}
  else {
    $urls = ['http://www.awardconcepts.net/about-us/staff/'];
}

$fails = [];
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
      $fail++;
    }
  }


  if ($fail > 0) {
    array_push($fails, $url);
  }

}


if (count($fails) > 0) {
  $failList = '';

  foreach ($fails as $row) {
    $failList .= $row . "\n";
    }
  }
    else {
      $failList = 'All Clear.';
   }

    try {
      if ($env !='test') {
           $slackClient->send($failList);

        }
          else {
             print 'found!';
          }
     } catch  (Exception $e) {
      var_dump($e);
     }


