<template>
	<div class="col-12">
		<div class="card card-primary card-outline" v-if="soloAdministration">
			<div class="card-header">
                <h5 class="card-title">ATD Solofreigabenverwaltung</h5>
                <div class="card-tools">
                    <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="closeSoloAdministration">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
            	<table class="table table-bordered table-hover table-responsive-sm" id="solosTable">
            		<thead>
            			<tr>
            				<th>CID</th>
            				<th>Trainee</th>
            				<th>Station</th>
            				<th>Option</th>
            				<th>Verlängerungen</th>
            				<th>Läuft Ab Am</th>
            				<th>Genehmigt</th>
            				<th></th>
            			</tr>
            		</thead>
            		<tbody>
            			<tr v-for="(solo, index) in soloendorsements.solos" v-bind:key="index" :class="solo.approved ? 'table-info' : 'table-warning'">
	                        <td>{{ solo.candidate.id }}</td>
	                        <td>{{ solo.candidate.firstname + ' ' + solo.candidate.lastname }}</td>
	                        <td>{{ solo.station.name }}</td>
	                        <td>{{ solo.phase.title }}</td>
	                        <td>{{ solo.extensions }}</td>
	                        <td>{{ solo.ends_at | moment("utc", "YYYY-MM-DD") }}</td>
	                        <td>{{ solo.approved ? 'Yes' : 'No' }}</td>
	                        <td>
	                            <button class="btn btn-default" @click="openSoloDetails(solo)">Details</button>
	                            <button class="btn btn-default" v-if="solo.approved" @click="extendSolo(solo)">Verlängern</button>
	                            <button class="btn btn-danger" v-if="solo.approved" @click="revokeSolo(solo)">Entziehen</button>
	                            <button class="btn btn-success" v-else @click="approveSolo(solo)">Genehmigen</button>
	                            <button class="btn btn-danger" v-if="!solo.approved" @click="deleteSolo(solo)">Löschen</button>
	                        </td>
	                    </tr>
            		</tbody>
            	</table>
            	<div class="modal fade" id="soloDetailsModal" aria-modal="true">
	                <div class="modal-dialog modal-xl">
	                    <div class="modal-content">
	                        <div class="modal-header">
	                            <h5 class="modal-title">Details</h5>
	                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                                <span aria-hidden="true">×</span>
	                            </button>
	                        </div>
	                        <div class="modal-body" v-if="selectedSolo != null">
	                            <p>Solo Endorsement for {{ selectedSolo.candidate.firstname + ' ' + selectedSolo.candidate.lastname }}</p>
	                            <dl>
	                                <dt>Phase</dt>
	                                <dd>{{ selectedSolo.phase.title }}</dd>
	                                <dt>Station</dt>
	                                <dd>{{ selectedSolo.station.name }} ({{ selectedSolo.station.ident }})</dd>
	                                <dt>Begin</dt>
	                                <dd>{{ selectedSolo.begins_at | moment("utc", "DD.MM.YYYY") }}</dd>
	                                <dt>End</dt>
	                                <dd>{{ selectedSolo.ends_at | moment("utc", "DD.MM.YYYY") }}</dd>
	                                <dt>Extensions</dt>
	                                <dd>{{ selectedSolo.extensions }}</dd>
	                            </dl>
	                            <p>Es besteht die Möglichkeit die Solofreigabe zu verändern. Wähle entweder eine neue Station aus, verschiebe die Solophase, oder ändere die Solophase.</p>
	                            <ul>
	                                <li>Stationswechsel ändert nur die Freigabestation</li>
	                                <li>Verschiebung der Solophase addiert die Zeit der neuen Phase auf das Ende der bestehenden Phase.</li>
	                                <li>Änderung der Solophase kalkuliert die neue Endzeit vom Begin der bestehenden Phase.</li>
	                            </ul>
	                            <div class="form-group">
	                                <label>Neue Station</label>
                                    <select class="form-control" v-model="newStation">
                                        <option :value="s.id" v-for="(s, index) in soloendorsements.stations" v-bind:key="index">{{ s.ident + ' (' + s.name  + ')' }}</option>
                                    </select>
	                            </div>
	                            <div class="form-group">
		                            <button class="btn btn-default" @click="switchStation">
		                                Weise die neue Station zu
		                            </button>
	                            </div>
	                            <div class="form-group">
	                                <label>Phase Verschieben</label>
                                    <select class="form-control" v-model="newPhaseFwd">
                                        <option :value="p.id" v-for="(p, index) in soloendorsements.phases" v-bind:key="index">{{ p.title }}</option>
                                    </select>
	                            </div>
	                            <div class="form-group">
		                            <button class="btn btn-default" @click="forwardPhase">
		                                Freigabe in neue Phase verschieben
		                            </button>
	                            </div>
	                            <div class="form-group">
	                                <label>Phase Wechseln</label>
                                    <select class="form-control" v-model="newPhaseSwitch">
                                        <option :value="p.id" v-for="(p, index) in soloendorsements.phases" v-bind:key="index">{{ p.title }}</option>
                                    </select>
	                            </div>
	                            <div class="form-group">
		                            <button class="btn btn-default" @click="switchPhase">
		                                Freigabe in neue Phase wechseln
		                            </button>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
            </div>
            <div class="card-footer">
            	<button class="btn btn-default btn-block" v-on:click="openNewSoloModal">Neue Solofreigabe Eintragen</button>
            	<div class="modal fade" id="newSoloModal" aria-modal="true">
	                <div class="modal-dialog modal-lg">
	                    <div class="modal-content">
	                        <div class="modal-header">
	                            <h5 class="modal-title">Neue Solofreigabe Eintragen</h5>
	                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                                <span aria-hidden="true">×</span>
	                            </button>
	                        </div>
	                        <div class="modal-body">
	                            <div class="form-group">
	                            	<label>CID</label>
	                            	<input class="form-control" v-model="newSolo.cid">
	                            </div>
	                            <div class="form-group">
                                    <label class="">Beginn</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <date-picker v-model="newSolo.begin" lang="de" format="DD.MM.YYYY" date-format="DD.MM.YYYY" type="date" value-type="format" :first-day-of-week="1" :not-before="renderTimeNow()"></date-picker>
                                    </div>
                                </div>
	                            <div class="form-group">
	                                <label>Station</label>
                                    <select class="form-control" v-model="newSolo.station">
                                        <option :value="s.id" v-for="(s, index) in soloendorsements.stations" v-bind:key="index">{{ s.ident + ' (' + s.name + ')' }}</option>
                                    </select>
	                            </div>
	                            <div class="form-group">
	                                <label>Phase</label>
                                    <select class="form-control" v-model="newSolo.phase">
                                        <option :value="p.id" v-for="(p, index) in soloendorsements.phases" v-bind:key="index">{{ p.title }}</option>
                                    </select>
	                            </div>
	                            <div class="form-group">
	                            	<button class="btn btn-primary btn-block" v-on:click="createNewSolo">Solofreigabe Eintragen</button>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
            </div>
		</div>
		<div class="card card-widget widget-user" v-else>
		<!-- Add the bg color to the header using any of the bg-* classes -->
			<div class="widget-user-header text-white" style="background-image: url('/images/radar.png'); background-position: center center;background-size: cover; background-repeat: no-repeat;">
				<h3 class="widget-user-username text-right">Solofreigaben</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<p>Verwalte die Solofreigaben der ATD Trainees.</p>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-12">
						<div class="description-block">
							<span class="description-text">
								<button class="btn btn-default" v-on:click="openSoloAdministration">Solofreigaben Verwalten</button>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment'
	import DatePicker from 'vue2-datepicker'

	export default {
		components: {
			DatePicker,
		},
		data() {
			return {
				soloAdministration: false,
				soloendorsements: {},
				selectedSolo: null,
                newStation: 0,
                newPhaseFwd: 0,
                newPhaseSwitch: 0,
                newSolo: {
                	cid: null,
                	begin: null,
                	station: null,
                	phase: null
                }
			}
		},
		methods: {
			openSoloAdministration() {
				// Load the solo endorsements
				axios.get('/api/administration/atd/solos')
					.then(res => {
						this.soloendorsements = res.data;
						this.$nextTick(() => {
							$('#solosTable').DataTable();
						})
					});
				// Set the display
				this.soloAdministration = true;
			},
			closeSoloAdministration() {
				this.soloAdministration = false;
				this.soloendorsements = {};
				$('#solosTable').DataTable().destroy();
			},
			reloadSoloAdministration() {
				this.soloendorsements = {};
				$('#solosTable').DataTable().destroy();
				// Load the solo endorsements
				axios.get('/api/administration/atd/solos')
					.then(res => {
						this.soloendorsements = res.data;
						this.$nextTick(() => {
							$('#solosTable').DataTable();
						});
					});
			},
			approveSolo: function (solo) {
                axios.put('/api/administration/atd/solos', {
                    solo: solo.id,
                    approved: true
                }).then(res => {
                    this.reloadSoloAdministration();
                });
            },
            revokeSolo: function (solo) {
                axios.put('/api/administration/atd/solos', {
                    solo: solo.id,
                    approved: false
                }).then(res => {
                    this.reloadSoloAdministration();
                });
            },
            deleteSolo: function (solo) {
                axios.delete('/api/administration/atd/solos', {
                    data: {
                        solo: solo.id
                    }
                }).then(res => {
                    this.reloadSoloAdministration();
                });
            },
            extendSolo: function (solo) {
                let nextExtension = solo.extensions + 1;
                axios.put('/api/administration/atd/solos/'+solo.id, {
                    extension: nextExtension
                }).then(res => {
                    this.reloadSoloAdministration();
                });
            },
            openSoloDetails: function (solo) {
                this.selectedSolo = solo;
                $('#soloDetailsModal').modal('show');
            },
            switchStation: function () {
                axios.put('/api/administration/atd/solos/'+this.selectedSolo.id+'/station', {
                    station: this.newStation
                }).then(res => {
                    $('#soloDetailsModal').modal('hide');
                    this.reloadSoloAdministration();
                });
            },
            switchPhase: function () {
                axios.put('/api/administration/atd/solos/'+this.selectedSolo.id+'/switch', {
                    phase: this.newPhaseSwitch
                }).then(res => {
                    $('#soloDetailsModal').modal('hide');
                    this.reloadSoloAdministration();
                });
            },
            forwardPhase: function () {
                axios.put('/api/administration/atd/solos/'+this.selectedSolo.id+'/forward', {
                    phase: this.newPhaseFwd
                }).then(res => {
                    $('#soloDetailsModal').modal('hide');
                    this.reloadSoloAdministration();
                });
            },
            openNewSoloModal() {
            	$('#newSoloModal').modal('show');
            },
            createNewSolo() {
            	axios.post('/api/administration/atd/solos', {
            		newSolo: this.newSolo
            	}).then(res => {
            		if(res.data.success) {
            			$('#newSoloModal').modal('hide');
            			this.reloadSoloAdministration();
            			this.newSolo.cid = null;
            			this.newSolo.begin = null;
            			this.newSolo.phase = null;
            			this.newSolo.station = null;
            		}
            	});
            },
            renderTimeNow: function () {
                // Calculate UTC by subtracting the OFFSET
                return moment().subtract(moment().utcOffset(), 'minutes').startOf('hour');
            }
		}
	}
</script>
