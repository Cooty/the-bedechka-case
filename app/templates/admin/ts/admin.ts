import "core-js/stable";
import "regenerator-runtime/runtime";

import * as $ from "jquery";
import "bootstrap";

import "../scss/admin.scss";
import {toggleSidebarMenu} from "./sidebar-menu";
import {init as initCustomFile} from "./show-file-names-in-uploads";
import {init as initDeleteEntity} from "./delete-entity";

class Admin {
    private static instance: Admin;

    private constructor() {}

    public static getInstance(): Admin {
        if (!Admin.instance) {
            Admin.instance = new Admin();
        }

        return Admin.instance;
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
const app = Admin.getInstance();
Object.freeze(app);

app.init();