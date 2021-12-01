#!/usr/bin/env bash

env_vars=(APP_ENV APP_SECRET HOST_NAME DATABASE_NAME DATABASE_URL MAILER_URL)
file_name=".env.dev.local"
if [ "$1" == "prod" ]; then
    file_name=".env"
fi
file_content=""

for e in "${env_vars[@]}"
do
    temp="$e=$(eval echo \$$e)"
    file_content+=${temp}"\n"
done

formatted=$(printf "$file_content")

echo "$formatted" > "../$file_name"