<template>
	<div class="content-wrapper">

		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('pilots.pilotscorner') }} - {{ trans('pilots.aerodromes.aerodromes') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/pilots">
									{{ trans('pilots.pilotscorner') }}
								</router-link>
							</li>
							<li class="breadcrumb-item" v-on:click="goBack">
								{{ trans('pilots.aerodromes.aerodromes') }}
							</li>
							<li class="breadcrumb-item active" v-if="selectedAerodrome != null">
								{{ selectedAerodrome.icao }}
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid" v-if="selectedAerodrome != null">
				<div class="row">
					<div class="col-md-3">
						<div class="card card-primary card-outline">
							<div class="card-body box-profile">
								<h3 class="profile-username text-center">{{ selectedAerodrome.name }}</h3>
								<p class="text-muted text-center">{{ selectedAerodrome.icao }}</p>
								<p class="text-muted text-center">{{ selectedAerodrome.iata }}</p>
								<a :href="selectedAerodrome.wiki_link" v-if="selectedAerodrome.wiki_link" target="_blank" class="text-center nav-link">Wikieintrag</a>
								<hr>
								<p class="text-muted text-center">{{ selectedAerodromeMetar }}</p>
							</div>
						</div>
					</div>
					<div class="col-md-9">
						<div class="card card-primary card-outline">
							<div class="card-header p-2">
								<ul class="nav nav-pills">
									<li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">{{ trans('pilots.aerodromes.general') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#stations" data-toggle="tab">{{ trans('pilots.aerodromes.stations.stations') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#navigation" data-toggle="tab">{{ trans('pilots.aerodromes.navigation.navigation') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#charts" data-toggle="tab">{{ trans('pilots.aerodromes.charts.charts') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#stands" data-toggle="tab">{{ trans('pilots.aerodromes.stands.stands') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#pilotactivity" data-toggle="tab">{{ trans('pilots.aerodromes.flights.flights') }}</a></li>
									<li class="nav-item"><a class="nav-link" href="#atcactivity" data-toggle="tab">{{ trans('pilots.aerodromes.atc.atc') }}</a></li>
								</ul>
							</div>
							<div class="card-body">
								<div class="tab-content">
									<div class="tab-pane active" id="general">
										<div class="row">
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">ICAO / IATA</span>
														<span class="info-box-number">{{ selectedAerodrome.icao+' / '+selectedAerodrome.iata }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.latitude') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.latitude }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.longitude') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.longitude }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.elevation') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.elevation }}</span>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.classification') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.major ? trans('pilots.aerodromes.major') : trans('pilots.aerodromes.minor') }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.military') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.military ? trans('pilots.aerodromes.yes') : trans('pilots.aerodromes.no') }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="far fa-bookmark"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.civilian') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.civilian ? trans('pilots.aerodromes.yes') : trans('pilots.aerodromes.no') }}</span>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="fas fa-globe"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.country') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.country_detail.name }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="fas fa-globe-europe"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.state') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.state }}</span>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-6 col-12">
												<div class="info-box bg-info">
													<span class="info-box-icon"><i class="fas fa-city"></i></span>
													<div class="info-box-content">
														<span class="info-box-text">{{ trans('pilots.aerodromes.city') }}</span>
														<span class="info-box-number">{{ selectedAerodrome.city }}</span>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<h5>{{ trans('pilots.aerodromes.description') }}</h5>
												<p v-if="selectedAerodrome.description.length > 0">{{ selectedAerodrome.description }}</p>
												<p class="text-warning" v-else>{{ trans('pilots.aerodromes.nodescription') }}</p>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="stations">
										<table class="table table-borderless table-hover table-responsive-sm">
											<thead>
												<tr>
													<th>#</th>
													<th>{{ trans('pilots.aerodromes.stations.station') }}</th>
													<th>{{ trans('pilots.aerodromes.stations.frequency') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="station in selectedAerodrome.stations" :key="station.id">
													<td>{{ station.pivot.order }}</td>
													<td>{{ station.name + '( ' + station.ident + ' )' }}</td>
													<td>{{ station.fixedFrequency }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="navigation">
										<table class="table table-borderless table-hover table-responsive-sm" id="runwaysTable">
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.navigation.runway') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.heading') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.length') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.width') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.surface.type') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="runway in selectedAerodrome.runways" v-bind:key="runway.id">
													<td>{{ runway.ident }}</td>
													<td>{{ runway.heading }}</td>
													<td>{{ runway.length }} m</td>
													<td>{{ runway.width }} m</td>
													<td>{{ runway.surfaceTypeString }}</td>
												</tr>
											</tbody>
										</table>
										<table class="table table-borderless table-hover table-responsive-sm" id="navaidTable">
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.navigation.navaid') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.heading') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.frequency') }}</th>
													<th>{{ trans('pilots.aerodromes.navigation.remarks') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="navaid in selectedAerodrome.navaids" v-bind:key="navaid.id">
													<td>{{ navaid.ident }} - {{ navaid.name }}</td>
													<td>{{ navaid.heading }}</td>
													<td>{{ navaid.frequency }} {{ navaid.frequencyBandString }}</td>
													<td>{{ navaid.remarks }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="charts">
										<table class="table table-borderless table-hover table-responsive-sm" id="chartsTable">
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.charts.name') }}</th>
													<th>{{ trans('pilots.aerodromes.charts.fri') }}</th>
													<th>{{ trans('pilots.aerodromes.charts.type') }}</th>
													<th>{{ trans('pilots.aerodromes.charts.airac') }}</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="chart in aerodromeCharts" v-bind:key="chart.id">
													<td>{{ chart.name }}</td>
													<td>{{ chart.fri | uppercase  }}</td>
													<td>{{ chart.type | uppercase }}</td>
													<td>{{ chart.airac }}</td>
													<td :class="'chart-'+chart.type" v-html="chart.downloadLink">
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="stands">
										<table class="table table-borderless table-hover table-responsive-sm" id="standTable" data-order='[[1, "desc"]]' data-page-length='25'>
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.stands.stand') }}</th>
													<th>{{ trans('pilots.aerodromes.stands.status') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="stand in selectedAerodromeStands" :key="stand.id" :class="stand.occupied !== undefined ? 'table-danger' : 'table-info'">
													<td>{{ stand.id }}</td>
													<td v-if="stand.occupied !== undefined" data-order="1">
														{{ stand.occupied.callsign }}
													</td>
													<td v-else data-order="0">{{ trans('pilots.aerodromes.stands.free') }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="pilotactivity">
										<table class="table table-responsive-sm table-borderless">
											<caption>vACC Germany Network Feed</caption>
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.flights.callsign') }}</th>
													<th>{{ trans('pilots.aerodromes.flights.origin') }}</th>
													<th>{{ trans('pilots.aerodromes.flights.destination') }}</th>
													<th>{{ trans('pilots.aerodromes.flights.connectedat') }}</th>
													<th>{{ trans('pilots.aerodromes.flights.eta') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="flight in selectedAerodromeFlights" v-bind:key="flight.id" :class="(flight.departure_airport == selectedAerodrome.icao) ? 'table-info' : 'table-primary'">
													<td>{{ flight.callsign }}</td>
													<td>{{ flight.departure_airport }}</td>
													<td>{{ flight.arrival_airport }}</td>
													<td>{{ ct(flight.connected_at) }}</td>
													<td>{{ ceta(flight.ExpectedArrivalTime) }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="atcactivity">
										<table class="table table-responsive-sm table-borderless">
											<caption>vACC Germany Network Feed</caption>
											<thead>
												<tr>
													<th>{{ trans('pilots.aerodromes.atc.station') }}</th>
													<th>{{ trans('pilots.aerodromes.atc.frequency') }}</th>
													<th>{{ trans('pilots.aerodromes.atc.connectedat') }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="atc in selectedAerodromeAtc" v-bind:key="atc.id">
													<td>{{ atc.callsign }}</td>
													<td>{{ atc.fixedFrequency }}</td>
													<td>{{ ct(atc.connected_at) }}</td>
												</tr>
											</tbody>
										</table>
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
	import moment from 'moment'

	export default {
		data() {
			return {
				icao: '',
				selectedAerodrome: null,
				selectedAerodromeStands: [],
				selectedAerodromeAtc: [],
				selectedAerodromeFlights: [],
				selectedAerodromeMetar: '',
				updateInterval: null
			}
		},
		methods: {
			loadAerodrome() {
				axios.get('/api/navigation/aerodrome/'+this.icao)
					.then(res => {
						this.selectedAerodrome = res.data;
						this.updateActivity();
						if(this.updateInterval == null){
							this.updateInterval = setInterval(
								function () {
									this.updateActivity();
								}.bind(this),
								60 * 1000
							);
						}
					});
			},
			updateActivity() {
				axios.get('/api/navigation/aerodrome/'+this.icao+'/stands')
					.then(res => {
						if ($.fn.DataTable.isDataTable( '#standTable' ) ) {
							$('#standTable').DataTable().destroy();
						}
						this.selectedAerodromeStands = res.data;
						this.$nextTick(() => {
							$('#standTable').DataTable();
						})
					});
				axios.get('/api/navigation/aerodrome/'+this.icao+'/atc')
					.then(res => {
						this.selectedAerodromeAtc = res.data;
					});
				axios.get('/api/navigation/aerodrome/'+this.icao+'/flights')
					.then(res => {
						this.selectedAerodromeFlights = res.data;
					});
				axios.get('/api/network/weather/'+this.icao)
					.then(res => {
						this.selectedAerodromeMetar = res.data;
					})
			},
			async getChartDownloadLink(chart) {
				await axios.get('/api/navigation/chart/'+chart.id)
					.then(res => {
						if(res.data.access && res.data.token !== false) {
							chart.downloadLink = '<a href="'+chart.href+'?token='+res.data.token+'" target="_blank"><i class="fa fa-download"></i> Download</a>';
						} else if(res.data.access && chart.is_gitlab) {
                            chart.downloadLink = '<a href="' + chart.gitlab_link + '" target="_blank"><i class="fa fa-download"></i> Download</a>';
                        } else if(res.data.access) {
							chart.downloadLink = '<a href="'+chart.href+'" target="_blank"><i class="fa fa-download"></i> Download</a>';
						} else {
							chart.downloadLink = trans('pilots.aerodromes.charts.unauthorized');
						}
					}).catch(error => {
						if(error.response.status == 403) {
							chart.downloadLink = trans('pilots.aerodromes.charts.unauthorized');
						}
					});
			},
			async getAerodromeCharts() {
				if(this.selectedAerodrome != null) {
					for(let i = 0; i < this.selectedAerodrome.charts.length; i++) {
						await this.getChartDownloadLink(this.selectedAerodrome.charts[i]);
					}

					return this.selectedAerodrome.charts;
				}
			},
			ceta: function(tr) {
				if(tr == -3) return trans('pilots.aerodromes.flights.status.unknown');
				if(tr == -2) return trans('pilots.aerodromes.flights.status.arrived');
				if(tr == -1) return trans('pilots.aerodromes.flights.status.departing');
				let eta = moment().utc().add(tr, 'h').format('HH:mm');
				return eta;
			},
			ct: function(timestamp) {
				return moment.utc(timestamp).format('HH:mm');
			},
			goBack() {
				this.$router.push('/pilots/aerodromes');
			}
		},
		asyncComputed: {
			async aerodromeCharts() {
				return await this.getAerodromeCharts();
			}
		},
		watch: {
			aerodromeCharts() {
				if ($.fn.DataTable.isDataTable( '#chartsTable' ) ) {
					$('#chartsTable').DataTable().destroy();
				}
				this.$nextTick(() => {
					$('#chartsTable').DataTable();
				});
			}
		},
        filters: {
            uppercase: function (value) {
                if (!value) return '';
                value = value.toString();
                return value.toUpperCase();
            }
        },
		activated() {
			this.icao = this.$route.params.icao;
			this.loadAerodrome();
		},
		deactivated() {
			clearInterval(this.updateInterval);
			this.updateInterval = null;
		},
		beforeDestroy() {
			clearInterval(this.updateInterval);
			this.updateInterval = null;
		}
	}
</script>
