<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Eventroutes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                Eventroutes
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title">Eventrouten</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-hover table-responsive-sm">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>name</th>
                            <th>begins_at</th>
                            <th>ends_at</th>
                            <th>flight_rules</th>
                            <th>require_order</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(er, index) in routes" v-bind:key="index">
                            <td>{{ er.id }}</td>
                            <td>{{ er.name }}</td>
                            <td>{{ er.begins_at | moment('utc','YYYY-MM-DD HH:mm') }}</td>
                            <td>{{ er.ends_at | moment('utc','YYYY-MM-DD HH:mm')  }}</td>
                            <td>{{ er.flight_rules == null ? 'I+V' : er.flight_rules}}</td>
                            <td>{{ er.require_order ? 'yes' : 'no' }}</td>
                            <td><button class="btn btn-secondary btn-sm" v-on:click="loadCompletedLegs(er)">Anzeigen</button></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr v-if="!createMode">
                            <td colspan="8"><button class="btn btn-default btn-block" v-on:click="createMode = true">Neue Route anlegen</button></td>
                        </tr>
                        <tr v-else>
                            <td>New Route</td>
                            <td><input class="form-control" type="text" v-model.trim="newRoute.name"></td>
                            <td><input class="form-control" type="text" v-model.trim="newRoute.begins_at"></td>
                            <td><input class="form-control" type="text" v-model.trim="newRoute.ends_at"></td>
                            <td>
                                <select class="form-control" v-model="newRoute.flight_rules">
                                    <option value="">I+V</option>
                                    <option value="I">I</option>
                                    <option value="V">V</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" v-model="newRoute.require_order">
                                    <option value="1">yes</option>
                                    <option value="0">no</option>
                                </select>
                            </td>
                            <td><button class="btn btn-success btn-sm" v-on:click="createRoute(newRoute)">Anlegen</button></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card card-primary card-outline" v-if="detailedRoute != null">
                <div class="card-header">
                    <h5 class="card-title">Eventroute #{{ detailedRoute.id }} {{ detailedRoute.name }} - Details</h5>
                    <button type="button" class="close" aria-label="Close" v-on:click="routeEditMode = !routeEditMode">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="card-body" v-if="routeEditMode">
                    <h2>Eventroute</h2>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>#</label>
                                <input class="form-control" type="text"  v-model="detailedRoute.id" readonly />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>name</label>
                                <input class="form-control" type="text" v-model.trim="detailedRoute.name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>description</label>
                        <input class="form-control" type="text" v-model.trim="detailedRoute.description">
                    </div>
                    <div class="form-group">
                        <label>img_url</label>
                        <input class="form-control" type="text" v-model.trim="detailedRoute.img_url">
                    </div>
                    <div class="form-group">
                        <label>link</label>
                        <input class="form-control" type="text" v-model.trim="detailedRoute.link">
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>begins_at (YYYY-MM-DD)</label>
                                <input class="form-control" type="text" v-model.trim="detailedRoute.begins_at">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>ends_at (YYYY-MM-DD)</label>
                                <input class="form-control" type="text" v-model.trim="detailedRoute.ends_at">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>flight_rules</label>
                                <select class="form-control" v-model="detailedRoute.flight_rules">
                                    <option value="">IFR+VFR</option>
                                    <option value="I">IFR</option>
                                    <option value="V">VFR</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>require_order</label>
                                <select class="form-control" v-model="detailedRoute.require_order">
                                    <option value="1">yes</option>
                                    <option value="0">no</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>aircrafts (ICAO Codes mit , getrennt)</label>
                        <input class="form-control" type="text" v-model.trim="detailedRoute.aircrafts">
                    </div>
                    <div class="form-group">
                        <label>forum badge id</label>
                        <input class="form-control" type="text" v-model.trim="detailedRoute.forum_badge_id">
                    </div>
                    <button class="btn btn-success" v-on:click="editRoute(detailedRoute)">Save edited eventroute</button>
                    <button class="btn btn-danger" v-on:click="deleteRoute(detailedRoute)">Delete eventroute</button>
                    <br>
                    <br>
                    <hr>
                    <br>
                    <h2>Legs</h2>
                    <div class="row">
                        <div class="col">
                            ID und Order
                        </div>
                        <div class="col">
                            departureaerodrome_id
                        </div>
                        <div class="col">
                            arrivalaerodrome_id
                        </div>
                        <div class="col">

                        </div>
                    </div>
                    <div class="row" v-for="leg in detailedLegData">
                        <div class="col">
                            <input type="number" class="form-control" v-model.number="leg.id" readonly>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" v-model.number="leg.departureaerodrome_id" readonly>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" v-model.number="leg.arrivalaerodrome_id" readonly>
                        </div>
                        <div class="col">
                            <button class="btn btn-danger" v-on:click="deleteLeg(detailedRoute, leg)">Delete leg</button>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col">
                            New Leg (ICAOs)
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" v-model="newLeg.departureaerodrome_icao">
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" v-model="newLeg.arrivalaerodrome_icao">
                        </div>
                        <div class="col">
                            <button class="btn btn-success" v-on:click="createLeg(detailedRoute, newLeg)">Add new leg</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-primary card-outline" v-if="detailedRoute != null">
                <div class="card-header">
                    <h5 class="card-title">Eventroute #{{ detailedRoute.id }} {{ detailedRoute.name }} - Teilnehmner</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered table-sm">
                        <thead>
                        <tr>
                            <th>account_id</th>
                            <th v-for="leg in detailedLegData"><small>{{ leg.departure_aerodrome.icao }}<br>{{ leg.arrival_aerodrome.icao }}</small></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="acc in detailedLegAccounts">
                                <td>{{ acc.account.id }}<br>{{ acc.account.firstname }} {{ acc.account.lastname }}</td>
                                <td v-for="leg in acc.legs">
                                    <button class="btn">
                                        <i class="fa fa-check" v-if="leg.completed_at != null"></i>
                                        <i class="fa fa-times" v-else></i>
                                    </button>
                                    <br>
                                    <button class="btn" v-if="leg.fight_data_id != null" v-on:click="loadFlightData(leg.fight_data_id)">
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn" v-if="leg.completed_at == null && leg.fight_data_id == null" v-on:click="manualCompleteLeg(detailedRoute, leg, acc.account)">
                                        <i class="fa fa-bug" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
        <div class="modal" style="display: block;" v-if="flightData != null">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Flight Data #{{ flightData.id }}</h4>
                    </div>
                    <div class="modal-body">
                        {{ flightData }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" v-on:click="flightData = null">Close</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal" style="display: block;" v-if="manualLeg != null">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Manual mark leg completed #{{ manualLeg.leg_id }}/{{
                                manualLeg.account.id }}</h4>
                    </div>
                    <div class="modal-body">
                        route_id: {{manualLeg.er.id}}, leg_id: {{manualLeg.leg_id}}, account_id: {{manualLeg.account.id}}
                        <div class="form-group">
                            <label>completed_at (YYYY-MM-DD)</label>
                            <input class="form-control" type="text" v-model.trim="manualLeg.completed_at">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success pull-left" v-on:click="saveManualCompleteLeg(manualLeg)">Save</button>
                        <button type="button" class="btn btn-default pull-left" v-on:click="manualLeg = null">Close</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<script>
