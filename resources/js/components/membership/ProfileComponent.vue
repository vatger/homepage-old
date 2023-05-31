<template>
	<div class="card card-primary card-outline collapsed collapsed-card">
		<div class="card-header">
			<h5 class="card-title">Profile</h5>
			<div class="card-tools">
				<button type="button" data-card-widget="collapse" class="btn btn-tool">
					<i class="fas fa-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body" style="display: none;">
            <div class="form-group">
                <label>CID:</label>
                <input class="form-control" type="text" disabled v-model="cid"/>
            </div>
			<div class="form-group">
				<label>Email (homepage):</label>
				<input class="form-control" type="text" disabled v-model="hp_email"/>
			</div>
            <div class="form-group">
                <label>Email (forum):</label>
                <input class="form-control" type="text" disabled v-model="forum_email"/>
            </div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
                cid: '-/-',
                hp_email: '-/-',
                forum_email: '-/-',
			};
		},
        mounted() {
            this.load_data();
        },
        methods: {
			load_data: function () {
				axios
					.get("/api/membership/my-profile")
					.then((res) => {
						if (res.data) {
                            this.cid = res.data.cid;
                            this.hp_email = res.data.hp_email;
                            this.forum_email = res.data.forum_email;
						}
					})
			},
		},
	};
</script>
