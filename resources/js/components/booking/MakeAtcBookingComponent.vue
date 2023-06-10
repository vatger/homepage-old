<template>
	<div class="content-wrapper">

		<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>Buchungssystem - ATC</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/membership">Schreibtisch</router-link>
							</li>
							<li class="breadcrumb-item active">
								ATC
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>

		<!-- Dashboard Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-12 col-md-6">
						<!-- Personal ATC Bookings -->
						<myatc-bookings @editBooking="editBooking" @deleteBooking="deleteBooking" :editable="true" ref="personalBookings"></myatc-bookings>
						<!-- General ATC Bookings -->
						<atc-bookings :dashboard="true" :range="true" ref="atcBookings"></atc-bookings>
					</div>
					<div class="col-sm-12 col-md-6">
						<!-- Booking Form -->
						<div class="card card-warning card-outline" v-if="editmode">
							<div class="card-header">
								<h3 class="card-title">{{ trans('booking.make.editMode') }}</h3>
							</div>
							<div class="card-body">
								<ul class="list-unstyled alert alert-danger" v-if="errors">
					                <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
					            </ul>
					            <div class="callout callout-info" v-html="trans('booking.make.guide')"></div>
                                <div class="form-group">
                                	<label>{{ trans('booking.make.select.search') }}</label>
									<autocomplete
										:search="searchStation"
										:get-result-value="getSearchStationValue"
										placeholder="ATC Station Suchen"
										aria-label="ATC Station Suchen"
										@submit="handleEditSearchStationSubmit"
									></autocomplete>
                                </div>
					            <div class="form-group mb-2">
                                    <label>{{ trans('booking.make.select.aerodrome') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-plane"></i></span>
                                        </div>
                                        <select class="form-control" v-model="selectedAerodrome">
                                            <option v-for="a in aerodromes" :value="a" v-bind:key="a.id">{{ a.icao }} ({{ a.name }})</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group mt-2">
                                    <label>{{ trans('booking.make.select.station') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-wifi"></i></span>
                                        </div>
                                        <select class="form-control" v-model="editStation">
                                            <option v-for="s in availableStations" :value="s" v-bind:key="s.id">{{ s.ident }} ({{ s.name }})</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label>{{ trans('booking.make.select.begin') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <!-- <date-picker v-model="editFrom" lang="de" format="DD.MM.YYYY HH:mm" date-format="DD.MM.YYYY HH:mm" type="datetime" value-type="format" :first-day-of-week="1" :not-before="renderTimeNow()" :minute-step="15"></date-picker> -->
                                        <date-picker v-model="editFrom" lang="de" format="DD.MM.YYYY HH:mm" date-format="DD.MM.YYYY HH:mm" type="datetime" value-type="format" :first-day-of-week="1" :minute-step="15"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('booking.make.select.end') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <!-- <date-picker v-model="editTill" lang="de" format="DD.MM.YYYY HH:mm" date-format="DD.MM.YYYY HH:mm" type="datetime" value-type="format" :first-day-of-week="1" :not-before="renderTimeNow()" :minute-step="15"></date-picker> -->
                                        <date-picker v-model="editTill" lang="de" format="DD.MM.YYYY HH:mm" date-format="DD.MM.YYYY HH:mm" type="datetime" value-type="format" :first-day-of-week="1" :minute-step="15"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="editTraining">
										<label class="form-check-label">{{ trans('booking.make.select.training') }}</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="editEvent">
										<label class="form-check-label">{{ trans('booking.make.select.event') }}</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="editVoice">
										<label class="form-check-label">{{ trans('booking.make.select.voice') }}</label>
									</div>
								</div>
                                <div class="form-group">
                                	<button class="btn btn-default btn-block" v-on:click="saveEditedBooking">{{ trans('booking.make.save') }}</button>
                                	<button class="btn btn-danger btn-block" v-on:click="cancelEdit">{{ trans('booking.make.abort') }}</button>
                                </div>
							</div>
						</div>
						<div class="card card-primary card-outline" v-else>
							<div class="card-header">
								<h3 class="card-title">{{ trans('booking.make.newMode') }}</h3>
							</div>
							<div class="card-body">
								<ul class="list-unstyled alert alert-danger" v-if="errors">
					                <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
					            </ul>
					            <div class="callout callout-info" v-html="trans('booking.make.guide')"></div>
                                <div class="form-group">
                                	<label>{{ trans('booking.make.select.search') }}</label>
									<autocomplete
										:search="searchStation"
										:get-result-value="getSearchStationValue"
										placeholder="ATC Station Suchen"
										aria-label="ATC Station Suchen"
										@submit="handleSearchStationSubmit"
									></autocomplete>
                                </div>
					            <div class="form-group mb-2" v-if="station == null">
                                    <label>{{ trans('booking.make.select.aerodrome') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-plane"></i></span>
                                        </div>
                                        <select class="form-control" v-model="selectedAerodrome">
                                            <option v-for="a in aerodromes" :value="a" v-bind:key="'aa'+a.id">{{ a.icao }} ({{ a.name }})</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group mt-2">
                                    <label>{{ trans('booking.make.select.station') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-wifi"></i></span>
                                        </div>
                                        <select class="form-control" v-model="station">
                                            <option v-for="s in availableStations" :value="s" v-bind:key="'as'+s.id">{{ s.ident }} ({{ s.name }})</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label>{{ trans('booking.make.select.begin') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <!-- <date-picker v-model="from" lang="de" format="DD.MM.YYYY HH:mm" date-format="DD.MM.YYYY HH:mm" type="datetime" value-type="format" :first-day-of-week="1" :not-before="renderTimeNow()" :minute-step="15"></date-picker> -->
                                        <date-picker v-model="from" lang="de" format="DD.MM.YYYY" type="date" value-type="date" :not-before="new Date()"
                                                     :default-value="new Date()" partial-update="true"></date-picker>
                                        <date-picker v-model="from_time" lang="de" format="HH:mm" type="time" value-type="format" show-second="false" :minute-step="15"
                                                     partial-update="true"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('booking.make.select.end') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <date-picker v-model="till" lang="de" format="DD.MM.YYYY" type="date" value-type="date" :not-before="new Date()" :default-value="new Date()"
                                                     partial-update="true"></date-picker>
                                        <date-picker v-model="till_time" lang="de" format="HH:mm" type="time" value-type="format" show-second="false" :minute-step="15"
                                                     partial-update="true"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="training">
										<label class="form-check-label">{{ trans('booking.make.select.training') }}</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="event">
										<label class="form-check-label">{{ trans('booking.make.select.event') }}</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" v-model="voice">
										<label class="form-check-label">{{ trans('booking.make.select.voice') }}</label>
									</div>
								</div>
                                <div class="form-group">
                                	<button class="btn btn-default btn-block" v-on:click="bookStation">{{ trans('booking.make.save') }}</button>
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
	import DatePicker from 'vue2-datepicker'
	import Autocomplete from '@trevoreyre/autocomplete-vue'
	import '@trevoreyre/autocomplete-vue/dist/style.css'
	// Subcomponents
	import MyAtcBookings from '../booking/PersonalBookingsComponent.vue';
	import AtcBookings from '../booking/AtcBookingsComponent.vue';

	export default {
		components: {
			DatePicker,
			Autocomplete,
			'myatc-bookings': MyAtcBookings,
			'atc-bookings': AtcBookings
		},
		data() {
			return {
				editmode: false,
				aerodromes: {},
				selectedAerodrome: null,
				availableStations: {},
				station: null,
				from: new Date(),
                from_time: null,
				till: new Date(),
                till_time: null,
				training: false,
				event: false,
				voice: true,
				selectedBooking: null,
				editStation: null,
				editFrom: null,
				editTill: null,
				editTraining: false,
				editEvent: false,
				editVoice: true,
				errors: false
			}
		},
		methods: {
            getAerodromes() {
                axios.get('/api/navigation/aerodromes/local')
                    .then(res => {
                        this.aerodromes = res.data;
                    });
            },
            searchStation(input) {
                if (input.length < 3) {
                    return []
                }
                return this.availableStations.filter(station => {
                    return station.ident.toLowerCase().includes(input.toLowerCase()) || station.name.toLowerCase().includes(input.toLowerCase());
                });
            },
            getSearchStationValue(result) {
                return result.ident + ' ( ' + result.name + ' )';
            },
            handleSearchStationSubmit(result) {
                if (result != undefined && result != null) {
                    this.station = result;
                }
            },
            handleEditSearchStationSubmit(result) {
                if (result != undefined && result != null) {
                    this.editStation = result;
                }
            },
            bookStation: function () {
                this.errors = false;
                axios.post('/api/booking/atc', {
                    station: this.station.id,
                    from: this.convertDateOnly(this.from) + " " + this.from_time,
                    till: this.convertDateOnly(this.till) + " " + this.till_time,
                    training: this.training,
                    event: this.event,
                    voice: this.voice
                }).then(res => {
                    if (res.data.success) {
                        $('#sysMessages').append('<p class="text-success mb-2">Buchung erfolgreich gespeichert.</p>');
                        this.till = null;
                        this.from = null;
                        this.station = null;
                        this.$refs.personalBookings.update();
                        this.$refs.atcBookings.update();
                        this.errors = false;
                    } else {
                        $('#sysMessages').append('<p class="text-danger mb-2">Buchung fehlgeschlagen.</p>');
                    }
                }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
            },
            editBooking(params) {
                this.editmode = true;
                this.selectedBooking = params;
                this.editStation = params.station;
                this.editTraining = params.training;
                this.editEvent = params.event;
                this.editVoice = params.voice;
                this.editFrom = this.convertDate(params.starts_at);
                this.editTill = this.convertDate(params.ends_at);
            },
            saveEditedBooking() {
                this.errors = false;
                axios.put('/api/booking/atc/' + this.selectedBooking.id, {
                    station: this.editStation.id,
                    from: this.editFrom,
                    till: this.editTill,
                    training: this.editTraining,
                    event: this.editEvent,
                    voice: this.editVoice
                }).then(res => {
                    if (res.data.success) {
                        $('#sysMessages').append('<p class="text-success mb-2">Buchung erfolgreich geändert.</p>');
                        this.editTill = null;
                        this.editFrom = null;
                        this.editStation = null;
                        this.editTraining = false;
                        this.editEvent = false;
                        this.editVoice = false;
                        this.editmode = false;
                        this.$refs.personalBookings.update();
                        this.$refs.atcBookings.update();
                        this.errors = false;
                    } else {
                        $('#sysMessages').append('<p class="text-danger mb-2">Änderung der Buchung fehlgeschlagen.</p>');
                    }
                }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
            },
            cancelEdit() {
                this.editmode = false;
                this.selectedBooking = null;
                this.editStation = null;
                this.editFrom = null;
                this.editTill = null;
                this.editTraining = false;
                this.editEvent = false;
                this.editVoice = false;
                this.errors = false;
            },
            deleteBooking(params) {
                axios.delete('/api/booking/atc/' + params.id)
                    .then(res => {
                        if (res.data.success) {
                            $('#sysMessages').append('<p class="text-success mb-2">Buchung erfolgreich gelöscht.</p>');
                            this.$refs.personalBookings.update();
                            this.$refs.atcBookings.update();
                            this.errors = false;
                        } else {
                            $('#sysMessages').append('<p class="text-danger mb-2">Löschen der Buchung fehlgeschlagen.</p>');
                        }
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
            },
            loadAvailableStations: function () {
                axios.get('/api/navigation/stations')
                    .then(res => {
                        this.availableStations = res.data
                    });
            },
            renderTimeNow: function () {
                // Calculate UTC by subtracting the OFFSET
                return moment().subtract(moment().utcOffset(), 'minutes').startOf('hour');
            },
            convertDate: function (date) {
                return moment.utc(date).format('DD.MM.YYYY HH:mm');
            },
            convertDateOnly: function (date)
            {
                return moment.utc(date).format('DD.MM.YYYY');
            },
        },
		computed: {
        	validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        },
        watch: {
        	selectedAerodrome(oldVal, newVal) {
        		if(this.selectedAerodrome.stations != null && this.selectedAerodrome.stations.length >= 1)
        			this.availableStations = this.selectedAerodrome.stations;
        		else
        			this.loadAvailableStations();
        	}
        },
		activated() {
			this.getAerodromes();
			this.loadAvailableStations();
			this.from = this.renderTimeNow();
		}
	}
</script>
