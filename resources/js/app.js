/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// Let axios know about the api token
let apiToken = document.head.querySelector('meta[name="token"]');
if (apiToken) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + apiToken.content;
}

// Import dashboard javascript
require('admin-lte');

window.Vue = require('vue');
/**
 * Prototype translation functions for vue application
 */
window.Vue.prototype.selectedLanguage = window.selectedLanguage;
window.Vue.prototype.trans = window.trans;
window.Vue.prototype.trans_choice = window.trans_choice;

// Install Vue Router
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Import aync computed properties
import AsyncComputed from 'vue-async-computed';
Vue.use(AsyncComputed);

// Import Moment
import moment from 'moment';
import VueMoment from 'vue-moment';
Vue.use(VueMoment, {moment});

/**
 * Register vue components
 */
// Global components needed


// Membership Components
const EventRoutesComponent = require('./components/membership/EventRoutesComponent').default;

// Administration Components
const AdministrationComponent = require('./components/administration/AdministrationComponent').default;
const AdministrationEventRoutes = require('./components/administration/events/EventRouteComponent').default;




// After all components have been loaded
// we can declare routes to them
const routes = [
    // Membership Route
    {
        path: '/membership',
        component: EventRoutesComponent,
        children: [
            {
                path: 'events',
                component: EventRoutesComponent,
                children: [
                    {
                        path: 'routes', //maybe better name
                        component: EventRoutesComponent
                    }
                ]
            },
        ]
    },
    // Administration Routes
    {
        path: '/administration',
        component: AdministrationComponent,
        children: [
            {
                path: 'events/routes',
                component: AdministrationEventRoutes
            },
            {
                path: '',
                component: AdministrationEventRoutes
            }
        ]
    }
]

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const router = new VueRouter({
    mode: 'history',
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
})

const app = new Vue({
    el: '#app',
    router: router
});


/**
 * Register channels to listen to
 */
