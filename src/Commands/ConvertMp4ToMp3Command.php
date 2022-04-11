<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConvertMp4ToMp3Command extends Command
{
    protected static $defaultName = 'fabpico:convert-mp4-to-mp3';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mp4FolderItems = scandir('data/mp4');
        foreach ($mp4FolderItems as $mp4FolderItem) {
            $pathInfo = pathinfo($mp4FolderItem);
            if (!$pathInfo['extension']) {
                continue;
            }
            if (!$pathInfo['filename']) {
                continue;
            }
            $filename = $pathInfo['filename'];
            $mp4Path = "data/mp4/$filename.mp4";
            $mp3Path = "data/mp3/$filename.mp3";
            if (file_exists($mp3Path)) {
                $output->writeln("Skip [already done]: {$filename}");
                continue;
            }
            $output->writeln("Converting: {$filename}");
            exec("ffmpeg -i \"$mp4Path\" \"$mp3Path\"");
        }
        return Command::SUCCESS;
    }

}