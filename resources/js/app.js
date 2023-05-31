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
Vue.component('setup', require('./components/membership/SetupComponent.vue').default);
Vue.component('atc-live-component', require('./components/network/AtcLiveComponent.vue').default);
Vue.component('atc-bookings-component', require('./components/booking/AtcBookingsComponent.vue').default);
Vue.component('news-feed', require('./components/forum/NewsFeed.vue').default);
Vue.component('statistics-aerodromes-component', require('./components/statistics/AerodromesComponent.vue').default);

// Membership Components
const Membership = require('./components/membership/MembershipComponent').default;
const MembershipDashboard = require('./components/membership/DashboardComponent').default;
const Notifications = require('./components/membership/NotificationsComponent').default;
const AtcBookingComponent = require('./components/booking/MakeAtcBookingComponent').default;
const RegionalgroupComponent = require('./components/membership/RegionalgroupComponent').default;
const RegionalgroupRequestComponent = require('./components/membership/RegionalgroupRequestComponent').default;
const EventRoutesComponent = require('./components/membership/EventRoutesComponent').default;

// Administration Components
const AdministrationComponent = require('./components/administration/AdministrationComponent').default;
const AdministrationDownloader = require('./components/administration/AdministrationDownloadComponent').default;
const AdministrationDashboard = require('./components/administration/DashboardComponent').default;
const AdministrationMembership = require('./components/administration/membership/AccountsComponent').default;
const AdministrationMember = require('./components/administration/membership/AccountComponent').default;
const AdministrationGroups = require('./components/administration/membership/GroupComponent').default;
const AdministrationStation = require('./components/administration/navigation/StationComponent').default;
const AdministrationAerodrome = require('./components/administration/navigation/AerodromeComponent').default;
const AdministrationChart = require('./components/administration/navigation/ChartComponent').default;
const AdministrationNavaids = require('./components/administration/navigation/NavaidComponent').default;
const AdministrationAip = require('./components/administration/navigation/AipComponent').default;
const AdministrationGroundlayoutGenerator = require('./components/administration/navigation/GroundlayoutGenerator').default
const AdministrationSectorfilecombiner = require('./components/administration/navigation/SectorfileCombiner').default;
const AdministrationRegionalgroups = require('./components/administration/regionalgroup/RegionalgroupsComponent').default;
const AdministrationRegionalgroup = require('./components/administration/regionalgroup/RegionalgroupComponent').default;
const AdministrationATD = require('./components/administration/atd/ATDComponent').default;
const AdministrationEventRoutes = require('./components/administration/events/EventRouteComponent').default;
const AdministrationForumGroups = require('./components/administration/forum/ForumGroupComponent').default;
const AdministrationImagesUploader = require('./components/administration/images/UploaderComponent').default;
const AdministrationImagesManager = require('./components/administration/images/ManagerComponent').default;
const AdministrationEuroScope = require('./components/administration/euroscope/EuroScopeComponent').default;
const AdministrationGitlab = require('./components/administration/services/GitlabComponent').default;

// Pilots Components
const Pilots = require('./components/pilots/PilotsComponent').default;
const PilotsDashboard = require('./components/pilots/DashboardComponent').default;
const PilotsAerodrome = require('./components/pilots/AerodromeComponent').default;
const PilotsAerodromes = require('./components/pilots/AerodromesComponent').default;
const PilotsLivemap = require('./components/network/LivemapComponent').default;
const PilotsWeatherComponent = require('./components/pilots/WeatherComponent').default;

// Controller Components
const Controllers = require('./components/controllers/ControllerComponent').default;
const ControllersAtdSoloComponent = require('./components/controllers/Atd/SoloComponent').default;

// After all components have been loaded
// we can declare routes to them
const routes = [
    {
        path: '/controllers',
        component: Controllers,
        children: [
            {
                path: 'atd/solos',
                component: ControllersAtdSoloComponent
            }
        ]
    },
    // Frontend Pilot Routes
    {
        path: '/pilots',
        component: Pilots,
        children: [
            {
                path: 'livemap',
                component: PilotsLivemap
            },
            {
                path: 'weather',
                component: PilotsWeatherComponent
            },
            {
                path: 'aerodrome/:icao',
                component: PilotsAerodrome
            },
            {
                path: 'aerodromes/:icao',
                component: PilotsAerodrome
            },
            {
                path: 'aerodrome',
                component: PilotsAerodromes
            },
            {
                path: 'aerodromes',
                component: PilotsAerodromes
            },
            {
                path: '',
                component: PilotsDashboard
            }
        ]
    },
    // Membership Route
    {
        path: '/membership',
        component: Membership,
        children: [
            {
                path: 'regionalgroups',
                component: RegionalgroupComponent
            },
            {
                path: 'regionalgrouprequests',
                component: RegionalgroupRequestComponent
            },
            // Atc Session Booking
            {
                path: 'booking',
                component: AtcBookingComponent
            },
            // Notifications
            {
                path: 'notifications',
                component: Notifications
            },
            // Membership Dashboard
            {
                path: '',
                component: MembershipDashboard
            },
            {
                path: 'events',
                component: Membership,
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
                path: 'download/:filepath',
                component: AdministrationDownloader
            },
            {
                path: 'forum/group',
                component: AdministrationForumGroups
            },
            {
                path: 'aip',
                component: AdministrationAip
            },
            {
                path: 'atd',
                component: AdministrationATD
            },
            {
                path: 'events/routes',
                component: AdministrationEventRoutes
            },
            {
                path: 'images/manager',
                component: AdministrationImagesManager
            },
            {
                path: 'images/uploader',
                component: AdministrationImagesUploader
            },
            {
                path: 'regionalgroups/:id',
                component: AdministrationRegionalgroup
            },
            {
                path: 'regionalgroups',
                component: AdministrationRegionalgroups
            },
            {
                path: 'aerodrome',
                component: AdministrationAerodrome
            },
            {
                path: 'station',
                component: AdministrationStation
            },
            {
                path: 'chart',
                component: AdministrationChart
            },
            {
                path: 'navaids',
                component: AdministrationNavaids
            },
            {
                path: 'sectorcombine',
                component: AdministrationSectorfilecombiner
            },
            {
                path: 'glg',
                component: AdministrationGroundlayoutGenerator
            },
            {
                path: 'membership/:id',
                component: AdministrationMember
            },
            {
                path: 'membership',
                component: AdministrationMembership
            },
            {
                path: 'group',
                component: AdministrationGroups
            },
            {
                path: 'euroscope',
                component: AdministrationEuroScope
            },
            {
                path: 'services/gitlab',
                component: AdministrationGitlab
            },
            {
                path: '',
                component: AdministrationDashboard
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
if (window.appUser != undefined && window.appUser != null) {
    window.Echo.private('App.Models.Membership.Account.' + window.appUser.user)
        .notification((notification) => {
            $(document).Toasts('create', {
                title: notification.title,
                body: notification.message,
                autohide: true,
                delay: 5000,
                class: 'bg-info'
            })
        });
}
