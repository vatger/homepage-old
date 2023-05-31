<template>
	<div class="content-wrapper">

		<!-- Dashboard Header -->
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>{{ trans('dashboard.membership') }} - {{ trans('regionalgroup.regionalgroups') }} - {{ trans('regionalgroup.request.requests') }}</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/membership">{{ trans('dashboard.membership') }}</router-link>
							</li>
							<li class="breadcrumb-item">
								<router-link to="/membership/regionalgroups">{{ trans('regionalgroup.regionalgroups') }}</router-link>
							</li>
							<li class="breadcrumb-item active">
								<router-link to="/membership/regionalgrouprequests">{{ trans('regionalgroup.request.requests') }}</router-link>
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
					<div class="col-12 col-md-6">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">{{ trans('regionalgroup.request.currentrequests') }}</h5>
							</div>
							<div class="card-body">
								<table class="table table-responsive-sm table-borderless table-hover">
									<thead>
										<tr>
											<th>{{ trans('regionalgroup.request.toregionalgroup') }}</th>
											<th>{{ trans('regionalgroup.request.topic') }}</th>
											<th>{{ trans('regionalgroup.request.type') }}</th>
											<th>{{ trans('regionalgroup.request.date') }}</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="r in requests" v-bind:key="r.id">
											<td>{{ r.regionalgroup.name }}</td>
											<td v-if="r.topic == 'join'">{{ trans('regionalgroup.request.join') }}</td>
											<td v-if="r.topic == 'change'">{{ trans('regionalgroup.request.change') }}</td>
											<td v-if="r.topic == 'leave'">{{ trans('regionalgroup.request.leave') }}</td>
											<!-- <td>{{ r.type }} - {{ r.as }}</td> -->
											<td>{{ r.type }}</td>
											<td>{{ convertDate(r.created_at) }}</td>
											<td><button class="btn btn-sm btn-danger" v-on:click="revokeRequest(r)">{{ trans('regionalgroup.request.cancel') }}</button></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">{{ trans('regionalgroup.request.newrequest') }}</h5>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>{{ trans('regionalgroup.request.toregionalgroup') }}</label>
									<select class="form-control" v-model="newRequest.regionalgroup">
										<option value="2">RG Berlin</option>
										<option value="1">RG Bremen</option>
										<option value="3">RG Düsseldorf</option>
										<option value="4">RG Frankfurt</option>
										<option value="5">RG München</option>
									</select>
								</div>
								<div class="form-group">
									<label>{{ trans('regionalgroup.request.topic') }}</label>
									<select class="form-control" v-model="newRequest.topic">
										<option value="join">{{ trans('regionalgroup.request.join') }}</option>
										<option value="change">{{ trans('regionalgroup.request.change') }}</option>
										<option value="leave">{{ trans('regionalgroup.request.leave') }}</option>
									</select>
								</div>

                                <!--
								<div class="form-group" v-if="newRequest.topic == 'change'">
									<label>{{ trans('regionalgroup.request.changetoregionalgroup') }}</label>
									<select class="form-control" v-model="newRequest.destination">
										<option value="1">RG Berlin</option>
										<option value="2">RG Bremen</option>
										<option value="3">RG Düsseldorf</option>
										<option value="4">RG Frankfurt</option>
										<option value="5">RG München</option>
									</select>
								</div>
                                -->
								<div class="form-group" v-if="newRequest.topic != 'leave'">
									<label>{{ trans('regionalgroup.request.type') }}</label>
									<select class="form-control" v-model="newRequest.type">
										<option value="member">{{ trans('regionalgroup.fullmember') }}</option>
										<option value="guest">{{ trans('regionalgroup.guestmember') }}</option>
									</select>
								</div>
								<!-- <div class="form-group">
									<label>Art der Mitgliedschaft[to remove]</label>
									<select class="form-control" v-model="newRequest.as">
										<option value="controller">Lotse[to remove]</option>
										<option value="pilot">Pilot[to remove]</option>
										<option value="both">Beides[to remove]</option>
									</select>
								</div> -->
								<div class="form-group" v-if="newRequest.topic != 'leave'">
									<label>{{ trans('regionalgroup.request.reason') }}</label>
									<textarea class="form-control" v-model="newRequest.reason" minlength="25" maxlength="150"></textarea>
								</div>
                                <ul class="list-unstyled alert alert-danger" v-if="error != null">
                                    <li>{{ error }}</li>
                                </ul>
								<div class="form-group">
									<button class="btn btn-default btn-block" v-on:click="sendNewRequest"
                                            :disabled="!(newRequest.reason != null && newRequest.reason.length >= 25) && newRequest.topic != 'leave'">
                                        {{ trans('regionalgroup.request.send') }}
                                    </button>
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
	import moment from 'moment'

	export default {
		data() {
			return {
				requests: {},
                newRequest: {},
                error: null
			}
		},
		methods: {
			loadRequests() {
				axios.get('/api/regionalgroup/requests')
					.then(res => {
						this.requests = res.data;
					});
			},
			sendNewRequest() {
                this.error = null;
                if(this.newRequest.topic == 'leave'){
                    this.newRequest.reason = 'No description provided! This is a leave Request!';
                    this.newRequest.type = 'none'
                }
				axios.post('/api/regionalgroup/requests', {
					newRequest: this.newRequest
				}).then(res => {
                    this.requests.push(res.data);
                    if(this.newRequest.topic == 'leave'){
                        this.newRequest.reason = '';
                        this.newRequest.type = ''
                    }
				}).catch(error => {
					if (error.response.status == 422){
                        this.error = error.response.data.message;
                    }
                    if(this.newRequest.topic == 'leave'){
                        this.newRequest.reason = '';
                        this.newRequest.type = ''
                    }
                });
			},
			revokeRequest(request) {
				axios.delete('/api/regionalgroup/requests/'+request.id)
					.then(res => {
                        this.requests = this.requests.filter(req => req.id != res.data);
					});
			},
			convertDate: function (date) {
                return moment.utc(date).format('DD.MM.YYYY HH:mm');
            }
		},
		activated() {
			this.loadRequests();
		}
	}
</script>
