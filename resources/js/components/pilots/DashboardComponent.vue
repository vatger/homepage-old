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
							<li class="breadcrumb-item active">
                                <router-link to="/pilots">
                                    {{ trans('pilots.pilotscorner') }}
                                </router-link>
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
						<!-- General ATC Bookings -->
						<atc-bookings :dashboard="true" :range="true"></atc-bookings>
					</div>
					<div class="col-sm-12 col-md-6">
						<atc-live :dashboard="true"></atc-live>
					</div>
				</div>
			</div>
		</section>
	</div>
</template>

<script>

	// Subcomponents
	import AtcBookings from '../booking/AtcBookingsComponent.vue';
	import AtcLive from '../network/AtcLiveComponent.vue';

	export default {
		components: {
			'atc-bookings': AtcBookings,
			'atc-live': AtcLive
		},
		data() {
			return {

			}
		},
		mounted() {

		}
	}
</script>
