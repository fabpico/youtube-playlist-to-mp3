<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Commands;

use Fabpico\YoutubePlaylistToMp3\Common\Httpclient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DownloadVideosCommand extends Command
{
    protected static $defaultName = 'fabpico:download-videos';

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
        $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=$playlistId&key={$_ENV['API_KEY']}";
        $data = $this->httpClient->get($url);
        $items = $data['items'];
        $reducedItems = array_map(function (array $item): array {
            return [
                'title' => $item['snippet']['title'],
                'videoId' => $item['snippet']['resourceId']['videoId']
            ];
        }, $items);

        $this->downloadVideos($reducedItems, $output);
        return Command::SUCCESS;
    }

    private function downloadVideos(array $items, OutputInterface $output): void
    {
        $itemsCount = count($items);
        $output->writeln("Downloading videos: $itemsCount");
        foreach ($items as $item) {
            $output->writeln("Extracting URL: {$item['title']}");
            $mp4Url = $this->extractMp4Url($item['videoId']);
            $output->writeln("Downloading: {$item['title']}");
            file_put_contents("data/mp4/{$item['title']}.mp4", fopen($mp4Url, 'r'));
        }
    }

    private function extractMp4Url(string $videoId): string
    {
        $videoUrl = "https://www.youtube.com/watch?v=$videoId}";
        $content = file_get_contents($videoUrl);
        $fromBetween = '"formats"';
        $toBetween = 'video/mp4';
        $extractingFormatsJson = $this->getStringBetween($fromBetween, $toBetween, $content);
        $extractingFormatsJson = str_replace('"formats":[', '', $extractingFormatsJson);
        $extractingFormatsJson = str_replace(',"mimeType":"', '', $extractingFormatsJson) . '}';
        $extractingFormatsArray = json_decode($extractingFormatsJson, true);
        return $extractingFormatsArray['url'];
    }

    private function getStringBetween(string $from, string $to, string $haystack): string
    {
        $fromPosition = strpos($haystack, $from);
        $toPosition = strpos($haystack, $to, $fromPosition);
        $betweenLength = $toPosition - $fromPosition;
        return substr($haystack, $fromPosition, $betweenLength);
    }
}