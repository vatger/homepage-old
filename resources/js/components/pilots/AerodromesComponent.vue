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
							<li class="breadcrumb-item active">
								{{ trans('pilots.aerodromes.aerodromes') }}
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">{{ trans('pilots.aerodromes.aerodromes') }}</h5>
							</div>
							<div class="card-body">
								<div class="form-group">
                                	<label>{{ trans('pilots.aerodromes.search') }}</label>
									<autocomplete
										:search="searchAerodrome"
										:get-result-value="getSearchAerodromeValue"
										placeholder="ICAO, Name"
										aria-label="Aerodrome Suchen"
										@submit="handleSearchAerodromeSubmit"
									></autocomplete>
                                </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">{{ trans('pilots.aerodromes.aerodromes') }}</h5>
							</div>
							<div class="card-body">
								<div class="row py-3" v-for="chunk in chunked" v-bind:key="chunk[0].id">
									<div class="col-4" v-for="aerodrome in chunk" v-bind:key="aerodrome.id">
										<router-link :to="'/pilots/aerodrome/'+aerodrome.icao">
											{{ aerodrome.icao }} <br/> {{ aerodrome.name }}
										</router-link>
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
	import Autocomplete from '@trevoreyre/autocomplete-vue'
	import '@trevoreyre/autocomplete-vue/dist/style.css'
	export default{
		data() {
			return {
				availableAerodromes: []
			}
		},
		methods: {
			loadAvailableAerodromes() {
				axios.get('/api/navigation/aerodromes/1')
					.then(res => {
						this.availableAerodromes = res.data;
					});
			},
			searchAerodrome(input) {
				if(input.length < 3) {return [] }
				return this.availableAerodromes.filter(aerodrome => {
					return aerodrome.icao.toLowerCase().includes(input.toLowerCase()) || aerodrome.name.toLowerCase().includes(input.toLowerCase());
				});
			},
			getSearchAerodromeValue(result) {
				return result.icao + ' ( ' + result.name + ' )';
			},
			handleSearchAerodromeSubmit(result) {
				if(result != undefined && result != null) {
					// this.station = result;
					this.$router.push('/pilots/aerodrome/'+result.icao);
				}
			}
		},
		computed: {
			chunked () {
				return _.chunk(this.availableAerodromes, 3);
			}
		},
		activated() {
			this.loadAvailableAerodromes();
		}
	}
</script>
