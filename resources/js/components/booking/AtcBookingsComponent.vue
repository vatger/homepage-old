<template>
	<div class="card card-primary card-outline collapsed" v-if="dashboard">
		<div class="card-header">
			<h5 class="card-title">{{ trans('booking.bookings') }}</h5>
			<div class="card-tools" v-if="range">
				<div class="form-inline">
                    <label class="mr-2">{{ trans('booking.timeframe') }}</label>
                    <div class="input-group">
                        <date-picker v-model="from" lang="de" format="DD.MM.YYYY" date-format="DD.MM.YYYY" type="date" value-type="format" :first-day-of-week="1" :clearable="false" :not-before="renderTimeNow()" :input-class="'form-control form-control-sm'"></date-picker>
                    </div>
                    <div class="input-group">
                        <date-picker v-model="till" lang="de" format="DD.MM.YYYY" date-format="DD.MM.YYYY" type="date" value-type="format" :first-day-of-week="1" :clearable="false" :not-before="renderTimeNow()" :input-class="'form-control form-control-sm'"></date-picker>
                    </div>
                    <button v-on:click="daterange" class="btn btn-tool">{{ trans('booking.filter') }}</button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
			</div>
			<div class="card-tools" v-else>
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-minus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: block;">
			<table class="table table-borderless table-hover table-responsive-sm" v-if="atcBookings.length > 0">
				<thead>
					<tr>
						<th>{{ trans('booking.station') }}</th>
						<th>{{ trans('booking.frequency') }}</th>
						<th>{{ trans('booking.timeframe') }}</th>
					</tr>
				</thead>
				<tbody>
					<template v-for="atc in atcBookings">
						<tr v-if="atc.divider" class="divider" v-bind:key="'ab'+atc.id">
							<td colspan="3">{{ atc.starts_at | moment("utc", "DD.MM.YYYY")}}</td>
						</tr>
						<tr v-bind:key="atc.id">
							<td>{{ atc.station.ident }} <br/> {{ atc.controller.firstname }} {{ atc.controller.lastname }} ({{ atc.controller.id }})</td>
							<td>{{ atc.station.fixedFrequency }}</td>
							<td>{{ atc.starts_at | moment("utc", "HH:mm")}} - {{ atc.ends_at | moment("utc", "HH:mm") }}</td>
						</tr>
					</template>
				</tbody>
				<tfoot>
                    <tr>
                        <td colspan="3">All Times UTC</td>
                    </tr>
                </tfoot>
			</table>
			<div class="alert alert-info" v-else>
              <h5><i class="icon fas fa-info"></i> {{ trans('booking.nobookings') }}</h5>
              {{ trans('booking.nobookings_actions') }}
            </div>
		</div>
	</div>
    <div id="atcBooking" v-else>
    	<div class="single-cat max-height-350">
			<h4 class="mb-20">{{ trans('booking.bookings') }}</h4>
			<p>{{ trans('booking.next48hours') }}</p>
			<table class="table table-dark table-responsive-sm table-borderless table-max-height-350">
                <thead>
                    <tr>
                        <th>{{ trans('booking.station') }}</th>
                        <th width="35%">{{ trans('booking.begin') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="atc in atcBookings">
						<tr v-if="atc.divider" class="divider" v-bind:key="'ab'+atc.id">
							<td colspan="2">{{ atc.starts_at | moment("utc", "DD.MM.YYYY")}}</td>
						</tr>
						<tr v-bind:key="atc.id">
							<td>{{ atc.station.ident }}</td>
							<td>{{ atc.starts_at | moment("utc", "HH:mm")}} - {{ atc.ends_at | moment("utc", "HH:mm") }}</td>
						</tr>
					</template>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">All Times UTC</td>
                    </tr>
                </tfoot>
            </table>
		</div>
    </div>
</template>

<script>
	import moment from 'moment'
	import DatePicker from 'vue2-datepicker'

	export default {
		components: {
			DatePicker
		},
		props: {
			dashboard: false,
			range: false
		},
		data() {
			return {
				atcBookings: {},
				from: null,
				till: null,
				interval: null
			}
		},
		methods: {
			loadBookings: function () {
				axios.get('/api/booking/atc')
					.then(res => {
						this.atcBookings = res.data;
						this.addDividers();
					}).catch(error => {
						console.log(error.response.status);
					});
			},
			daterange() {
				if(this.till == null) {
					axios.get('/api/booking/atc/daterange/'+this.from).then(res => {
						this.atcBookings = res.data;
						this.addDividers();
					}).catch(error => {
						console.log(error.response.status);
					});
				} else {
					axios.get('/api/booking/atc/daterange/'+this.from+'/'+this.till).then(res => {
						this.atcBookings = res.data;
						this.addDividers();
					}).catch(error => {
						console.log(error.response.status);
					});
				}
			},
			update() {
				this.from = null;
				this.till = null;
				this.loadBookings();
			},
			addDividers() {
				if(this.atcBookings != null && this.atcBookings.length > 0) {
					this.atcBookings.sort((a,b) => {
						if(moment(a.starts_at).utc().format('YYYY.MM.DD') < moment(b.starts_at).utc().format('YYYY.MM.DD'))
							return -1;
						if(moment(a.starts_at).utc().format('YYYY.MM.DD') > moment(b.starts_at).utc().format('YYYY.MM.DD'))
							return 1;
						return a.station.id - b.station.id;
					});
					for (let i = 0; i < this.atcBookings.length; i++) {
						if(i == 0) {
							this.atcBookings[i].divider = true;
						} else {
							if(moment(this.atcBookings[i].starts_at).utc().format('YYYY.MM.DD') != moment(this.atcBookings[i-1].starts_at).utc().format('YYYY.MM.DD')) {
								this.atcBookings[i].divider = true;
							} else {
								this.atcBookings[i].divider = false;
							}
						}
					}
				}
			},
			convertDate: function (date) {
                return moment.utc(date).format('HH:mm');
            },
            displayDayFromDate(date) {
            	return moment.utc(date).format('DD.MM.YYYY');
            },
            renderTimeNow: function () {
                // Calculate UTC by subtracting the OFFSET
                return moment().subtract(moment().utcOffset(), 'minutes').startOf('hour');
            }
		},
		mounted() {
			if(!this.dashboard) {
				this.loadBookings();
				this.interval = setInterval(
	                function() {
	                    this.loadBookings();
	                }.bind(this),
	                1 * 60 * 1000
	            );
			}
		},
		activated() {
			this.loadBookings();
			this.interval = setInterval(
                function() {
                    this.loadBookings();
                }.bind(this),
                1 * 60 * 1000
            );
		},
		beforeDestroy: function(){
            clearInterval(this.interval);
            this.interval = null;
        }
	}
</script>
