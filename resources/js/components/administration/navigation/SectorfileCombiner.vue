<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - SectorFile Combiner</h1>
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

        <section class="content">
            <div class="card card-primary card-outline" v-if="generating">
                <div class="small-box bg-info">
                <!-- Loading (remove the following to stop the loading)-->
                    <div class="overlay">
                        <i class="fas fa-3x fa-sync-alt"></i>
                    </div>
                <!-- end loading -->
                    <div class="inner">
                        <h3>Generating Sectorfile</h3>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation"></i>
                    </div>
                </div>
            </div>
        	<div class="card card-primary card-outline" v-else>
        		<div class="card-header">
        			<h5 class="card-title">SectorFile Combiner</h5>
        		</div>
        		<div class="card-body">
                    <ul class="list-unstyled alert alert-danger" v-if="errors">
                        <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
                    </ul>
                    <div class="form-group">
                        <label for="airac">Airac URL From GNG:</label>
                        <input type="text" class="form-control" id="airac" v-model="airac">
                    </div>
                    <div class="form-group">
                        <label for="cso">Master Sector</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="cso" ref="cso" v-on:change="handleSectorOne">
                                <label class="custom-file-label" for="cso">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cst">Sector To Merge Into Master</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="cst" ref="cst" v-on:change="handleSectorTwo">
                                <label class="custom-file-label" for="cst">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-default" v-on:click="combineSectors">Start Combining</button>
        		</div>
        	</div>
            <div class="card card-primary card-outline" v-if="sector.length > 0">
                <div class="card-header">
                    <h5 class="card-title">Resulting Sectorfile</h5>
                </div>
                <div class="card-body" v-html="sector">
                </div>
            </div>
        </section>
    </div>
</template>

<script>
	export default{
		data() {
			return {
                airac: '',
                customSectorOne: null,
                customSectorTwo: null,
                sector: '',
                generating: false,
                errors: false
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
            handleSectorOne() {
                this.customSectorOne = this.$refs.cso.files[0];
            },
            handleSectorTwo() {
                this.customSectorTwo = this.$refs.cst.files[0];
            },
            combineSectors: function () {
                this.generating = true;
                /*
                Initialize the form data
                */
                let formData = new FormData();

                /*
                    Add the form data we need to submit
                */
                formData.append('airac', this.airac);
                formData.append('sector_one', this.customSectorOne);
                formData.append('sector_two', this.customSectorTwo);

                /*
                  Make the request to the POST /single-file URL
                */
                axios.post( '/api/administration/navigation/sct/combine',
                    formData,
                    {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                  }
                ).then(res => {
                    this.generating = false;
                    this.sector = res.data;
                })
                .catch(error => {
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        this.generating = false;
                    }
                });
            }
		}
	}
</script>