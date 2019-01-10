<?php

require __DIR__ . '/vendor/autoload.php';
use Goutte\Client;

$client = new Client();

$strJsonFileContents = file_get_contents("config.json");
$data = json_decode($strJsonFileContents, false);
$ifttt = $data->ifttt;
$jobs = $data->jobs;

foreach($jobs as $key => $job){
    $newLastId = 0;

    $crawler = $client->request('GET', $job->url);
    $crawler->filter('.search-result-entry:not(#wh_adition_FakeAd1)')->each(function ($node) use ($client, &$newLastId, $job, $ifttt) {
        if(count($node->children()->filter('.content-section')) != 0){
            
            $id = (int)trim($node->children()->filter('.content-section')->children('.header')->children('a')->attr('data-ad-link'));

            $newLastId = max($newLastId, $id);

            if($id > $job->lastId){
                
                $ad_url = "https://www.willhaben.at".trim($node->children()->filter('.content-section')->children('.header')->children('a')->attr('href'));
                $crawler = $client->request('GET', $ad_url);

                preg_match('/.*?€\ ([0-9]*).*/', $crawler->filter('title')->text(), $output_array);
                $price = $output_array[1]." €";

                $url = trim($node->children()->filter('.content-section')->children('.header')->children('a')->attr('href'));
                $title = trim($node->children()->filter('.content-section')->children('.header')->children('a')->text());

                $url = 'https://maker.ifttt.com/trigger/'.$ifttt->event.'/with/key/'.$ifttt->key;
                $ch = curl_init($url);
                $xml = "value1=".$title."&value2=".$price."&value3=".$ad_url;
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
                
                $response = curl_exec($ch);
                curl_close($ch);

                print $title." (".$id.") for ".$price."\n";
            }
        }
    });
    $data->jobs[$key]->lastId = $newLastId;
}

$json_data = json_encode($data);
file_put_contents('config.json', $json_data);


//$crawler = $client->request('GET', 'https://github.com/');
