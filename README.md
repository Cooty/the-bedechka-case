# Website for the Bedechka Case Documentary project
## Develop locally

The app is dockerized for **local development**. All files related to the actual application reside in the `app/` folder.

**Prerequitites:**
- [Node.js](https://nodejs.org/en/) (>= v8) - _for working with the frontend files_.
- [Docker](https://docs.docker.com/get-started/)
- [Docker Compose](https://docs.docker.com/compose/)

**Getting up and running:**
1) Run `make build` to build the images.

2) Run `make up` to start all services.

3) Run `make composer-install` to install the PHP dependencies in the `app` container.

4) Run `make create-env-file` to create the `.env.dev.local` file inside the has all the environment variables needed for the development version of the app.

5) Manually create a file named `google_client_secret.json` inside the root of the `app/` directory and fill it's content with the production values that are needed to connenct to the YouTube API. **NOTE:** This part of the setup is **not** automated, the secret values are stored in a password manager. If you need them send an email to [cooty13@gmail.com](mailto:cooty13@gmail.com)

6) Run `make create-db`

7) Run `make run-migrations`

8) Run `make create-user-content-directories` to create the directories for user uploaded content (files uploaded on the admin area).

9) `run make email=<someone@example.com> create-admin-user` to create an admin user locally. The value of the `email` variable has to be a **valid** email address format (but it doesn't have to actually exist). You'll see a randomly generated password in the command prompt. You can login with this the first time and you'll be prompted to change it.

10) Build the frontend files if you have an exception complaining about `manifest.json` not being found (it's an error coming from [WebPack Encore](https://github.com/symfony/webpack-encore)). The frontend files have to be built on the host machine, so do `cd app`, `npm install` and then `npm run build`.

At this point you should have the website running in development mode on `http//:localhost`, the admin area can be reached from `http//:localhost/admin`

**Note:** Steps 3-9 should be optional if you've already run the site locally.

If you want to change frontend files, then you'll have to run `cd app/ && npm run watch` on your host machine. After that changes to SCSS and TS files should be automatically reflected in the browser. (Reload is neccesseray - **TODO:** Add hot reload to dev environment!)

## Backend

The app is built with [Symfony](https://symfony.com/) (>=v5). We use [Doctrine ORM](https://www.doctrine-project.org/) to manage the DB layer and the entites.

The entities are managed by a custom admin interface, and forms are generated to handle the CRUD operations, via Symfony's standard [form interface](https://symfony.com/doc/current/forms.html).

The "Partners" section is stored in a static json file in the `data` directory.

Inernationalization is handled through [Symfony's translation package](https://symfony.com/doc/current/translation.html), routes are also translated and the every page has it's mirrored route. The only exception are "News articles" which are links relating to the movie. Since there can be 3rd party websites writting about it in German, Bulgarian, English, Hungarian, etc, these are not internationalized and all language versions recieve the same content (latter we might include a language filter).

The transaltion copy itself is found in the `translations` folder in XML format, right now change in these texts requires code deployment and the files are inclued in VCS (we might outsource this to a GUI and generate them dynamically in the future).

## Frontend
We use [Symfony's Webpack Encore package](https://github.com/symfony/webpack-encore) to connenct the frontend build pipeline to the server-side rendered templates. See `app/webpack.config.js` for details.

CSS is written in [SCSS](https://sass-lang.com/), while JS is written in [TypeScript](https://www.typescriptlang.org/).

Frontend files are organized according to "packages", so each route or business domain has it's own package, with all the `.scss`, `.ts`, `.twig` and graphic asset files in one folder. These are located in the `app/templates` directory. The `.scss` files in these folders whose filename doesn't begin with an `_` will be compiled into actual CSS files that will be loaded only on the given route (this is done via Twig blocks).

There are also files in `templates/scss` and `templates/ts` these are global files or helpers imported in other files.

The frontend of the **admin** part of the website is built with [Bootstrap (v4.4)](https://getbootstrap.com/docs/4.4/getting-started/introduction/), while the client-facing site doesn't use any CSS/JS library nor framework, apart from [Leaflet.js](https://leafletjs.com/) used for interactive maps.

We use [BEM](http://getbem.com/introduction/) CSS naming conventions on the client-facing site's code-base.
### Webfonts
Webfonts are added using CSS's `@font-face` feature, the font-files are self-hosted, and they can be found under `assets/fonts`.
The reason for self-hosting these fonts is that this way we have precise control over cache headings (we use a long-term caching on static assets via Nginx) and also to avoid GDPR-related potential [legal issues](https://usercentrics.com/knowledge-hub/google-fonts-gdpr-compliant/).

The two fonts we currently use are [Amatic SC](https://fonts.google.com/specimen/Amatic+SC?query=Amatic) and [Roboto](https://fonts.google.com/specimen/Roboto).
Both families were downloaded from [Google Fonts](https://fonts.google.com) web-font files were generated via [Font Squirrel's webfont generator](https://www.fontsquirrel.com/tools/webfont-generator).
Both folders for the respective font files contain a file called `generator_config.txt`, with this you should be able to recreate the exact settings that were used to generate the files.

If you add new fonts (either using Font Squirrel or other tool to generate the final assets) please make sure to add somekind of generator setting files (or if not possible, then manual documentation), how the files were generated and add this to VCS! 
