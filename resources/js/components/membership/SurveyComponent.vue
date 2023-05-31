<template>
	<div class="card card-primary card-outline collapsed collapsed-card">
		<div class="card-header">
			<h5 class="card-title">Survey Keys</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
            <table style="table-layout: fixed;" class="table table-bordered over">
                <thead>
                <tr>
                    <th>Name (click)</th>
                    <th>Valid till</th>
                    <th>Key</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="key in keys" v-bind:key="key.id">
                    <td><a v-bind:href="key.url"><b>{{ key.name }}</b></a></td>
                    <td v-if="key.valid_till != null">{{ key.valid_till | moment('utc','DD.MM.YYYY HH:mm') }}z</td>
                    <td v-else>N/A</td>
                    <td><code>{{ key.token }}</code></td>
                </tr>
                </tbody>
            </table>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
                keys: []
			};
		},
        mounted() {
            this.load_data();
        },
        methods: {
			load_data: function () {
                axios.get("/api/membership/my-keys").then((res) => {
                    this.keys = res.data;
                });
			},
		},
	};
</script>
