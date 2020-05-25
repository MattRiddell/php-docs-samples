<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/speech/README.md
 */

// Include Google Cloud dependendencies using Composer
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/php-docs-samples/speech/src/cred.json');
// if (count($argv) != 2) {
//     return print("Usage: php transcribe_sync.php AUDIO_FILE\n");
// }
// list($_, $audioFile) = $argv;

# [START speech_transcribe_sync]
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

/** Uncomment and populate these variables in your code */
// $audioFile = 'path to an audio file';

// change these variables if necessary
$encoding = AudioEncoding::LINEAR16;
$sampleRateHertz = 8000;
$languageCode = 'ar-EG';

// get contents of a file into a string
// $content = $_POST['file'];
$content = file_get_contents("php://input");

// set string as audio content
$audio = (new RecognitionAudio())
    ->setContent($content);

// set config
$config = (new RecognitionConfig())
    ->setEncoding($encoding)
    ->setSampleRateHertz($sampleRateHertz)
    ->setLanguageCode($languageCode);

// create the speech client
$client = new SpeechClient();
/*
 * [0] => stdClass Object
    (
        [alternatives] => Array
            (
                [0] => stdClass Object
                (
                    [confidence] => 0.63
                    [transcript] => yes
                )
            )
        [final] => 1
    )
)
 */
try {
    $response = $client->recognize($config, $audio);
    // $results = array();

    foreach ($response->getResults() as $result) {
        $alternatives = $result->getAlternatives();
        $mostLikely = $alternatives[0];
        $transcript = $mostLikely->getTranscript();
        $confidence = $mostLikely->getConfidence();
        $results = new stdClass();
        $results->alternatives = Array();
        $results->alternatives[0] = new StdClass();
        $results->alternatives[0]->confidence = $confidence;
        $results->alternatives[0]->transcript = htmlentities((string)$transcript);
        // $results[] = $resultx;
        // printf('Transcript: %s' . PHP_EOL, $transcript);
        //$result = Array();
        // $result = htmlentities((string)$transcript);
        // echo '{result: "'.$result.'"}';
        // echo json_encode($result);
        // break;
        // printf('Confidence: %s' . PHP_EOL, $confidence);
    }
} finally {
    $client->close();
}
// print_r($results);
$resultsx = Array();
$resultsx['results'] = $results;
$json =  json_encode($resultsx, JSON_UNESCAPED_UNICODE);
echo $json;
// echo html_entity_decode($json);
// echo html_entity_decode((string)$json);
// echo html_entity_decode(utf8_decode($json));
// $output = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $json);
// echo "\n".$output;
# [END speech_transcribe_sync]
