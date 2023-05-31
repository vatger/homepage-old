<template>
	<div class="card card-primary card-outline collapsed collapsed-card">
		<div class="card-header">
			<h5 class="card-title">{{ trans('teamspeak.accounts') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
			<div class="callout callout-info" v-html="trans('teamspeak.registationprocess')"></div>
			<table class="table table-responsive-sm table-bordered table-hover">
				<thead>
					<tr>
						<th>TS-ID</th>
						<th>{{ trans('teamspeak.lastip') }}</th>
						<!--<th>{{ trans('teamspeak.lastos') }}</th>-->
						<th>{{ trans('teamspeak.lastused') }}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="ts in teamspeaks" v-bind:key="ts.id">
						<td>{{ ts.uid }}</td>
						<td>{{ ts.last_ip }}</td>
						<!--<td>{{ ts.last_os }}</td>-->
						<td>{{ ts.last_login | moment('DD.MM.YYYY HH:mm') }}z</td>
						<td>
							<button class="btn btn-danger" v-on:click="removeRegistration(ts)">{{ trans('teamspeak.removeaccount') }}</button>
						</td>
					</tr>
				</tbody>
			</table>
			<!--<div class="form-group my-3" v-if="!automaticRegistrationInProgress">
				<button class="btn btn-default btn-block" v-on:click="startAutomaticRegistration">{{ trans('teamspeak.auto.start') }}</button>
			</div>
			<div class="callout callout-info my-3" v-else>
				<div v-html="trans('teamspeak.auto.text')"></div>
				<p>
					<button class="btn btn-default btn-block" v-on:click="completeAutomaticRegistration">{{ trans('teamspeak.auto.finish') }}</button>
				</p>
			</div>-->
			<div class="callout callout-warning mt-3" v-html="trans('teamspeak.manual.text')"></div>
			<div class="form-group">
				<label>TS-ID / Identität:</label>
				<input class="form-control" v-model="manualTSId" type="text" />
			</div>
			<div class="form-group" v-if="manualTSId && manualTSId.length > 0">
				<button @click="registerTeamSpeakManual" class="btn btn-block btn-info">{{ trans('teamspeak.manual.finish') }}</button>
			</div>
            <div v-if="this.errors != null">
                <ul class="list-unstyled alert alert-danger"><li>{{this.errors}}</li></ul>
            </div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
                teamspeaks: {},
                errors: null,
				automaticRegistrationInProgress: false,
				teamSpeakRegistrationLink: "",
				manualTSId: "",
			};
		},
		methods: {
			loadTeamspeaks() {
				axios.get("/api/membership/teamspeak").then((res) => {
					this.teamspeaks = res.data;
				});
			},
			startAutomaticRegistration() {
				axios.post("/api/membership/teamspeak").then((res) => {
					if (res.data.success) {
						this.automaticRegistrationInProgress = true;
						this.teamSpeakRegistrationLink = res.data.tslink;
					}
				});
			},
			completeAutomaticRegistration() {
				if (this.teamSpeakRegistrationLink.length != "") {
					window.open(this.teamSpeakRegistrationLink);
					this.automaticRegistrationInProgress = false;
					this.teamSpeakRegistrationLink = "";
					this.loadTeamspeaks();
				}
			},
			registerTeamSpeakManual: function () {
                this.errors = null;
				axios
					.put("/api/membership/teamspeak", { tsId: this.manualTSId })
					.then((res) => {
                        if(res.data.success == false){
                            this.errors = res.data.message;
                        }
						this.loadTeamspeaks();
					});
			},
			removeRegistration(tsreg) {
				axios
					.delete("/api/membership/teamspeak/" + tsreg.id)
					.then((res) => {
						this.loadTeamspeaks();
					});
			},
		},
		activated() {
			this.loadTeamspeaks();
		},
	};
</script>
