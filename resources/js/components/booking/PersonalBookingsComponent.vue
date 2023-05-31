<template>
	<div class="card card-primary card-outline collapsed">
		<div class="card-header">
			<h5 class="card-title">{{ trans('booking.mybookings') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-minus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: block;">
			<table class="table table-borderless table-hover table-responsive-sm" v-if="personalBookings.length > 0">
				<thead>
					<tr v-if="editable">
						<th>{{ trans('booking.station') }}</th>
						<th>{{ trans('booking.frequency') }}</th>
						<th>{{ trans('booking.timeframe') }}</th>
						<th></th>
					</tr>
					<tr v-else>
						<th>{{ trans('booking.station') }}</th>
						<th>{{ trans('booking.frequency') }}</th>
						<th>{{ trans('booking.timeframe') }}</th>
					</tr>
				</thead>
				<tbody>
					<template v-for="atc in personalBookings">
						<tr v-if="atc.divider" class="divider" v-bind:key="'pb.'+atc.id">
							<td colspan="4" v-if="editable">{{ displayDayFromDate(atc.starts_at) }}</td>
							<td colspan="3" v-else>{{ displayDayFromDate(atc.starts_at) }}</td>
						</tr>
						<tr v-bind:key="atc.id">
							<td>{{ atc.station.ident }}</td>
							<td>{{ atc.station.fixedFrequency }}</td>
							<td>{{ convertDate(atc.starts_at) }} - {{ convertDate(atc.ends_at) }}</td>
							<td v-if="editable">
								<button v-on:click="edit(atc)" class="btn btn-primary btn-sm">{{ trans('booking.modify') }}</button>
								<button v-on:click="remove(atc)" class="btn btn-danger btn-sm">{{ trans('booking.delete') }}</button>
							</td>
						</tr>
					</template>
				</tbody>
				<tfoot>
                    <tr>
                        <td colspan="4" v-if="editable">All Times UTC</td>
                        <td colspan="3" v-else>All Times UTC</td>
                    </tr>
                </tfoot>
			</table>
			<div class="alert alert-info" v-else>
              <h5><i class="icon fas fa-info"></i> {{ trans('booking.nobookings') }}</h5>
              {{ trans('booking.nobookings_actions') }}
            </div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment'

	export default {
		props: {
			editable: false
		},
		data() {
			return {
				personalBookings: {}
			}
		},
		methods: {
			loadPersonalBookings: function () {
				axios.get('/api/booking/atc/personal')
					.then(res => {
						this.personalBookings = res.data;
						this.addDividers();
					}).catch(error => {
						console.log(error.response.status);
					});
			},
			edit(atc) {
				this.$emit('editBooking', atc);
			},
			remove(atc) {
				this.$emit('deleteBooking', atc);
			},
			update() {
				this.loadPersonalBookings();
			},
			addDividers() {
				if(this.personalBookings != null && this.personalBookings.length > 0) {
					this.personalBookings.sort((a,b) => {
						if(moment(a.starts_at).utc().format('YYYY.MM.DD') < moment(b.starts_at).utc().format('YYYY.MM.DD'))
							return -1;
						if(moment(a.starts_at).utc().format('YYYY.MM.DD') > moment(b.starts_at).utc().format('YYYY.MM.DD'))
							return 1;
						return a.station.id - b.station.id;
					});
					for (let i = 0; i < this.personalBookings.length; i++) {
						if(i == 0) {
							this.personalBookings[i].divider = true;
						} else {
							if(moment(this.personalBookings[i].starts_at).utc().format('YYYY.MM.DD') != moment(this.personalBookings[i-1].starts_at).utc().format('YYYY.MM.DD')) {
								this.personalBookings[i].divider = true;
							} else {
								this.personalBookings[i].divider = false;
							}
						}
					}
				}
			},
			displayDayFromDate(date) {
            	return moment.utc(date).format('DD.MM.YYYY');
            },
			convertDate: function (date) {
                return moment.utc(date).format('HH:mm');
            }
		},
		mounted() {
			this.loadPersonalBookings();
			this.interval = setInterval(
                function() {
                    this.update();
                }.bind(this),
                1 * 60 * 1000
            );
		},
		activated() {
			this.loadPersonalBookings();
			this.interval = setInterval(
                function() {
                    this.update();
                }.bind(this),
                1 * 60 * 1000
            );
		}
	}
</script>