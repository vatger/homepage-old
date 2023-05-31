<template>
	<div class="content-wrapper">

		<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('dashboard.membership') }} - {{ trans('dashboard.dashboard') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item active">
								<router-link to="/membership">{{ trans('dashboard.dashboard') }}</router-link>
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
						<myatc-bookings></myatc-bookings>
						<!-- General ATC Bookings -->
						<atc-bookings :dashboard="true" :range="true"></atc-bookings>
						<!-- Current ATC -->
						<atc-live :dashboard="true"></atc-live>
					</div>
					<div class="col-sm-12 col-md-6">
                        <!-- Profile-->
                        <profile-component></profile-component>
                        <!-- Keys -->
                        <survey-component></survey-component>
						<!-- Settings -->
						<my-settings :dashboard="true"></my-settings>
						<!-- Forum Settings -->
						<forums></forums>
						<!-- TeamSpeak Identities -->
						<teamspeak></teamspeak>
					</div>
				</div>
			</div>
		</section>
	</div>
</template>

<script>

	// Subcomponents
	import MyAtcBookings from '../booking/PersonalBookingsComponent.vue';
	import AtcBookings from '../booking/AtcBookingsComponent.vue';
	import AtcLive from '../network/AtcLiveComponent.vue';
	import Settings from './SettingsComponent.vue';
	import Forums from './ForumsComponent.vue';
	import TeamSpeak from './TeamspeakComponent.vue';
    import ProfileComponent from "./ProfileComponent.vue";
    import SurveyComponent from "./SurveyComponent.vue";

	export default {
		components: {
            'survey-component':SurveyComponent,
            'profile-component': ProfileComponent,
			'myatc-bookings': MyAtcBookings,
			'atc-bookings': AtcBookings,
			'atc-live': AtcLive,
			'my-settings': Settings,
			'forums': Forums,
			'teamspeak': TeamSpeak
		},
		data() {
			return {

			}
		},
		mounted() {

		}
	}
</script>
