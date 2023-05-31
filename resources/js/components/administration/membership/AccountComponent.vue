<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Membership</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link to="/administration/membership">
                                    Membership
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active" v-if="selectedAccount != null">
                                {{ selectedAccount.id }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="accessDenied">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-danger">
                            <div class="card-header">
                                <h3 class="card-title">Zugang Verweigert</h3>
                            </div>
                            <div class="card-body">
                                Du hast nicht die erforderlichen Rechte, um diese Quelle aufzurufen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-else>
            <div class="container-fluid" v-if="selectedAccount != null">
                <div class="row">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">

                                <h3 class="profile-username text-center">{{ selectedAccount.firstname + ' ' + selectedAccount.lastname }}</h3>

                                <p class="text-muted text-center">{{ selectedAccount.id }}</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Flugstunden</b> <a class="float-right">{{ selectedAccount.data.time_pilot }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Lotsenstunden</b> <a class="float-right">{{ selectedAccount.data.time_atc }}</a>
                                    </li>
                                    <li class="list-group-item" v-for="(rg, index) in selectedAccount.regionalgroups" v-bind:key="index">
                                        <b>{{ rg.name }}</b> <a class="float-right">{{ rg.pivot.guest ? 'Gastmitglied' : 'Vollmitglied' }}</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- About Me Box -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Faktencheck</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <strong><i class="fas fa-book mr-1"></i> Ausbildung</strong>

                                <p class="text-muted" v-if="selectedAccount.data.controllerRating && selectedAccount.data.pilotRating">
                                    {{ selectedAccount.data.controllerRating.long }} / {{ selectedAccount.data.pilotRating }}
                                </p>
                                <p class="text-muted" v-else>
                                    {{ selectedAccount.data.rating_atc }} / {{ selectedAccount.data.rating_pilot }}
                                </p>

                                <hr>

                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Regionszuweisung</strong>

                                <p class="text-muted">{{ selectedAccount.data.region_name }}</p>

                                <p class="text-muted">{{ selectedAccount.data.division_name }}</p>

                                <p class="text-muted" v-if="selectedAccount.data.subdivision_code != null">{{ selectedAccount.data.subdivision_name }}</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Aktivitäten</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#notes" data-toggle="tab">Notizen</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#ts" data-toggle="tab">TeamSpeak</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#forum" data-toggle="tab">Forum</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#bans" data-toggle="tab">Sperren</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#impersonate" data-toggle="tab">Danger Area</a></li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="activity">
                                        <div class="timeline timeline-inverse">
                                            <!-- timeline time label -->
                                            <div class="time-label">
                                                <span class="bg-info">
                                                    {{ moment().utc().format("YYYY-MM-DD") }}
                                                </span>
                                            </div>
                                            <!-- /.timeline-label -->

                                            <!-- timeline item -->
                                            <div v-for="(action, index) in selectedAccount.actions" v-bind:key="index">
                                                <i class="fas fa-info bg-primary"></i>

                                                <div class="timeline-item">
                                                    <span class="time"><i class="far fa-clock"></i> {{ action.created_at | moment('DD.MM.YYYY HH:mm') }}z</span>

                                                    <h3 class="timeline-header" v-if="action.subject_type == 'App\Models\Membership\Account'">Membership Updated</h3>
                                                    <h3 class="timeline-header" v-else>{{ action.subject_type }}</h3>

                                                    <div class="timeline-body">
                                                        {{ action.description }}
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END timeline item -->
                                            <div>
                                                <i class="far fa-clock bg-gray"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="notes">
                                        <div class="post" v-for="(note, index) in selectedAccount.notes" v-bind:key="index">
                                            <div class="user-block">
                                                <span class="username">
                                                    <a href="#">{{ note.author.firstname + ' ' + note.author.lastname }}</a>
                                                    <a href="#" class="float-right btn-tool" v-on:click="removeNote(note)"><i class="fas fa-times"></i></a>
                                                </span>
                                                <span class="description">Geschrieben - {{ note.created_at }}</span>
                                            </div>
                                            <!-- /.user-block -->
                                            <p>
                                                {{ note.note }}
                                            </p>
                                        </div>

                                        <div class="post" v-if="errors">
                                            <ul class="alert alert-danger">
                                                <li v-for="(value, key) in validationErrors" v-bind:key="key">@{{ value }}</li>
                                            </ul>
                                        </div>

                                        <div class="post">
                                            <textarea class="form-control form-control-sm" type="text" placeholder="Neue Notiz" v-model="newNote"></textarea>
                                            <button type="submit" class="form-control form-control-sm" v-on:click="addNote">Hinzufügen</button>
                                        </div>

                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="ts">
                                        <div class="post" v-if="selectedAccount.teamspeak_registrations !=null && selectedAccount.teamspeak_registrations.length >0">
                                            <table class="table table-responsive-sm table-bordered table-hover">
                                                <thead>
                                                    <tr><th>TSUUID</th><th>DBID</th><th>letzte IP</th><th>zuletzt genutzt</th><th>erstellt</th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="ts in selectedAccount.teamspeak_registrations" v-bind:key="ts.id">
                                                        <td>{{ ts.uid }}</td>
                                                        <td>{{ ts.dbid }}</td>
                                                        <td>{{ ts.last_ip }}</td>
                                                        <td>{{ ts.last_login | moment('DD.MM.YYYY HH:mm') }}z</td>
                                                        <td>{{ ts.created_at | moment('DD.MM.YYYY HH:mm') }}z</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="post" v-else>
                                            <p class="text-danger">
                                                Dieser Nutzer hat keinen TeamSpeak Zugang.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="forum">
                                        <div class="post" v-if="selectedAccount.setting.forum_id != null">
                                            <p>
                                                Dieser Nutzer hat einen Forenaccount.<br>
                                                Die ID des Nutzers im Forum ist: {{ selectedAccount.setting.forum_id }}
                                            </p>
                                        </div>
                                        <div class="post" v-else>
                                            <p class="text-danger">
                                                Dieser Nutzer hat keinen Forenaccount.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="bans">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Vorhandene Sperren</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="post" v-for="(ban, index) in selectedAccount.bans" v-bind:key="index">
                                                    <div class="user-block">
                                                        <span class="username">
                                                            Ausgesprochen von
                                                            <a href="#">{{ ban.author.firstname + ' ' + ban.author.lastname }}</a>
                                                            <button class="btn bg-gradient-danger float-right btn-tool" v-on:click="removeBan(ban)">Aufheben</button>
                                                        </span>
                                                        <span class="description">Ausgesprochen am - {{ ban.created_at | moment('DD.MM.YYYY HH:mm') }}z</span>
                                                        <span class="description">Homepage {{ ban.homepage }}, Forum {{ ban.forum }}, Teamspeak {{ ban.teamspeak }}</span>
                                                        <span class="description" v-if="ban.permanent == 0">Ablauf der Sperre - {{ ban.banned_till | moment('DD.MM.YYYY HH:mm') }}z</span>
                                                        <span class="description" v-else>Ablauf der Sperre - nie (Permanent)</span>
                                                    </div>
                                                    <p>
                                                        {{ ban.reason }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Neue Sperre anlegen</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="post" v-if="errors">
                                                    <ul class="alert alert-danger">
                                                        <li v-for="(value, key) in validationErrors" v-bind:key="key">@{{ value }}</li>
                                                    </ul>
                                                </div>

                                                <div class="post mt-1">
                                                    <label class="form-label">Grund der Sperre</label>
                                                    <textarea class="form-control form-control-sm" type="text" placeholder="Grund der Sperre" v-model="banReason"></textarea>
                                                    <label class="form-label mt-4">Dauer der Sperre</label>
                                                    <div class="form-group row">
                                                        <div class="col-sm-1"></div>
                                                        <div class="form-check col-sm-4">
                                                            <input class="form-check-input" type="checkbox" checked v-model="banPermanent">
                                                            <label class="form-check-label">Permanente Sperre</label>
                                                        </div>
                                                        <p class="col-sm-1"> oder </p>
                                                        <div class="col-sm-5">
                                                            <label class="form-check-label">Ablauf der Sperre</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                </div>
                                                                <input type="text" class="form-control" v-model="banTill" v-bind:disabled="banPermanent">
                                                            </div>
                                                            <label class="form-check-label text-muted text-sm"> Format: yyyy-mm-dd (z.B.: 2020-05-31)</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row mt-4">
                                                        <div class="col-sm-2"><label class="form-label">Betroffene Services</label></div>
                                                        <div class="col-sm-10">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" v-model="banHomepage">
                                                                <label class="form-check-label">Homepage</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" v-model="banForum">
                                                                <label class="form-check-label">Forum</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" v-model="banTeamspeak">
                                                                <label class="form-check-label">Teamspeak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="form-control form-control-sm" v-on:click="addBan">Hinzufügen</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="impersonate">
                                        <div class="post">
                                            <p class="text-danger">
                                                In die Rolle des Nutzers schlüpfen?
                                            </p>
                                            <p>
                                                <a class="btn btn-default" :href="'/impersonate/take/'+selectedAccount.id">Als {{ selectedAccount.firstname+' '+selectedAccount.lastname }} nutzen.</a>
                                            </p>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
            <div class="container-fluid" v-else>
                Loading Data...
            </div>
        </section>
    </div>
</template>

<script>
import moment from "moment";

export default {
		data() {
			return {
                accessDenied: false,
				selectedAccount: null,
                errors: false,
                newNote: '',
                banReason: '',
                banPermanent: true,
                banTill: '',
                banHomepage: true,
                banForum: true,
                banTeamspeak: true,
			}
		},
		methods: {
            moment: function (args) {
                return moment(args);
            },
            /**
             * Select a clicked account and switch view to single user display
             * @param  {[type]} account [description]
             * @return {[type]}         [description]
             */
			selectAccount: function (id) {
				axios.get('/api/administration/membership/'+id)
					.then(res => {
						$("#membershipTable").DataTable().destroy();
						this.selectedAccount = res.data.account;
                        this.selectedAccount.actions = _.orderBy(res.data.actions.reverse(), ['updated_at', 'created_at'], ['desc', 'desc']); // Reverse the actions order to have latest on top
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			},
            /**
             * add a note to a selected user
             */
            addNote: function () {
                axios.post('/api/administration/membership/'+this.selectedAccount.id+'/note', {
                    newNote: this.newNote
                }).then(res => {
                    if(res.data != null) {
                        this.selectAccount(this.selectedAccount.id);
                    }
                }).catch(error => {
                    if (error.response.status == 422){
                        this.errors = error.response.data.errors;
                    }
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            /**
             * Remove a note from a selected user
             * @param  {[type]} note [description]
             * @return {[type]}      [description]
             */
            removeNote: function (note) {
                axios.delete('/api/administration/membership/'+this.selectedAccount.id+'/note/'+note.id)
                    .then(res => {
                        if(res.data) {
                            this.selectAccount(this.selectedAccount.id);
                        }
                    }).catch(error => {
                        if (error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            /**
             * Add a ban to a selected user
             */
            addBan: function () {
                axios.post('/api/administration/membership/'+this.selectedAccount.id+'/ban', {
                    reason: this.banReason,
                    permanent: this.banPermanent,
                    till: this.banTill,
                    homepage: this.banHomepage,
                    teamspeak: this.banTeamspeak,
                    forum: this.banForum,
                }).then(res => {
                    if(res.data != null) {
                        this.selectAccount(this.selectedAccount.id);
                    }
                }).catch(error => {
                    if (error.response.status == 422){
                        this.errors = error.response.data.errors;
                    }
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            /**
             * Remove a ban from a selected user
             * @param  {[type]} ban [description]
             * @return {[type]}     [description]
             */
            removeBan: function (ban) {
                axios.delete('/api/administration/membership/'+this.selectedAccount.id+'/ban/'+ban.id)
                    .then(res => {
                        if(res.data) {
                            this.selectAccount(this.selectedAccount.id);
                        }
                    }).catch(error => {
                        if (error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
		},
        computed: {
            validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        },
        mounted: function () {
            this.selectedAccount = null;
            this.selectAccount(this.$route.params.id);
        },
        activated: function () {
            this.selectedAccount = null;
            this.selectAccount(this.$route.params.id);
        }
	}
</script>
