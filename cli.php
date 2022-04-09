<?php declare(strict_types=1);

require './vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\HttpClient\CurlHttpClient as SymfonyCurlHttpClient;
use Fabpico\YoutubePlaylistToMp3\Common\Httpclient\CurlHttpClient;
use Fabpico\YoutubePlaylistToMp3\Commands\DownloadVideosCommand;
use Fabpico\YoutubePlaylistToMp3\Commands\ConvertMp4ToMp3Command;

$application = new Application();

// todo, autowire
$httpClient = new CurlHttpClient(new SymfonyCurlHttpClient());
$application->add(new DownloadVideosCommand($httpClient));
$application->add(new ConvertMp4ToMp3Command());
$application->run();