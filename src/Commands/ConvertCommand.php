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
        $this->log("Items count: $itemsCount", $output);
        foreach ($playlistItems as $playlistItem) {
            $this->convertItem($playlistItem, $output);
        }
        return Command::SUCCESS;
    }

    private function getPlaylistItems(string $playlistId, string $pageToken = null): array
    {
        $apiFilters = http_build_query([
            'part' => 'snippet',
            'maxResults' => 50, // is the maximum for one call
            'playlistId' => $playlistId,
            'key' => $_ENV['API_KEY'],
            'pageToken' => $pageToken,
        ]);
        $playlistUrl = "https://www.googleapis.com/youtube/v3/playlistItems?$apiFilters";
        $playlistResponse = $this->httpClient->get($playlistUrl);
        $recursivePlaylistItems = array_key_exists('nextPageToken', $playlistResponse) ?
            $this->getPlaylistItems($playlistId, $playlistResponse['nextPageToken']) :
            [];

        $playlistItems = array_map(function (array $item): array {
            return [
                'title' => $item['snippet']['title'],
                'videoId' => $item['snippet']['resourceId']['videoId']
            ];
        }, $playlistResponse['items']);
        return array_merge($playlistItems, $recursivePlaylistItems);
    }

    private function sanitizeTitle(string $title): string
    {
        $title = str_replace('/', '-', $title);
        $title = str_replace('$', '', $title);
        $title = str_replace('"', "'", $title);
        $title = str_replace(':', ' -', $title);
        return $title;
    }

    private function convertItem(array $playlistItem, OutputInterface $output): void
    {
        $title = $playlistItem['title'];
        $this->log("Process \"$title\"..", $output);
        $sanitizedTitle = $this->sanitizeTitle($title);
        $targetPath = "data/mp3/{$sanitizedTitle}.mp3";
        $videoDownloadPath = "data/mp4/{$sanitizedTitle}.mp4";
        if (file_exists($targetPath)) {
            $this->log("Skip. Mp3 exists.", $output);
            return;
        }
        if (!file_exists($videoDownloadPath)) {
            $this->downloadVideo($playlistItem['videoId'], $videoDownloadPath, $output);
        }
        $this->convert($videoDownloadPath, $targetPath, $output);
    }

    private function downloadVideo(string $videoId, string $videoDownloadPath, OutputInterface $output): void
    {
        $this->log("Extract video URL..", $output);
        $mp4Url = $this->extractMp4Url($videoId);
        if (!$mp4Url) {
            $this->log("Video URL could not be extracted.", $output);
            return;
        }
        $this->log("Downloading video ..", $output);
        file_put_contents($videoDownloadPath, fopen($mp4Url, 'r'));
    }

    private function convert(string $videoDownloadPath, string $targetPath, OutputInterface $output): void
    {
        $this->log("Converting..", $output);
        exec("ffmpeg -loglevel error -i \"$videoDownloadPath\" \"$targetPath\"");
        $this->log("Converted.", $output);
    }

    private function extractMp4Url(string $videoId): ?string
    {
        $videoUrl = "https://www.youtube.com/watch?v=$videoId";
        $output = [];
        exec("yt-dlp --get-url $videoUrl", $output);
        if (!array_key_exists(1, $output)) {
            return null;
        }
        return $output[1]; // index 0 url is a video without audio
    }

    private function log(string $message, OutputInterface $output): void
    {
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $output->writeln("[$time] $message");
    }
}