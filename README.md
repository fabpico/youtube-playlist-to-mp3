# YouTube playlist to mp3

Download YouTube videos as mp3 files.

## Requirements

- Docker
- [Google API key](https://cloud.google.com/docs/authentication/api-keys#create)
- A playlist ID of a public or unlisted YouTube playlist. This is found in the URL
  like https://www.youtube.com/playlist?list=[playlistId].

## Install

1. Add `./app/.env` (see `./app/.env.sample`)
2. Bash into the container `docker compose up -d`, `docker compose exec php sh`
3. Install packages `composer install`

## Use

1. Bash into the container `docker compose up -d`, `docker compose exec php sh`
2. Run: `php cli.php fabpico:convert [playlistId]`

Your mp3 files will be located in `./data/mp3`.