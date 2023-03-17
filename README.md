# YouTube playlist to mp3

Download YouTube videos as mp3 files.

## Requirements

- Docker
- [Google API key](https://cloud.google.com/docs/authentication/api-keys#create)
- A playlist ID of a public or unlisted YouTube playlist. This is found in the URL
  like https://www.youtube.com/playlist?list=[playlistId].

## Install

1. Put your Google API key into `.env` (see `.env.sample`)
2. Run `scripts/start.sh`

## Use

1. Bash into the container
2. Run: `php cli.php fabpico:convert [playlistId]`

Your mp3 files will be located in `./data/mp3`.