cd ..
docker compose up -d
docker compose exec php composer install
read -p "Done"