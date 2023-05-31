<template>
	<div class="content-wrapper">
		<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('dashboard.membership') }} - {{ trans('regionalgroup.regionalgroups') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/membership">{{ trans('dashboard.dashboard') }}</router-link>
							</li>
							<li class="breadcrumb-item active">
								<router-link to="/membership/regionalgroups">{{ trans('regionalgroup.regionalgroups') }}</router-link>
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>

		<!-- Dashboard Content -->
		<section class="content">
			<div class="container-fluid">
				<div class="row" v-if="regionalgroups.length == 0">
					<div class="col-12">
						<div class="card bg-danger">
							<div class="card-header">
								<h3 class="card-title">Fehler</h3>
							</div>
							<div class="card-body">{{ trans('regionalgroup.nomember') }}</div>
						</div>
					</div>
				</div>
				<div class="row" v-else>
					<div class="col-12 col-md-6" v-for="rg in regionalgroups" v-bind:key="rg.id">
						<div class="card card-widget widget-user">
							<!-- Add the bg color to the header using any of the bg-* classes -->
							<div class="widget-user-header text-white" style="background-image: url('/images/splash/wing.jpg'); background-position: center center;background-size: cover; background-repeat: no-repeat;">
								<h3 class="widget-user-username text-right">{{ rg.name }}</h3>
								<h5 class="widget-user-desc text-right">{{ rg.fir.name }}</h5>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-6 border-right">
										<div class="description-block">
											<h5 class="description-header">{{ trans('regionalgroup.membershipstate') }}</h5>
											<span class="description-text">{{ rg.pivot.guest == 0 ? trans('regionalgroup.fullmember') : trans('regionalgroup.guestmember') }}</span>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="description-block">
											<h5 class="description-header">{{ trans('regionalgroup.membershipas') }}</h5>
											<span class="description-text">
												{{ trans('regionalgroup.atc') }}
												<i class="fas fa-check" v-if="rg.pivot.controller"></i>
												<i class="fas fa-times" v-else></i>
											</span>
											<hr class="my-2" />
											<span class="description-text">
												{{ trans('regionalgroup.pilot') }}
												<i class="fas fa-check" v-if="rg.pivot.pilot"></i>
												<i class="fas fa-times" v-else></i>
											</span>
										</div>
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
	export default {
		data() {
			return {
				regionalgroups: {},
			};
		},
		methods: {
			loadRegionalgroups() {
				axios.get("/api/regionalgroup").then((res) => {
					this.regionalgroups = res.data;
				});
			}
		},
		activated() {
			this.loadRegionalgroups();
		},
	};
</script>
