<template>
	<div class="content-wrapper">

		<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('navigation.controllers.controllers') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item active">
                                <router-link to="/controllers">
                                    {{ trans('navigation.controllers.controllers') }}
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
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">ATD Solo-Endorsements</h5>
							</div>
							<div class="card-body">
								<table class="table table-hover table-bordered table-responsive-sm">
									<thead>
										<tr>
											<th>Controller</th>
											<th>Station</th>
											<th>Expires</th>
                                            <th v-if="solos[0].candidate">Extensions</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="solo in solos" v-bind:key="solo.id">
											<td v-if="solo.candidate">{{ solo.candidate.firstname }} {{ solo.candidate.lastname }} ({{ solo.candidate_id }})</td>
                                            <td v-else>{{ solo.candidate_id }}</td>
											<td>{{ solo.station.ident }}</td>
											<td>{{ displayDayFromDate(solo.ends_at) }}</td>
                                            <td v-if="solo.candidate">{{solo.extensions}}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</template>

<script>
	import moment from 'moment';

	export default {
		data() {
			return {
				solos: {}
			}
		},
		methods: {
			grabSolos: function () {
				axios.get('/api/atd/solos')
					.then(res => {
						this.solos = res.data;
					})
			},
			displayDayFromDate(date) {
            	return moment.utc(date).format('DD.MM.YYYY');
            },
		},
		mounted() {
			this.grabSolos();
		}
	}
</script>
