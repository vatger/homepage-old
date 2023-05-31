<template>
	<div class="card card-primary card-outline collapsed collapsed-card" v-if="dashboard">
		<div class="card-header">
			<h5 class="card-title">{{ trans('network.atclive.currentcoverage') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;" v-if="errors">
			<ul class="alert alert-danger">
                <li v-for="(value, key) in validationErrors" v-bind:key="key">@{{ value }}</li>
            </ul>
		</div>
		<div class="card-body" style="display: none;" v-else>
			<table class="table table-bordered table-hover table-responsive-sm" v-if="currentAtc.length > 0">
		        <thead>
		            <tr>
		                <th>{{ trans('network.atclive.station') }}</th>
		                <th>{{ trans('network.atclive.since') }}</th>
		            </tr>
		        </thead>
		        <tbody>
		            <tr v-for="atc in currentAtc" v-bind:key="atc.id">
		                <td v-if="atc.account != null">{{ atc.callsign }} <br/> {{ atc.account.firstname }} {{ atc.account.lastname }} </td>
						<td v-else>{{ atc.callsign }} <br/> {{ atc.account_id }}</td>
		                <td>{{ convertDate(atc.connected_at) }}</td>
		            </tr>
		        </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="2">All Times UTC <span class="float-right text-muted">{{ lastUpdate }}</span></td>
		            </tr>
		        </tfoot>
			</table>
			<div class="alert alert-info" v-else>
              <h5><i class="fas fa-info"></i> {{ trans('network.atclive.offline') }}</h5>
              {{ trans('network.atclive.alloffline') }}
            </div>
		</div>
	</div>
	<div id="atcCurrent" v-else>
		<div class="single-cat max-height-350">
			<h4 class="mb-20">{{ trans('network.atclive.currentcoverage') }}</h4>
			<p>{{ trans('network.atclive.coveragevacc') }}</p>
			<table class="table table-dark table-responsive-sm table-borderless" v-if="currentAtc.length > 0">
                <thead>
                    <tr>
                        <th>{{ trans('network.atclive.station') }}</th>
                        <th>{{ trans('network.atclive.since') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="atc in currentAtc" v-bind:key="atc.id">
		                <td>{{ atc.callsign }}</td>
		                <td>{{ convertDate(atc.connected_at) }}</td>
		            </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">All Times UTC <span class="float-right text-muted">{{ lastUpdate }}</span></td>
                    </tr>
                </tfoot>
            </table>
            <div class="alert alert-info" v-else>
            	<h5><i class="fa fa-info"></i> {{ trans('network.atclive.offline') }}</h5>
            	{{ trans('network.atclive.alloffline') }}
            </div>
		</div>
    </div>
</template>

<script>
	import moment from 'moment';

	export default {
		props: {
			dashboard: false
		},
		data() {
			return {
				currentAtc: {},
				errors: false,
				interval: null,
				lastUpdate: ''
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
			grabLiveAtc: function () {
				if(this.dashboard) {
					axios.get('/api/network/atc/local/1')
						.then(res => {
							this.currentAtc = res.data;
							this.lastUpdate = moment.utc().format('DD.MM.YYYY HH:mm');
						})
						.catch(error => {
							if (error.response.status == 422){
								this.errors = error.response.data.errors;
							}
						})
				} else {
					axios.get('/api/network/atc/local/')
						.then(res => {
							this.currentAtc = res.data;
							this.lastUpdate = moment.utc().format('DD.MM.YYYY HH:mm');
						})
						.catch(error => {
							if (error.response.status == 422){
								this.errors = error.response.data.errors;
							}
						})
				}
				
			},
			convertDate: function (date) {
                return moment.utc(date).format('HH:mm');
            }
		},
		activated() {
			this.grabLiveAtc();
			this.interval = setInterval(
                function() {
                    this.grabLiveAtc();
                }.bind(this),
                1 * 60 * 1000
            );
		},
		mounted() {
			if(!this.dashboard) {
				this.grabLiveAtc();
				this.interval = setInterval(
	                function() {
	                    this.grabLiveAtc();
	                }.bind(this),
	                1 * 60 * 1000
	            );
			}
		},
        beforeDestroy: function(){
            clearInterval(this.interval);
            this.interval = null;
        }
	}
</script>