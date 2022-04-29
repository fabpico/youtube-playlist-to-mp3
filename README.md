# Youtube playlist to mp3

## Set up

1. Create a Google API key, and put it in `.env` (see `.env.sample`)
2. Install Docker
3. Build container: Run `docker-compose build`
4. Start container: Run `docker-compose up -d`
5. Bash into PHP: Run `docker-compose exec php bash`
6. Install PHP packages: In bash, run `composer install`

## Usage

1. Bash into PHP: Run `docker-compose exec php bash`
2. In bash: Run `php cli.php fabpico:convert [playlistId]`

Your mp3 files will be located in `./data/mp3`.