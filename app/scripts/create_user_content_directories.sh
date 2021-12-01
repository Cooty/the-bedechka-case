#!/usr/bin/env bash

IMAGE_FOLDER="../public/uploads/images"

if [ ! -d "$IMAGE_FOLDER" ]; then
    mkdir -p -v "$IMAGE_FOLDER"
    chmod g+w "$IMAGE_FOLDER"
else
    echo "All user upload folders already exist"
    exit 0
fi