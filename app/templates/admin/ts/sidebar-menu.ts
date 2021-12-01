const sidebar = document.querySelector('.js-sidebar');
const html = document.querySelector('html');

export const toggleSidebarMenu = ()=> {
    if(sidebar) {
        html.classList.toggle('sidebar-opened');
    }
};