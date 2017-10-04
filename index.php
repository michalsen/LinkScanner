<?php


require('vendor/autoload.php');
use PHPHtmlParser\Dom;


$hook_url = file_get_contents('.hook');
$slackClient = new Maknz\Slack\Client($hook_url);

$urls = ['http://www.natomassmiles.com/about-us/our-team/',
         'http://www.royalreportingservices.com/our-story/our-team/'];

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
      //print $html->text . "\n";
      $fail++;
    }
  }


  //print 'fail: ' .  $fail . "\n";
  if ($fail > 0) {
    array_push($fails, $url);
  }

}


if (count($fails) > 0) {
  $failList = '';

  foreach ($fails as $row) {
    $failList .= $row . "\n";
  }

    try {
      $slackClient->send($failList);

      // $to      = '';
      // $subject = 'Missing Title Data';
      // $message = $failList;
      // $headers = 'From: testing@straightnorth.com' . "\r\n" .
      // 'Reply-To: testing@straightnorth.com' . "\r\n" .
      // 'X-Mailer: PHP/' . phpversion();
      // mail($to, $subject, $message, $headers);

     } catch  (Exception $e) {
      var_dump($e);
     }
}
