# Folder structure

**IMPORTANT!**
This is deprecated: Move all "modular styles" to the respective folder in `templates/componets`.
The plan is to have all frontend files next to the twig files from where they are served.

All of the root level `.scss` files are loaded by WebPack (using WebPack Encore's `.addEntry` method) as an entry point,
or if there's no TS file with the same name in the `../ts` folder's root, then they are used with WebPack Encore's `.addStyleEntry` method to generate standalone CSS.

For the organization of files and what-goes-where, see the README files inside each folder.