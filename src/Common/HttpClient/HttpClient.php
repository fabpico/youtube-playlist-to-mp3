<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Common\HttpClient;

interface HttpClient
{
    public function get(string $url): array;
}