<?php declare(strict_types=1);

require './vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\CurlHttpClient as SymfonyCurlHttpClient;
use Fabpico\YoutubePlaylistToMp3\Common\HttpClient\CurlHttpClient;
use Fabpico\YoutubePlaylistToMp3\Commands\ConvertCommand;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$application = new Application();

// todo, autowire
$httpClient = new CurlHttpClient(new SymfonyCurlHttpClient());
$application->add(new ConvertCommand($httpClient));
$application->run();