import "../../scss/admin/app.scss";

class App {
    private static instance: App;

    private constructor() {}

    public static getInstance(): App {
        if (!App.instance) {
            App.instance = new App();
        }

        return App.instance;
    }

    public init() {
        console.log('admin script initialized');
        const $ = require('jquery');
        // this "modifies" the jquery module: adding behavior to it
        // the bootstrap module doesn't export/return anything
        require('bootstrap');


        // or you can include specific pieces
        // require('bootstrap/js/dist/tooltip');
        // require('bootstrap/js/dist/popover');

        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();