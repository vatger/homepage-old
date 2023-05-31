<template>
    <div class="content-wrapper">

    	<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('pilots.pilotscorner') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
                                <router-link to="/pilots">
                                    {{ trans('pilots.pilotscorner') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ trans('pilots.weather.weatherdata') }}
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
                                <h3 class="card-title">{{ trans('pilots.weather.weatherdata') }}</h3>
                            </div>
                            <div class="card-body">
        						<div class="form-group">
        							<label>{{ trans('pilots.weather.entericao') }}</label>
        							<input type="text" v-model="icaolist" class="form-control">
                                    <span class="text-muted text-sm">{{ trans('pilots.weather.listsupport') }}</span>
        						</div>
                            </div>
                            <div class="card-footer" v-if="resultsAvailable">
                                <ul class="list-unstyled">
                                    <li v-if="typeof weatherData == 'string'">{{ weatherData }}</li>
                                    <li v-for="wd in weatherData" v-bind:key="wd" v-else>{{ wd }}</li>
                                </ul>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</section>

    </div>
</template>

<script>
    export default {
    	data() {
    		return {
                icaolist: '',
                weatherData: {},
                resultsAvailable: false
    		}
    	},
    	watch: {
            icaolist() {
                this.grabWeatherData(this.icaolist);
            }
    	},
    	methods: {
    		grabWeatherData(icaoString) {
    			axios.get('/api/network/weather/'+icaoString)
    				.then(res => {
    					this.weatherData = res.data;
                        if(res.data)
                            this.resultsAvailable = true;
    				}).catch(error => {

    				});
    		}
    	}
    }
</script>
