<template>
	<div class="row">
		<div class="col-12">
			<p v-if="!aerodromes">
				Loading data... Please stand by!
			</p>
			<div class="form-group" v-else>
				<label>{{ trans('statistics.aerodromes.search') }}</label>
				<autocomplete
					:search="searchAerodrome"
					:get-result-value="getSearchAerodromeValue"
					placeholder="ICAO, City, Name"
					aria-label="ICAO, City, Name"
					@submit="handleSearchAerodromeSubmit"
				></autocomplete>
			</div>
			<div class="form-group" v-if="selectedAerodrome != null">
                <label>{{ trans('statistics.aerodromes.begin') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <date-picker v-model="from" lang="de" format="DD.MM.YYYY" date-format="DD.MM.YYYY" type="date" value-type="format" :first-day-of-week="1"></date-picker>
                </div>
            </div>
            <div class="form-group" v-if="from != null">
                <label>{{ trans('statistics.aerodromes.end') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <date-picker v-model="till" lang="de" format="DD.MM.YYYY" date-format="DD.MM.YYYY" type="date" value-type="format" :first-day-of-week="1"></date-picker>
                </div>
            </div>
            <div class="form-group" v-if="selectedAerodrome != null && from != null && till != null">
            	<a class="btn btn-default btn-block" :href="'/aerodrome/'+selectedAerodrome.icao+'/'+from+'/'+till">Show Data</a>
            </div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment'
	import DatePicker from 'vue2-datepicker'
	import Autocomplete from '@trevoreyre/autocomplete-vue'
	import '@trevoreyre/autocomplete-vue/dist/style.css'

	export default{
		components: {
			DatePicker,
			Autocomplete
		},
		data() {
			return {
				aerodromes: false,
				selectedAerodrome: null,
				from: null,
				till: null
			}
		},
		methods: {
			getAerodromes() {
				axios.get('/api/navigation/aerodromes/0')
					.then(res => {
						this.aerodromes = res.data;
					});
			},
			searchAerodrome(input) {
				if(input.length < 3) {return [] }
				return this.aerodromes.filter(a => {
					return a.icao.toLowerCase().includes(input.toLowerCase()) || a.name.toLowerCase().includes(input.toLowerCase()) || a.city.toLowerCase().includes(input.toLowerCase());
				});
			},
			getSearchAerodromeValue(result) {
				return result.icao + ' - ' + result.name + ' - ' + result.city + ' ( ' + result.country + ' )';
			},
			handleSearchAerodromeSubmit(result) {
				if(result != undefined && result != null) {
					this.selectedAerodrome = result;
				}
			}
		},
		mounted() {
			this.getAerodromes();
		}
	}
</script>