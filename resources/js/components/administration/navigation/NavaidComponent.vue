<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - Navaids</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <a v-on:click="selectedChart = null">Navaid Verwaltung</a>
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
            <ul class="list-unstyled alert alert-danger" v-if="errors">
                <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
            </ul>
        	<div class="card card-primary card-outline" v-if="createMode">
        		<div class="card-header">
        			<h5 class="card-title">Navaid Hinzufügen</h5>
        			<div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="createMode = false; newNavaid = {}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
        		</div>
        		<div class="card-body">
        			<div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="newNavaid.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Ident</label>
                        <input type="text" class="form-control" id="ident" v-model="newNavaid.ident">
                    </div>
                    <div class="form-group">
                        <label for="hdg">Bearing</label>
                        <input type="number" step="1" class="form-control" id="hdg" v-model="newNavaid.heading">
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" class="form-control" v-model="newNavaid.type">
                            <option value="1">NDB</option>
                            <option value="2">VOR</option>
                            <option value="3">VOR / DME</option>
                            <option value="4">DME</option>
                            <option value="5">ILS</option>
                            <option value="6">TACAN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frq">Frequency</label>
                        <input type="number" step=".001" class="form-control" id="frq" v-model="newNavaid.frequency">
                    </div>
                    <div class="form-group">
                        <label for="type">Frequency Band</label>
                        <select id="type" class="form-control" v-model="newNavaid.frequency_band">
                            <option value="1">Mhz</option>
                            <option value="2">Khz</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rmks">Remarks</label>
                        <textarea class="form-control" v-model="newNavaid.remarks"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="createNavaid">Speichern</button>
                    </div>
        		</div>
        	</div>
        	<div class="card card-primary card-outline" v-if="editMode && selectedNavaid != null">
        		<div class="card-header">
        			<h5 class="card-title">Navaid Bearbeiten</h5>
        			<div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="editMode = false; selectedNavaid = null">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
        		</div>
        		<div class="card-body">
        			<div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="selectedNavaid.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">Ident</label>
                        <input type="text" class="form-control" id="ident" v-model="selectedNavaid.ident">
                    </div>
                    <div class="form-group">
                        <label for="hdg">Bearing</label>
                        <input type="number" step="1" class="form-control" id="hdg" v-model="selectedNavaid.heading">
                    </div>
                    <div class="form-group">
                    	<label for="type">Type</label>
                    	<select id="type" class="form-control" v-model="selectedNavaid.type">
                    		<option value="1">NDB</option>
                    		<option value="2">VOR</option>
                            <option value="3">VOR / DME</option>
                            <option value="4">DME</option>
                            <option value="5">ILS</option>
                            <option value="6">TACAN</option>
                    	</select>
                    </div>
                    <div class="form-group">
                        <label for="frq">Frequency</label>
                        <input type="number" step=".001" class="form-control" id="frq" v-model="selectedNavaid.frequency">
                    </div>
                    <div class="form-group">
                    	<label for="type">Frequency Band</label>
                    	<select id="type" class="form-control" v-model="selectedNavaid.frequency_band">
                    		<option value="1">Mhz</option>
                    		<option value="2">Khz</option>
                    	</select>
                    </div>
                    <div class="form-group">
                        <label for="rmks">Remarks</label>
                        <textarea class="form-control" v-model="selectedNavaid.remarks"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="updateNavaid">Speichern</button>
                    </div>
        		</div>
        	</div>
            <div class="card card-primary card-outline">
    			<div class="card-header">
    				<h5 class="card-title">Navaids</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-hover table-bordered table-responsive-sm" id="navaidsTable">
                        <thead>
	                        <tr>
	                            <th>Name</th>
                                <th>Bearing</th>
                                <th>Frequency</th>
                                <th>Type</th>
	                            <th></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr v-for="n in navaids" v-bind:key="n.id">
	                            <td>{{ n.name }} ({{ n.ident }})</td>
                                <td>{{ n.heading }}</td>
                                <td>{{ n.frequency }} {{ n.frequencyBandString }}</td>
                                <td>{{ n.typeString }}</td>
	                            <td>
	                            	<button class="btn btn-sm btn-default" v-on:click="selectedNavaid=n; editMode=true">Bearbeiten</button>
	                                <button class="btn btn-sm btn-danger" v-on:click="deleteNavaid(n)">Löschen</button>
	                            </td>
	                        </tr>
	                    </tbody>
	                    <tfoot>
	                    	<tr>
	                    		<td colspan="5">
	                    			<button class="btn btn-default btn-block" v-on:click="createMode = true">Neues Navaid Hinzufügen</button>
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
                errors: false,
				navaids: {},
				createMode: false,
				newNavaid: {},
				editMode: false,
				selectedNavaid: null
			}
		},
		methods: {
			loadNavaids() {
				axios.get('/api/administration/navigation/navaids')
					.then(res => {
						this.navaids = res.data;
						this.$nextTick(() => {
							$('#navaidsTable').DataTable();
						})
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                        if(error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        }
                    })
			},
            createNavaid() {
                axios.post('/api/administration/navigation/navaids', {
                    newNavaid: this.newNavaid
                }).then(res => {
                    $('#navaidsTable').DataTable().destroy();
                    this.navaids.push(res.data);
                    this.$nextTick(() => {
                        $('#navaidsTable').DataTable();
                    })
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            updateNavaid() {
                axios.put('/api/administration/navigation/navaids/' + this.selectedNavaid.id, {
                    editNavaid: this.selectedNavaid
                }).then(res => {
                    $('#navaidsTable').DataTable().destroy();
                    this.navaids.pop(this.selectedNavaid);
                    this.navaids.push(res.data);
                    this.$nextTick(() => {
                        $('#navaidsTable').DataTable();
                    })
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            deleteNavaid(navaid) {
                axios.delete('/api/administration/navigation/navaids/' + navaid.id)
                    .then(res => {
                        this.navaids.pop(navaid);
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                        if(error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        }
                    });
            }
        },
        computed: {
            validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        },
		activated() {
			this.loadNavaids();
		}
	}
</script>
