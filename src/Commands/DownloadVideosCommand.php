<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DownloadVideosCommand extends Command
{
    protected static $defaultName = 'fabpico:download-videos';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('todo');
        return Command::SUCCESS;
    }
}