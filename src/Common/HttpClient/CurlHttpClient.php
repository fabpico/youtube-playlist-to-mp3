<?php declare(strict_types=1);

namespace Fabpico\YoutubePlaylistToMp3\Common\HttpClient;

use Symfony\Component\HttpClient\CurlHttpClient as SymfonyCurlHttpClient;

final class CurlHttpClient implements HttpClient
{
    private SymfonyCurlHttpClient $symfonyClient;

    public function __construct(SymfonyCurlHttpClient $symfonyClient)
    {
        $this->symfonyClient = $symfonyClient;
    }

    public function get(string $url): array
    {
        $response = $this->symfonyClient->request('GET', $url);
        return $response->toArray(true);
    }
}