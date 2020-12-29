MAP_FOLDER="../public/map-images"
CREW_FOLDER="../public/crew-member-images"
NEWS_FOLDER="../public/news-logos"
SCREENING_FOLDER="../public/screenings/"

if [ ! -d "$MAP_FOLDER" ]; then
    mkdir -p -v "$MAP_FOLDER"
    chmod g+w "$MAP_FOLDER"
elif [ ! -d "$CREW_FOLDER" ]; then
    mkdir -p -v "$CREW_FOLDER"
    chmod g+w "$CREW_FOLDER"
elif [ ! -d "$NEWS_FOLDER" ]; then
    mkdir -p -v "$NEWS_FOLDER"
    chmod g+w "$NEWS_FOLDER"
elif [ ! -d "$SCREENING_FOLDER" ]; then
    mkdir -p -v "$SCREENING_FOLDER"
    chmod g+w "$SCREENING_FOLDER"
else
    echo "All user upload folders already exist"
    exit 0
fi