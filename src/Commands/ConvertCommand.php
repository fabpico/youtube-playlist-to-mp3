<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Commands;

use Fabpico\YoutubePlaylistToMp3\Common\Httpclient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConvertCommand extends Command
{
    protected static $defaultName = 'fabpico:convert';

    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
    }

    protected function configure(): void
    {
        $this->addArgument('playlistId', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $playlistId = $input->getArgument('playlistId');
        $playlistItems = $this->getPlaylistItems($playlistId);
        $itemsCount = count($playlistItems);
        $output->writeln("Items count: $itemsCount");
        foreach ($playlistItems as $playlistItem) {
            $this->convertItem($playlistItem, $output);
        }
        return Command::SUCCESS;
    }

    private function getPlaylistItems(string $playlistId): array
    {
        $playlistUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=$playlistId&key={$_ENV['API_KEY']}";
        $playlistResponse = $this->httpClient->get($playlistUrl);
        $playlistItems = $playlistResponse['items'];
        $reducedPlaylistItems = array_map(function (array $item): array {
            return [
                'title' => $item['snippet']['title'],
                'videoId' => $item['snippet']['resourceId']['videoId']
            ];
        }, $playlistItems);
        return $reducedPlaylistItems;
    }

    private function convertItem(array $playlistItem, OutputInterface $output): void
    {
        $title = $playlistItem['title'];
        $output->writeln("Process \"$title\"..");
        $sanitizedTitle = str_replace('/', '-', $title);
        $targetPath = "data/mp3/{$sanitizedTitle}.mp3";
        if (file_exists($targetPath)) {
            $output->writeln("Skip. Mp3 exists.");
            return;
        }
        $output->writeln("Extract video URL..");
        $mp4Url = $this->extractMp4Url($playlistItem['videoId']);
        $videoDownloadPath = sys_get_temp_dir() . "/{$sanitizedTitle}.mp4";
        $output->writeln("Downloading video ..");
        file_put_contents($videoDownloadPath, fopen($mp4Url, 'r'));

        $output->writeln("Converting..");
        exec("ffmpeg -i -loglevel error \"$videoDownloadPath\" \"$targetPath\"");
        $output->writeln("Converted.");
    }

    private function extractMp4Url(string $videoId): string
    {
        $videoUrl = "https://www.youtube.com/watch?v=$videoId";
        $output = [];
        exec("youtube-dl --get-url $videoUrl", $output);
        return $output[1]; // index 0 url is a video without audio
    }
}