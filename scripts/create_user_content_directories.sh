map_folder="../public/map-images"
crew_folder="../public/crew-member-images"

if ([ ! -d "$map_folder" ] && [ ! -d "$crew_folder" ]); then
    mkdir -p -v $map_folder $crew_folder
    chmod g+w $map_folder
    chmod g+w $crew_folder
else
    echo "${map_folder} and ${crew_folder} already exist"
fi