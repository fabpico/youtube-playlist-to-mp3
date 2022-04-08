<?php declare(strict_types=1);

require './vendor/autoload.php';

use Symfony\Component\Console\Application;
use Fabpico\YoutubePlaylistToMp3\Commands\DownloadVideosCommand;

$application = new Application();

// todo, autowire
$application->add(new DownloadVideosCommand());
$application->run();