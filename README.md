# YouTube playlist to mp3

Download YouTube videos as mp3 files.

## Requirements

- Docker
- [Google API key](https://cloud.google.com/docs/authentication/api-keys#create)
- A public YouTube playlist ID (found in the URL like https://www.youtube.com/playlist?list={playlistId})

## Install

1. Put your Google API key into `.env` (see `.env.sample`)
2. Open PHP terminal: `docker compose up -d`,  `docker compose exec php bash`
3. Install PHP packages: `composer install`

## Use

1. Open PHP terminal: `docker compose up -d`,  `docker compose exec php bash`
2. Execute: `php cli.php fabpico:convert [playlistId]`

Your mp3 files will be located in `./data/mp3`.