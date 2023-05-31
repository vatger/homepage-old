<template>
	<div class="content-wrapper">

		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>Bildverwaltung - Manager</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/administration">
									Administration
								</router-link>
							</li>
							<li class="breadcrumb-item active">
								<router-link to="/images/manager">
									Bildverwaltung - Manager
								</router-link>
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>

		<section class="content">
			<div class="container-fluid">
				<div class="card card-primary card-outline" v-if="image != null">
					<div class="card-header">
						<h5 class="card-title">Image Viewer</h5>
						<div class="card-tools">
							<button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="image = null">
								<i class="fas fa-times"></i>
							</button>
						</div>
					</div>
					<div class="card-body">
						<p v-if="image === false">
							Bild wurde nicht gefunden. Aus Datenbank entfernt!
						</p>
						<img :src="'data:image/jpeg;base64, '+image" class="img-fluid" v-else />
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h5 class="card-title">Uploaded Images</h5>
							</div>
							<div class="card-body">
								<table class="table table-responsive-sm table-bordered table-sm">
									<thead>
										<tr>
											<th>#ID</th>
											<th>Uploader</th>
											<th>Approved</th>
											<th>Path<br/>(Relative from storage/app)</th>
											<th>External Link</th>
											<th>Uploaded At</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(img, index) in images" v-bind:key="index">
											<td>{{ img.id }}</td>
											<td>{{ img.account.id }}<br/>{{ img.account.firstname }} {{ img.account.lastname }}</td>
											<td>{{ img.approved ? 'Yes' : 'No' }}</td>
											<td>{{ img.path }}</td>
											<td>{{ img.href }}</td>
											<td>{{ img.created_at | moment("utc", "DD.MM.YYYY HH:mm") }}</td>
											<td>
												<button class="btn btn-sm btn-primary" v-on:click="viewImage(img)">View</button>
												<button class="btn btn-sm btn-default" v-if="!img.approved" v-on:click="approveImage(img)">Approve</button>
												<button class="btn btn-sm btn-warning" v-if="img.approved" v-on:click="denyImage(img)">Deny</button>
												<button class="btn btn-sm btn-danger" v-on:click="deleteImage(img)">Delete</button>
											</td>
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
	import moment from 'moment'

	export default {
		data() {
			return {
				images: [],
				image: null
			}
		},
		methods: {
			approveImage(image) {
				axios.put('/api/administration/images/'+image.id+'/approve')
					.then(res => {
						this.loadImages();
					})
			},
			denyImage(image) {
				axios.put('/api/administration/images/'+image.id+'/deny')
					.then(res => {
						this.loadImages();
					})
			},
			viewImage(image) {
				axios.get('/api/administration/images/'+image.id)
					.then(res => {
						this.image = res.data;

						if(this.image === false) {
							this.loadImages();
						}
					});
			},
			deleteImage(image) {
				axios.delete('/api/administration/images/'+image.id)
					.then(res => {
						this.loadImages();
					})
			},
			loadImages() {
				axios.get('/api/administration/images')
					.then(res => {
						this.images = res.data;
					})
			}
		},
		activated() {
			this.loadImages();
		}
	}
</script>