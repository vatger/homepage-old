<template>
	<div class="card card-primary card-outline collapsed collapsed-card" v-if="!completed">
		<div class="card-header">
			<h5 class="card-title">{{ trans('forum.access') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
			<div class="form-group">
				<label>{{ trans('forum.password') }}</label>
				<input type="password" class="form-control" v-model="na.pwd">
			</div>
			<div class="form-group">
				<label>{{ trans('forum.passwordconfirm') }}</label>
				<input type="password" class="form-control" v-model="na.pwd_confirmation">
			</div>
			<div class="form-group">
				<button class="btn btn-block btn-default" v-on:click="createForumAccount">{{ trans('forum.makeaccess') }}</button>
			</div>
		</div>
	</div>
	<div class="card card-primary card-outline collapsed collapsed-card" v-else>
		<div class="card-header">
			<h5 class="card-title">{{ trans('forum.access') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
			<div class="callout callout-info">
		        <h5>{{ trans('forum.access') }}</h5>
		        <p>{{ trans('forum.accessalreadyexits') }}</p>
		        <p>{{ trans('forum.accesswithcredentials', {username: username}) }}</p>
		    </div>
		</div>
	</div>
</template>

<script>
	export default
	{
		data() {
			return {
				completed: false,
				username: '',
				na: {
					pwd: '',
					pwd_confirmation: ''
				}
			}
		},
		methods: {
			createForumAccount() {
				axios.post('/api/membership/forum', {
					password: this.na.pwd,
					password_confirmation: this.na.pwd_confirmation
				}).then(res => {
					this.completed = res.data;
				})
			}
		},
		mounted() {
			axios.get('/api/membership/forum')
				.then(res => {
					this.completed = res.data;
				}).finally(() => {
					if(this.completed) {
						axios.get('/api/membership/forum/username')
							.then(res => {
								this.username = res.data;
							});
					}
				});
		}
	}
</script>
