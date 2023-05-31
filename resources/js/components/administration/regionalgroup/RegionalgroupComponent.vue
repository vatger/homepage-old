<template>
    <div class="content-wrapper">
        <section class="content-header" v-if="regionalgroup != null">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Regionalgruppen - {{ regionalgroup.name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">Administration</router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link to="/administration/regionalgroups">Regionalgruppen</router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ regionalgroup.name }}</li>
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
                            <div class="card-body">Du hast nicht die erforderlichen Rechte, um diese Quelle aufzurufen.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-if="regionalgroup != null && !accessDenied">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">{{ regionalgroup.name }}</h3>
                                <!-- <p class="text-muted text-center">Mitglieder: {{ regionalgroup.members.length }}</p>
                                <p class="text-muted text-center">Gäste: {{ regionalgroup.guests.length }}</p> -->
                            </div>
                        </div>

                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Neuen Leiter Auswählen</label>
                                    <select class="form-control" v-model="newChief">
                                        <option value="-1">Vacant</option>
                                        <option v-for="m in regionalgroup.accounts" v-bind:key="m.id" :value="m.id">{{ m.firstname }} {{ m.lastname }} ({{ m.id }})</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-block btn-default" v-on:click="changeChief">Neuen Leiter Einsetzen</button>
                                </div>
                                <div class="form-group">
                                    <label>Neuen Stellvertreter Auswählen</label>
                                    <select class="form-control" v-model="newDeputy">
                                        <option value="-1">Vacant</option>
                                        <option v-for="m in regionalgroup.accounts" v-bind:key="m.id" :value="m.id">{{ m.firstname }} {{ m.lastname }} ({{ m.id }})</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-block btn-default" v-on:click="changeDeputy">Neuen Stellvertreter Einsetzen</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <ul class="list-unstyled alert alert-danger" v-if="errors">
                            <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
                        </ul>
                        <div class="card card-primary card-outline">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#general" data-toggle="tab">Allgemeines</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#members" data-toggle="tab">Mitglieder</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#guests" data-toggle="tab">Gäste</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#mentors" data-toggle="tab">Mentoren</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#navlers" data-toggle="tab">Navigatoren</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#eventlers" data-toggle="tab">Eventteam</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#requests" data-toggle="tab">
                                            Anfragen
                                            <span class="badge badge-warning" v-if="regionalgroup.requests.length>0">{{regionalgroup.requests.length}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#templates" data-toggle="tab">Vorlagen</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#forumgroups" data-toggle="tab">Forengruppen</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="general">
                                        <div class="row">
                                            <div class="col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Leiter</span>
                                                        <span class="info-box-number" v-if="regionalgroup.chief != null">
                                                            {{ regionalgroup.chief.firstname }} {{ regionalgroup.chief.lastname }} ({{ regionalgroup.chief.id }})
                                                        </span>
                                                        <span class="info-box-number" v-else>Vakant</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Stellvertreter</span>
                                                        <span class="info-box-number" v-if="regionalgroup.deputy != null">
                                                            {{ regionalgroup.deputy.firstname }} {{regionalgroup.deputy.lastname}} ({{ regionalgroup.deputy.id }})
                                                        </span>
                                                        <span class="info-box-number" v-else>Vakant</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Mitglieder Gesamt</span>
                                                        <span class="info-box-number">{{ regionalgroup.membersCount + regionalgroup.guestsCount }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Vollmitglieder</span>
                                                        <span class="info-box-number">{{ regionalgroup.membersCount }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Gastmitglieder</span>
                                                        <span class="info-box-number">{{ regionalgroup.guestsCount }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Mentoren</span>
                                                        <span class="info-box-number">{{ regionalgroup.mentors.length }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Navigatoren</span>
                                                        <span class="info-box-number">{{ regionalgroup.navigators.length }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon">
                                                        <i class="far fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Eventteam</span>
                                                        <span class="info-box-number">{{ regionalgroup.eventler.length }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="members">
                                        <table class="table table-borderless table-responsive-sm" id="membersTable">
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Name</th>
                                                    <th>Rating</th>
                                                    <th>Zuordnung</th>
                                                    <th>Beitritt</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(member, index) in fullmembers" v-bind:key="index">
                                                    <td>{{ member.id }} <button class="btn" v-on:click="openAccountDetails(member.id)"><i class="fas fa-info-circle"></i></button></td>
                                                    <td>{{ member.firstname }} {{ member.lastname }}</td>
                                                    <td>{{ member.data.controllerRating.short }} / {{ member.data.pilotRating }}</td>
                                                    <td>{{ member.data.region_code }}/{{ member.data.division_code }}/{{ member.data.subdivision_code }}</td>
                                                    <td>{{ member.pivot.created_at | moment("utc", "YYYY-MM-DD") }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" v-on:click="removeMember(member)">Rauswerfen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="guests">
                                        <table class="table table-borderless table-responsive-sm" id="guestsTable">
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Name</th>
                                                    <th>Rating</th>
                                                    <th>Zuordnung</th>
                                                    <th>Beitritt</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(guest, index) in guests" v-bind:key="index">
                                                    <td>{{ guest.id }} <button class="btn" v-on:click="openAccountDetails(guest.id)"><i class="fas fa-info-circle"></i></button></td>
                                                    <td>{{ guest.firstname }} {{ guest.lastname }}</td>
                                                    <td>{{ guest.data.controllerRating.short }} / {{ guest.data.pilotRating }}</td>
                                                    <td>{{ guest.data.region_code }}/{{ guest.data.division_code }}/{{ guest.data.subdivision_code }}</td>
                                                    <td>{{guest.pivot.created_at | moment("utc", "YYYY-MM-DD")}}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" v-on:click="removeMember(guest)">Rauswerfen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="mentors">
                                        <table class="table table-borderless table-responsive-sm" id="mentorsTable">
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(mentor, index) in regionalgroup.mentors" v-bind:key="index">
                                                    <td>{{ mentor.id }}</td>
                                                    <td>{{ mentor.firstname }} {{ mentor.lastname }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" v-on:click="removeMentor(mentor)">Rauswerfen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>Neuen Mentor Ernennen:</td>
                                                    <td>
                                                        <select class="form-control form-control-sm" v-model="newMentor">
                                                            <option v-for="m in regionalgroup.accounts" :value="m.id" v-bind:key="m.id">{{ m.firstname }} {{ m.lastname }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-default" v-on:click="assignMentor">Ernennen!</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="navlers">
                                        <table class="table table-borderless table-responsive-sm" id="navlerTable">
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(navler, index) in regionalgroup.navigators" v-bind:key="index">
                                                    <td>{{ navler.id }}</td>
                                                    <td>{{ navler.firstname }} {{ navler.lastname }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" v-on:click="removeNavigator(navler)">Rauswerfen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>Neuen Navigator Ernennen:</td>
                                                    <td>
                                                        <select class="form-control form-control-sm" v-model="newNavigator">
                                                            <option v-for="n in regionalgroup.accounts" :value="n.id" v-bind:key="n.id">{{ n.firstname }} {{ n.lastname }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-default" v-on:click="assignNavigator">Ernennen!</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="eventlers">
                                        <table class="table table-borderless table-responsive-sm" id="eventlerTable">
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(eventler, index) in regionalgroup.eventler" v-bind:key="index">
                                                    <td>{{ eventler.id }}</td>
                                                    <td>{{ eventler.firstname }} {{ eventler.lastname }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" v-on:click="removeEventler(eventler)">Rauswerfen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>Neues Eventteammitglied Ernennen:</td>
                                                    <td>
                                                        <select class="form-control form-control-sm" v-model="newEventler">
                                                            <option v-for="e in regionalgroup.accounts" :value="e.id" v-bind:key="e.id">{{ e.firstname }} {{ e.lastname }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-default" v-on:click="assignEventler">Ernennen!</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div id="requests" class="tab-pane">
                                        <regionalgroup-tab-requests v-bind:regionalgroup="regionalgroup"
                                                                    v-on:update:regionalgroup="regionalgroup = $event"
                                                                    v-on:update:errors="errors = $event"
                                                                    v-on:update:accessDenied="accessDenied = $event">
                                        </regionalgroup-tab-requests>
                                    </div>
                                    <div class="tab-pane" id="templates">
                                        <regionalgroup-tab-templates v-bind:regionalgroup="regionalgroup"
                                                                     v-on:update:regionalgroup="regionalgroup = $event"
                                                                     v-on:update:errors="errors = $event"
                                                                     v-on:update:accessDenied="accessDenied = $event">
                                        </regionalgroup-tab-templates>
                                    </div>
                                    <div class="tab-pane" id="forumgroups" v-if="setForumGroups">
                                        <p>Hier werden die Forengruppen für die Regionalgruppe festgelegt.</p>
                                        <div class="form-group">
                                            <label>Staff Gruppe</label>
                                            <select class="form-control" v-model="staffGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Voting Gruppe</label>
                                            <select class="form-control" v-model="votingGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Mentoren Gruppe</label>
                                            <select class="form-control" v-model="mentorGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Navigatoren Gruppe</label>
                                            <select class="form-control" v-model="navlerGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Event Gruppe</label>
                                            <select class="form-control" v-model="eventlerGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Mitglieder Gruppe</label>
                                            <select class="form-control" v-model="membersGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Gäste Gruppe</label>
                                            <select class="form-control" v-model="guestsGroup">
                                                <option v-for="g in availableForumGroups" :value="g.id" v-bind:key="g.id">{{ g.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-block btn-default" v-on:click="updateForumGroups">Forengruppen festlegen</button>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="forumgroups" v-else>
                                        <p class="text-danger">Du hast nicht die erforderlichen Rechte, diese
                                            Einstellungen zu verändern. Dazu sind Systemadministrator-Rechte
                                            erforderlich. Sollte hier etwas nicht stimmen, kontaktiere bitte einen der
                                            Verantworlichen über das Ticketsystem der vACC Germany.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import RegionalgroupTabTemplates from './RegionalgroupTabTemplates.vue'
import RegionalgroupTabRequests from "./RegionalgroupTabRequests.vue";

export default {
    components: {RegionalgroupTabRequests, RegionalgroupTabTemplates},
    data() {
        return {
            accessDenied: false,
            errors: false,
            regionalgroup: null,
            selectedRequest: null,
            newMentor: null,
            newNavigator: null,
            newEventler: null,
            newChief: null,
            newDeputy: null,
            setForumGroups: false,
            availableForumGroups: {},
            staffGroup: null,
            votingGroup: null,
            mentorGroup: null,
            navlerGroup: null,
            eventlerGroup: null,
            membersGroup: null,
            guestsGroup: null,
            fullmembers: [],
            guests: [],
        };
    },
    methods: {
        loadRegionalgroup() {
            this.regionalgroup = null;

            axios
                .get(
                    "/api/administration/regionalgroups/" +
                    this.$route.params.id
                )
                .then((res) => {
                    $("#membersTable").DataTable().destroy();
                    $("#guestsTable").DataTable().destroy();
                    $("#mentorsTable").DataTable().destroy();
                    $("#navlerTable").DataTable().destroy();
                    $("#eventlerTable").DataTable().destroy();
                    $("#requestsTable").DataTable().destroy();
                    this.regionalgroup = res.data;
                    this.staffGroup = this.regionalgroup.staff_group_id;
                    this.votingGroup = this.regionalgroup.voting_group_id;
                    this.mentorGroup = this.regionalgroup.mentor_group_id;
                    this.navlerGroup = this.regionalgroup.navler_group_id;
                    this.eventlerGroup = this.regionalgroup.eventler_group_id;
                    this.membersGroup = this.regionalgroup.member_group_id;
                    this.guestsGroup = this.regionalgroup.guest_group_id;

                    this.fullmembers = this.regionalgroup.accounts.filter(account => account.pivot.guest == 0);
                    this.guests = this.regionalgroup.accounts.filter(account => account.pivot.guest == 1);

                    this.$nextTick(() => {
                        $("#membersTable").DataTable();
                        $("#guestsTable").DataTable();
                        $("#mentorsTable").DataTable();
                        $("#navlerTable").DataTable();
                        $("#eventlerTable").DataTable();
                        $("#requestsTable").DataTable();
                    });
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
        },

        removeMember(m) {
            axios
                .delete(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id +
                    "/accounts/" +
                    m.id
                )
                .then((res) => {
                    this.loadRegionalgroup();
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
        },
        assignMentor() {
            if (this.newMentor != null) {
                axios
                    .post(
                        "/api/administration/regionalgroups/" +
                        this.regionalgroup.id +
                        "/mentors",
                        {
                            mentor: this.newMentor,
                        }
                    )
                    .then((res) => {
                        this.loadRegionalgroup();
                    })
                    .catch((error) => {
                        if (error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
        },
        removeMentor(m) {
            axios
                .delete(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id +
                    "/mentors/" +
                    m.id
                )
                .then((res) => {
                    this.loadRegionalgroup();
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
        },
        assignNavigator() {
            if (this.newNavigator != null) {
                axios
                    .post(
                        "/api/administration/regionalgroups/" +
                        this.regionalgroup.id +
                        "/navigators",
                        {
                            navigator: this.newNavigator,
                        }
                    )
                    .then((res) => {
                        this.loadRegionalgroup();
                    })
                    .catch((error) => {
                        if (error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
        },
        removeNavigator(n) {
            axios
                .delete(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id +
                    "/navigators/" +
                    n.id
                )
                .then((res) => {
                    this.loadRegionalgroup();
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
        },
        assignEventler() {
            if (this.newEventler != null) {
                axios
                    .post(
                        "/api/administration/regionalgroups/" +
                        this.regionalgroup.id +
                        "/eventler",
                        {
                            eventler: this.newEventler,
                        }
                    )
                    .then((res) => {
                        this.loadRegionalgroup();
                    })
                    .catch((error) => {
                        if (error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
        },
        removeEventler(e) {
            axios
                .delete(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id +
                    "/eventler/" +
                    e.id
                )
                .then((res) => {
                    this.loadRegionalgroup();
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
        },
        changeChief() {
            if (this.newChief != null) {
                axios
                    .post(
                        "/api/administration/regionalgroups/" +
                        this.regionalgroup.id +
                        "/chief",
                        {
                            newChief: this.newChief,
                        }
                    )
                    .then((res) => {
                        this.loadRegionalgroup();
                    })
                    .catch((error) => {
                        if (error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
        },
        changeDeputy() {
            if (this.newDeputy != null) {
                axios
                    .post(
                        "/api/administration/regionalgroups/" +
                        this.regionalgroup.id +
                        "/deputy",
                        {
                            newDeputy: this.newDeputy,
                        }
                    )
                    .then((res) => {
                        this.loadRegionalgroup();
                    })
                    .catch((error) => {
                        if (error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            }
        },
        updateForumGroups() {
            axios
                .put(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id,
                    {
                        staff_group_id: this.staffGroup,
                        voting_group_id: this.votingGroup,
                        mentor_group_id: this.mentorGroup,
                        navler_group_id: this.navlerGroup,
                        eventler_group_id: this.eventlerGroup,
                        member_group_id: this.membersGroup,
                        guest_group_id: this.guestsGroup,
                    }
                )
                .then((res) => {
                    this.regionalgroup = res.data;
                });
        },
        loadAvailableForumGroups() {
            axios.get("/api/administration/forum/groups").then((res) => {
                this.setForumGroups = true;
                this.availableForumGroups = res.data;
            }).catch(error => {
                if (error.response.status == 403) {
                    this.setForumGroups = false;
                }
            });
        },
        openAccountDetails (id) {
            window.open('/administration/membership/' + id, 'Userinfo: ' + id, 'menubar=no,resizable=yes,scrollbars=yes')
        },
    },
    computed: {
        validationErrors() {
            let errors = Object.values(this.errors);
            errors = errors.flat();
            return errors;
        },
    },
    watch: {
        $route(to, from) {
            if (this.$route.params.id) {
                this.loadRegionalgroup();
            }
        },
    },
    activated() {
        this.loadAvailableForumGroups();
        this.loadRegionalgroup();
    },
};
</script>
