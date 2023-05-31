<template>
	<div class="col-12">
		<div class="card card-primary card-outline" v-if="generator">
			<div class="card-header">
				<h3 class="card-title">EuroScope Scenario Generator</h3>
				<div class="card-tools">
                    <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="generator = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
			</div>
			<div class="card-body" v-if="result == null">
				<ul class="list-unstyled alert alert-danger" v-if="errors">
	                <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
	            </ul>
				<div class="form-group">
					<label>Aerodrome ICAO</label>
					<input class="form-control" type="text" placeholder="EDDH" v-model="icao" />
				</div>
				<div class="form-group">
					<label>Holdings: <small>One holding per line. Direction: -1 for left, 1 for right</small></label>
					<textarea class="form-control" placeholder="FIX:INBD CRS:DIR" v-model="holdings"></textarea>
				</div>
				<div class="form-group">
					<label>Traffic Scale</label>
					<input class="form-control" type="number" step="1" v-model="trafficScale" />
				</div>
				<div class="form-group">
					<label>Departure / Arrival Scale</label>
					<input class="form-control" type="number" step="1" v-model="departureArrivalScale" />
				</div>
				<div class="form-group">
					<label>Min Squawk</label>
					<input class="form-control" type="number" step="1" v-model="minSquawk" />
				</div>
				<div class="form-group">
					<label>Max Squawk</label>
					<input class="form-control" type="number" step="1" v-model="maxSquawk" />
				</div>
				<div class="form-group">
					<label>Initial Pseudopilot <small>ATC Station Logon</small></label>
					<input class="form-control" type="text" placeholder="EDDH_TWR" v-model="initialPseudo" />
				</div>
				<div class="form-group">
					<button class="btn btn-block btn-default" v-on:click="generate">Generate Scenario</button>
				</div>
			</div>
			<div class="card-body" v-else>
				<textarea readonly v-model="result" class="form-control" rows="50"></textarea>
				<button v-on:click="result = null" class="btn btn-block btn-default">Reset</button>
			</div>
		</div>
		<div class="card card-widget widget-user" v-else>
		<!-- Add the bg color to the header using any of the bg-* classes -->
			<div class="widget-user-header text-white" style="background-image: url('/images/radar.png'); background-position: center center;background-size: cover; background-repeat: no-repeat;">
				<h3 class="widget-user-username text-right">EuroScope Scenario Generator</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<p>Erstelle EuroScope Scenarios basierend auf aktueller Netzwerkaktivität.</p>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-12">
						<div class="description-block">
							<span class="description-text">
								<button class="btn btn-default" v-on:click="generator = true">Scenario Erstellen</button>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				generator: false,
				icao: null,
				initialPseudo: '',
				holdings: '',
				trafficScale: 50,
				departureArrivalScale: 50,
				minSquawk: 1,
				maxSquawk: 7777,
				result: null,
				errors: false
			}
		},
		computed: {
        	validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        },
		methods: {
			generate() {
				this.result = null;
				axios.post('/api/administration/atd/euroscope/scenario', {
					icao: this.icao,
					holdings: this.holdings,
					trafficScale: this.trafficScale,
					depArrScale: this.departureArrivalScale,
					minSquawk: this.minSquawk,
					maxSquawk: this.maxSquawk,
					initialPseudo: this.initialPseudo
				}).then(res => {
					this.result = res.data;
				}).catch(error => {
					if(error.response.status == 422) {
						this.errors = error.response.data.errors;
					}
				})
			}
		}
	}
</script>