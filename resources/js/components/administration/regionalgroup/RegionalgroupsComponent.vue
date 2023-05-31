<template>
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Administration - Regionalgruppen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                        	<router-link to="/administration">
                            	Administration
                        	</router-link>
                        </li>
                        <li class="breadcrumb-item">
                        	<router-link to="/administration/regionalgroups">
                            	Regionalgruppen
                        	</router-link>
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
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-6" v-for="rg in regionalgroups" v-bind:key="rg.id">
					<div class="card card-widget widget-user">
					<!-- Add the bg color to the header using any of the bg-* classes -->
						<div class="widget-user-header text-white" style="background-image: url('/images/splash/bus.jpg'); background-position: center center;background-size: cover; background-repeat: no-repeat;">
							<h3 class="widget-user-username text-right">{{ rg.name }}</h3>
							<h5 class="widget-user-desc text-right">{{ rg.fir.name }}</h5>
						</div>
						<div class="card-footer">
							<div class="row">
								<div class="col-12">
									<div class="description-block">
										<h5 class="description-header">Administration</h5>
										<span class="description-text"><button class="btn btn-sm btn-default" v-on:click="selectRegionalgroup(rg)">Auswählen</button></span>
									</div>
								<!-- /.description-block -->
								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->
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
				accessDenied: false,
				regionalgroups: {}
			}
		},
		methods: {
			loadRegionalgroups(){
				axios.get('/api/administration/regionalgroups')
					.then(res => {
						this.regionalgroups = res.data;
					}).catch(error => {
						if(error.response.status == 403) this.accessDenied = true;
					})
			},
			selectRegionalgroup(rg) {
				this.$router.push('/administration/regionalgroups/'+rg.id);
			}
		},
		activated() {
			this.loadRegionalgroups();
		}
	}
</script>