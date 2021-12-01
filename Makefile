up:
	docker-compose up

down:
	docker-compose down

composer-install:
	docker-compose exec app composer install --no-scripts --prefer-dist --no-interaction

create-env-file:
	docker-compose exec app bash -c "cd /usr/src/app/scripts/ && chmod +x create_env_file.sh && ./create_env_file.sh"

create-user-content-directories:
	docker-compose exec app bash -c "cd /usr/src/app/scripts/ && chmod u+x create_user_content_directories.sh && ./create_user_content_directories.sh"