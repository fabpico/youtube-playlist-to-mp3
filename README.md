# YouTube playlist to mp3

Download YouTube videos as mp3 files.

## Requirements

- Docker
- [Google API key](https://cloud.google.com/docs/authentication/api-keys#create)
- A playlist ID of a public or unlisted YouTube playlist. This is found in the URL
  like https://www.youtube.com/playlist?list=[playlistID].

## Install

1. Add `./.docker/.env`, `./app/.env` (see samples)
2. Start the php container: `cd .docker && docker compose up -d`
3. In the php container: `composer install`

## Use

1. In the php container: `php cli.php fabpico:convert [playlistID]`

Your mp3 files will be located in `./data/mp3`.