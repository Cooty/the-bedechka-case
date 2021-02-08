import "core-js/stable";
import "regenerator-runtime/runtime";

import * as $ from "jquery";
import "bootstrap";

import "../../scss/admin/app.scss";
import {toggleSidebarMenu} from "./sidebar-menu";
import {init as initCustomFile} from "./show-file-names-in-uploads";
import {init as initDeleteEntity} from "./delete-entity";

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
        // @ts-ignore
        $('[data-toggle="dropdown"]').dropdown();

        const toggler = document.querySelector('.js-sidebar-opener');
        if(toggler) {
            toggler.addEventListener('click', toggleSidebarMenu);
        }

        initCustomFile();
        initDeleteEntity();

        if(window._configAdmin.sortable) {
            import("./sortable").then(sortable => {
                sortable.init();
            })
        }
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();