export default {
    name: "EventRouteComponent",

    data() {
        return {
            createMode: false,
            accessDenied: false,
            routes: [],
            detailedRoute: null,
            detailedLegData: [],
            detailedLegAccounts: [],
            newRoute: {},
            newLeg: {},
            flightData: null,
            routeEditMode: false,
            manualLeg: null,
        }
    },
    methods: {
        loadRoutes() {
            axios.get('/api/administration/event/routes')
                .then(res => {
                    this.routes = res.data;
                }).catch(error => {
                if(error.response.status == 403) {
                    this.accessDenied = true;
                }
            });
        },
        loadCompletedLegs(er) {
            axios.get('/api/administration/event/routes/'+ er.id).then(res => {
                this.detailedRoute = er;
                this.detailedLegData = res.data;
                this.detailedLegAccounts = [];
                if(this.detailedLegData.length > 0){
                    this.detailedLegData[0].accounts.forEach((acc, idx) =>{
                        this.detailedLegAccounts[idx] = {account: acc, legs: []};
                        this.detailedLegData.forEach((leg) => {
                            this.detailedLegAccounts[idx].legs.push(leg.accounts.find(el => el.id == acc.id).pivot);
                        })
                    });
                }
            });
        },
        manualCompleteLeg(er, leg, account){
            this.manualLeg = {er: er, leg_id: leg.routeleg_id, leg: leg, account: account, completed_at: ""};
        },
        saveManualCompleteLeg(data){
            axios.patch('/api/administration/event/routes/'+ data.er.id + '/' + data.leg_id, {account_id: data.account.id, completed_at: data.completed_at}).then(res => {
                this.manualLeg = null;
                this.loadCompletedLegs(er);
            });

        },
        createRoute(er) {
            axios.post('/api/administration/event/routes', {route: er})
                .then(res => {
                    this.createMode = false;
                    this.detailedLegData = null;
                    this.loadRoutes();
                });
        },
        editRoute(er){
            axios.patch('/api/administration/event/routes/'+ er.id,{route: er})
                .then(res => {
                    this.loadRoutes();
                    this.loadCompletedLegs(er);
                });
        },
        deleteRoute(er){
            axios.delete('/api/administration/event/routes/'+ er.id)
                .then(res => {
                    this.detailedRoute = null;
                    this.detailedLegData = [];
                    this.detailedLegAccounts = [];
                    this.loadRoutes();
                });

        },
        createLeg(er, leg){
            axios.post('/api/administration/event/routes/'+ er.id, {leg: leg})
                .then(res => {
                    this.loadRoutes();
                    this.loadCompletedLegs(er);
                });
        },
        deleteLeg(er, leg){
            axios.delete('/api/administration/event/routes/'+ er.id + '/' + leg.id)
                .then(res => {
                    this.loadRoutes();
                    this.loadCompletedLegs(er);
                });
        },
        loadFlightData(data_id){
            axios.get('/api/administration/event/routes/flightdata/' + data_id)
                .then(res => {
                    this.flightData = res.data;
                });
        },

    },
    watch: {

    },
    activated() {
        this.loadRoutes();
    }
}
</script>

<style scoped>

</style>
