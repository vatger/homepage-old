<template>
    <div class="content-wrapper">
        <!-- Dashboard Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ trans('event.event') }} - {{ trans('event.eventroutes') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/membership">{{ trans('dashboard.dashboard') }}</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <router-link to="/membership/event">{{ trans('event.event') }}</router-link>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6" v-for="er in eventroutes" v-bind:key="er.id">

                        <div class="card card-widget widget-user">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            <div class="widget-user-header bg-black"  :style="'background: url('+ er.img_url +') center center;'">
                            </div>
                            <div class="box-footer mt-2 mb-1">
                                <div class="row">
                                    <div class="col-sm-6 border-right">
                                        <div class="description-block">
                                            <h3>{{ er.name }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ er.begins_at | moment('utc', 'DD.MM.YYYY HH:mm') }}</h5>
                                            <span class="description-text">Start</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ er.ends_at | moment('utc', 'DD.MM.YYYY HH:mm') }}</h5>
                                            <span class="description-text">Ende</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer d-flex justify-content-center mt-2 mb-2" v-if="detailevent == null || detailevent != null && er.id != detailevent.id">
                                <button class="btn btn-primary" v-on:click="loadRouteLegs(er)">Details <i class="fa fa-arrow-circle-right"></i></button>
                            </div>

                            <div class="box-footer mr-5 ml-5" v-if="detailevent != null && er.id == detailevent.id">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="description-block">

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="description-block">
                                            <h5 class="description-header">{{ trans('event.participationstatus') }}</h5>
                                            <span class="description-text" v-if="er.joined_by_me"><i class="fas fa-check"></i> {{ trans('event.signedin') }}</span>
                                            <span class="description-text" v-else><i class="fas fa-times"></i> {{ trans('event.signedout') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="description-block">
                                            <button class="btn btn-secondary" v-if="er.joined_by_me" v-on:click="close(er)">close</button>
                                            <button class="btn btn-success" v-else v-on:click="signup(er)">{{ trans('event.signin') }}</button>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <p>{{ er.description }}</p>
                                <br>
                                <ul class="list-unstyled">
                                    <li><b>{{ trans('event.require_order') }}</b> {{ er.require_order ? 'yes' : 'no'}}</li>
                                    <li><b>{{ trans('event.flight_rules') }}</b> {{ er.flight_rules == null ? 'IFR + VFR' : er.flight_rules + 'FR only' }}</li>
                                    <li><b>{{ trans('event.aircrafts') }}</b> {{ er.aircrafts == "" ? 'all' : er.aircrafts + ' only' }}</li>
                                    <li><b>{{ trans('event.badge') }}</b> {{ er.forum_badge_id != null ? 'auto' : 'manual' }}</li>
                                </ul>
                                <div class="d-flex justify-content-right">
                                    <span class="">Für mehr Infos klicke <a :href="er.link">hier</a>.</span>
                                </div>
                                <hr>

                                <div class="row" v-for="leg in detaillegs" v-bind:key="leg.id">
                                    <div class="col-sm-6 border-right text-right">
                                        <span class="description-text">
                                            <i class="fa fa-plane"></i>
                                            {{ leg.departure_aerodrome.icao }} <small>({{ leg.departure_aerodrome.country }})</small>
                                             -
                                            {{ leg.arrival_aerodrome.icao }} <small>({{ leg.arrival_aerodrome.country }})</small>

                                        </span>
                                    </div>
                                    <div class="col-sm-6" v-if="legstate(leg)=='done'">
                                        <i class="fa fa-check-circle"></i> {{ trans('event.completed') }} <small>({{ leg.my_pivot.completed_at | moment('DD.MM.YYYY HH:mm') }}z)</small>
                                    </div>
                                    <div class="col-sm-6" v-if="legstate(leg)=='open'">
                                        <i class="fa fa-times-circle"></i> {{ trans('event.open') }}
                                    </div>
                                    <div class="col-sm-6" v-if="legstate(leg)=='locked'">
                                        <i class="fa fa-lock"></i> {{ trans('event.locked') }}
                                    </div>
                                </div>
                                <br>
                                <p>{{ trans('event.footerdescription') }}</p>
                                <br>
                                <p><button class="btn btn-danger" v-if="er.joined_by_me" v-on:click="signout(er)">{{ trans('event.signout') }}</button></p>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    data() {
        return {
            eventroutes: [],
            detailevent: null,
            detaillegs: [],
        };
    },
    methods: {
        loadEventRoutes() {
            axios.get("/api/event/routes").then((res) => {
                this.eventroutes = res.data;
            });
        },
        loadRouteLegs(er){
            this.detailevent = er;
            this.detaillegs = [];
            axios.get("/api/event/routes/" + er.id).then((res) => {
                this.detaillegs = res.data;
            });
        },
        close(er){
            this.detailevent = null;
            this.detaillegs = [];
        },
        signup(er){
            axios.get("/api/event/routes/" + er.id + "/signup").then((res) => {
                er.joined_by_me = true;
                this.loadRouteLegs(er);
            });
        },
        signout(er){
            axios.get("/api/event/routes/" + er.id + "/signout").then((res) => {
                er.joined_by_me = false;
                this.loadRouteLegs(er);
            });
        },
        legstate(leg){
            if(!this.detailevent.joined_by_me || leg.my_pivot == null) return 'locked';
            if(leg.my_pivot.completed_at != null) return 'done';
            if(!this.detailevent.require_order) return 'open';
            for (var i = 0; i < this.detaillegs.length; i++) {
                if(this.detaillegs[i].id == leg.id) return 'open';
                if(this.detaillegs[i].my_pivot == null || this.detaillegs[i].my_pivot.completed_at == null) return 'locked';
            }
            return null;
        }
    },
created() {
        this.loadEventRoutes();
    },
};
</script>
