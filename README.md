#Website for the Bedechka Case Documentary project

##Frontend

###Webfonts
Webfonts are added using CSS's `@font-face` feature, the font-files are self-hosted, and they can be found under `assets/fonts`.
The reason for self-hosting these fonts is that this way we have precise control over cache headings (we use a long-term caching on static assets via Nginx) and also to avoid GDPR-related potential [legal issues](https://usercentrics.com/knowledge-hub/google-fonts-gdpr-compliant/).

The two fonts we currently use are [Amatic SC](https://fonts.google.com/specimen/Amatic+SC?query=Amatic) and [Roboto](https://fonts.google.com/specimen/Roboto).
Both families were downloaded from [Google Fonts](https://fonts.google.com) web-font files were generated via [Font Squirrel's webfont generator](https://www.fontsquirrel.com/tools/webfont-generator).
Both folders for the respective font files contain a file called `generator_config.txt`, with this you should be able to recreate the exact settings that were used to generate the files.

If you add new fonts (either using Font Squirrel or other tool to generate the final assets) please make sure to add somekind of generator setting files (or if not possible, then manual documentation), how the files were generated and add this to VCS! 
