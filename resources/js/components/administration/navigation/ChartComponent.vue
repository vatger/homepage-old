<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - Charts</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <a v-on:click="selectedChart = null">Chartverwaltung</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="accessDenied">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-danger">
                            <div class="card-header">
                                <h3 class="card-title">Zugang Verweigert</h3>
                            </div>
                            <div class="card-body">
                                Du hast nicht die erforderlichen Rechte, um diese Quelle aufzurufen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-else>
        	<div class="card card-primary card-outline" v-if="createMode">
        		<div class="card-header">
        			<h5 class="card-title">Chart Hinzufügen</h5>
        			<div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="createMode = false; newChart = {}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
        		</div>
        		<div class="card-body">
        			<div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="newChart.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Link</label>
                        <input type="text" class="form-control" id="ident" v-model="newChart.href">
                    </div>
                    <div class="form-group">
                        <label for="freq">AIRAC</label>
                        <input type="number" step="1" class="form-control" id="freq" v-model="newChart.airac">
                    </div>
                    <div class="form-group">
                    	<label for="fri">FRI</label>
                    	<select id="fri" class="form-control" v-model="newChart.fri">
                    		<option value="ifr">IFR</option>
                    		<option value="vfr">VFR</option>
                    	</select>
                    </div>
                    <div class="form-group">
                    	<label for="type">Type</label>
                    	<select id="type" class="form-control" v-model="newChart.type">
                    		<option value="aoi">AOI</option>
                    		<option value="afc">AFC</option>
                    		<option value="agc">AGC</option>
                    		<option value="apc">APC</option>
                    		<option value="sid">SID</option>
                    		<option value="star">STAR</option>
                    		<option value="iac">IAC</option>
                    	</select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="pubSwitch" v-model="newChart.published">
                            <label class="custom-control-label" for="pubSwitch">Veröffentlicht?</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="avilSwitch" v-model="newChart.public_available">
                            <label class="custom-control-label" for="avilSwitch">Öffentlich einsehbar? -</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="createChart">Speichern</button>
                    </div>
        		</div>
        	</div>
        	<div class="card card-primary card-outline" v-if="editMode && selectedChart != null">
        		<div class="card-header">
        			<h5 class="card-title">Chart Bearbeiten</h5>
        			<div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="editMode = false; selectedChart = null">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
        		</div>
        		<div class="card-body">
        			<div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="selectedChart.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Link</label>
                        <input type="text" class="form-control" id="ident" v-model="selectedChart.href">
                    </div>
                    <div class="form-group">
                        <label for="freq">AIRAC</label>
                        <input type="number" step="1" class="form-control" id="freq" v-model="selectedChart.airac">
                    </div>
                    <div class="form-group">
                    	<label for="fri">FRI</label>
                    	<select id="fri" class="form-control" v-model="selectedChart.fri">
                    		<option value="ifr">IFR</option>
                    		<option value="vfr">VFR</option>
                    	</select>
                    </div>
                    <div class="form-group">
                    	<label for="type">Type</label>
                    	<select id="type" class="form-control" v-model="selectedChart.type">
                    		<option value="aoi">AOI</option>
                    		<option value="afc">AFC</option>
                    		<option value="agc">AGC</option>
                    		<option value="apc">APC</option>
                    		<option value="sid">SID</option>
                    		<option value="star">STAR</option>
                    		<option value="iac">IAC</option>
                    	</select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="pubEditSwitch" v-model="selectedChart.published">
                            <label class="custom-control-label" for="pubEditSwitch">Veröffentlicht?</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="availEditSwitch" v-model="selectedChart.public_available">
                            <label class="custom-control-label" for="availEditSwitch">Öffentlich einsehbar?</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="updateChart">Speichern</button>
                    </div>
        		</div>
        	</div>
            <div class="card card-primary card-outline">
    			<div class="card-header">
    				<h5 class="card-title">Charts</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-hover table-bordered table-responsive-sm" id="chartsTable">
                        <thead>
	                        <tr>
	                            <th>Name</th>
	                            <th>Pfad</th>
	                            <th>FRI</th>
	                            <th>Type</th>
	                            <th>Airac</th>
	                            <th>Publiziert</th>
                                <th>Öffentlich</th>
	                            <th></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr v-for="chart in charts" :class="'chart-'+chart.type" v-bind:key="chart.id">
	                            <td>{{ chart.name }}</td>
	                            <td>
                                    <a :href="chart.href" target="_blank">{{ chart.href.split('files/').pop() }}</a>
                                </td>
	                            <td>{{ chart.fri }}</td>
	                            <td>{{ chart.type }}</td>
	                            <td>{{ chart.airac }}</td>
	                            <td>{{ chart.published ? 'Ja' : 'Nein' }}</td>
                                <td>{{ chart.public_available ? 'Ja' : 'Nein' }}</td>
	                            <td>
	                            	<button class="btn btn-sm btn-default" v-on:click="selectedChart=chart; editMode=true">Bearbeiten</button>
	                                <button class="btn btn-sm btn-danger" v-on:click="removeChart(chart)">Löschen</button>
	                            </td>
	                        </tr>
	                    </tbody>
	                    <tfoot>
	                    	<tr>
	                    		<td colspan="8">
	                    			<button class="btn btn-default btn-block" v-on:click="createMode = true">Neue Chart Hinzufügen</button>
	                    		</td>
	                    	</tr>
	                    </tfoot>
                    </table>
    			</div>
    		</div>
        </section>
    </div>
</template>

<script>
	export default{
		data() {
			return {
                accessDenied: false,
				charts: {},
				createMode: false,
				newChart: {},
				editMode: false,
				selectedChart: null
			}
		},
		methods: {
			loadCharts() {
				axios.get('/api/administration/navigation/chart')
					.then(res => {
						this.charts = res.data;
						this.$nextTick(() => {
							$('#chartsTable').DataTable();
						})
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			},
			createChart() {
				axios.post('/api/administration/navigation/chart', {
					chart: this.newChart
				}).then(res => {
					$('#chartsTable').DataTable().destroy();
					this.charts.push(res.data);
					this.newChart = {};
					this.$nextTick(() => {
						$('#chartsTable').DataTable();
					})
				}).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
			},
			updateChart() {
				axios.put('/api/administration/navigation/chart/'+this.selectedChart.id, {
					chart: this.selectedChart
				}).then(res => {
					$('#chartsTable').DataTable().destroy();
					this.loadCharts();
					this.selectedChart = res.data;
				}).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
			},
			removeChart(chart) {
				axios.delete('/api/administration/navigation/chart/'+chart.id)
					.then(res => {
						$('#chartsTable').DataTable().destroy();
						this.loadCharts();
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			}
		},
		activated() {
			this.loadCharts();
		}
	}
</script>
