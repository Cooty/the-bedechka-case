# Components coupled to JavaScript

**IMPORTANT!**
This is deprecated: Move all "modular styles" to the respective folder in `templates/componets`.
The plan is to have all frontend files next to the twig files from where they are served.

These components are tied to some JS functionality, so they are imported by a corresponding JS (TypeScript) module (see `../ts`).

By contrast, styles that are not coupled with JS are added to some entry file which is imported by an entry TypeScript file (see `webpack.config.ts`), however these styles are all needed by a smaller TS module,
this is useful for code splitting.

The TS module that imports them should have the same name as the file or folder seen here.

**Note:** The files must all explicitly import their dependencies. So if a CSS rules uses a color variable form `config/_colors.scss`, then that file must have an `@import "../config/colors"` on top of it or else Webpack's SASS loader will fail.