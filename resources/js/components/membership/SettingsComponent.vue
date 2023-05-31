<template>
	<div class="card card-primary card-outline collapsed collapsed-card" v-if="dashboard">
		<div class="card-header">
			<h5 class="card-title">{{ trans('membership.settings.header') }}</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
			<h2>{{ trans('membership.settings.header') }}</h2>
			<ul class="list-unstyled alert alert-danger" v-if="errors">
				<li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
			</ul>
			<div class="form-group">
				<label>{{ trans('membership.settings.localpassword') }}:</label>
				<input class="form-control" v-model="backupPassword" type="password" />
			</div>
			<div class="form-group">
				<label>{{ trans('membership.settings.localpassword_confirm') }}:</label>
				<input class="form-control" v-model="backupPasswordConfirm" type="password" />
			</div>
			<div class="form-group">
				<label>{{ trans('membership.settings.language.chose') }}:</label>
				<select class="form-control" v-model="language">
					<option value="de">{{ trans('membership.settings.language.german') }}</option>
					<option value="en">{{ trans('membership.settings.language.english') }}</option>
				</select>
			</div>
			<button class="btn btn-block btn-primary mt-5" v-on:click="save">{{ trans('membership.settings.save') }}</button>
		</div>
	</div>
	<div v-else></div>
</template>

<script>
	export default {
		props: {
			dashboard: false,
		},
		data() {
			return {
				backupPassword: "",
				backupPasswordConfirm: "",
				language: "de",
				errors: false,
			};
		},
		methods: {
			save: function () {
				this.errors = false;
				axios
					.put("/api/membership/account", {
						gdpr: false,
						settings: true,
						backupPassword: this.backupPassword,
						backupPassword_confirmation: this.backupPasswordConfirm,
						language: this.language,
					})
					.then((res) => {
						if (res.data) {
							$("#sysMessages").append(
								'<p class="text-info mb-2">' +
									trans("membership.settings.saved") +
									"</p>"
							);
						}
					})
					.catch((error) => {
						if (error.response.status == 422) {
							this.errors = error.response.data.errors;
						}
					});
			},
		},
		computed: {
			validationErrors() {
				let errors = Object.values(this.errors);
				errors = errors.flat();
				return errors;
			},
		},
	};
</script>
