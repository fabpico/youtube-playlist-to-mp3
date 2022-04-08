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
        $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
        return Command::SUCCESS;
    }
}