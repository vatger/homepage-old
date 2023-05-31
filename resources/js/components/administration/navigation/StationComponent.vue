<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - Station</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <a v-on:click="selectedStation = null">Stationsverwaltung</a>
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
            <div class="card card-primary card-outline" v-if="createMode">
                <div class="card-header">
                    <h5 class="card-title">{{ newStation.ident }} - {{ newStation.name }}</h5>
                    <div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="createMode = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled alert alert-danger" v-if="errors">
                        <li v-for="(value, key) in validationErrors"  v-bind:key="key">{{ value }}</li>
                    </ul>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="newStation.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Identifier</label>
                        <input type="text" class="form-control" id="ident" v-model="newStation.ident">
                    </div>
                    <div class="form-group">
                        <label for="freq">Frequenz</label>
                        <input type="number" step=".001" class="form-control" id="freq" v-model="newStation.frequency">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="bookableSwitch" v-model="newStation.bookable">
                            <label class="custom-control-label" for="bookableSwitch">Buchbar</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="atisSwitch" v-model="newStation.atis">
                            <label class="custom-control-label" for="atisSwitch">ATIS</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="createStation">Speichern</button>
                    </div>
                </div>
            </div>
    		<div class="card card-primary card-outline" v-if="selectedStation != null">
                <div class="card-header">
                    <h5 class="card-title">{{ selectedStation.ident }} - {{ selectedStation.name }}</h5>
                    <div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="selectedStation = null">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled alert alert-danger" v-if="errors">
                        <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
                    </ul>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="selectedStation.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Identifier</label>
                        <input type="text" class="form-control" id="ident" v-model="selectedStation.ident">
                    </div>
                    <div class="form-group">
                        <label for="freq">Frequenz</label>
                        <input type="number" step=".001" class="form-control" id="freq" v-model="selectedStation.frequency">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="bookableSwitch" v-model="selectedStation.bookable">
                            <label class="custom-control-label" for="bookableSwitch">Buchbar</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="atisSwitch" v-model="selectedStation.atis">
                            <label class="custom-control-label" for="atisSwitch">ATIS</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="updateStation">Speichern</button>
                    </div>
                </div>
            </div>
            <div class="card card-primary card-outline">
    			<div class="card-header">
    				<h5 class="card-title">Stationen</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-hover table-bordered table-responsive-sm" id="stationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ident</th>
                                <th>Name</th>
                                <th>Frequenz</th>
                                <th>Buchbar</th>
                                <th>ATIS</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="station in stations" v-bind:key="station.id">
                                <td>{{ station.id }}</td>
                                <td>{{ station.ident }}</td>
                                <td>{{ station.name }}</td>
                                <td>{{ station.fixedFrequency }}</td>
                                <td>{{ station.bookable ? 'Ja' : 'Nein' }}</td>
                                <td>{{ station.atis ? 'Ja' : 'Nein' }}</td>
                                <td>
                                    <button class="btn btn-default btn-sm" v-on:click="selectedStation = station">Details</button>
                                    <button class="btn btn-danger btn-sm" v-on:click="removeStation(station)">Löschen</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7"><button class="btn btn-default btn-block" v-on:click="createMode = true">Neue Station Anlegen</button></td>
                            </tr>
                        </tfoot>
                    </table>
    			</div>
    		</div>
        </section>
    </div>
</template>

<script>
    
	export default{
        data() {
            return {
                accessDenied: false,
                stations: {},
                selectedStation: null,
                createMode: false,
                newStation: {
                    atis: false,
                    bookable: false,
                    frequency: 122.800,
                    name: 'Station Callsign',
                    ident: 'NETWORK_IDENT_STRING'
                },
                errors: false
            }
        },
        methods: {
            loadStations() {
                axios.get('/api/administration/navigation/station')
                    .then(res => {
                        this.stations = res.data;
                        this.$nextTick(() => {
                            $('#stationsTable').DataTable();
                        });
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            updateStation() {
                this.errors = false;
                axios.put('/api/administration/navigation/station/'+this.selectedStation.id, {
                    station: this.selectedStation
                }).then(res => {
                    this.selectedStation = res.data;
                    $('#stationsTable').DataTable().destroy();
                    this.createMode = false;
                    this.loadStations();
                }).catch(error => {
                    if(error.response.status == 422) {
                        this.errors = error.response.data;
                    }
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            createStation() {
                this.errors = false;
                axios.post('/api/administration/navigation/station', {
                    station: this.newStation
                }).then(res => {
                    this.selectedStation = res.data;
                    this.createMode = false;
                }).catch(error => {
                    if(error.response.status == 422) {
                        this.errors = error.response.data;
                    }
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            removeStation(station) {
                this.errors = false;
                axios.delete('/api/administration/navigation/station/'+station.id)
                .then(res => {
                    $('#stationsTable').DataTable().destroy();
                    this.selectedStation = null;
                    this.createMode = false;
                    this.loadStations();
                }).catch(error => {
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
        activated() {
            this.loadStations();
        }
	}
</script>