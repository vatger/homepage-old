<template>
	<div class="content-wrapper">

		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>Bildverwaltung - Uploader</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<router-link to="/administration">
									Administration
								</router-link>
							</li>
							<li class="breadcrumb-item active">
								<router-link to="/images/uploader">
									Bildverwaltung - Uploader
								</router-link>
							</li>
						</ol>
					</div>
				</div>
			</div>
		</section>

		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h3 class="card-title">Upload New Image</h3>
							</div>
							<div class="card-body">
								<ul class="list-unstyled alert alert-danger" v-if="errors">
									<li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
								</ul>
								<div class="form-group">
									<div class="custom-file">
										<input type="file" class="custom-file-input" ref="file" id="imgUploader" v-on:change="handleFileUpload">
										<label class="custom-file-label" for="imgUploader" v-if="imgFile == null">Select An Image To Upload</label>
										<label class="custom-file-label" for="imgUploader" v-else>{{ imgFile.name }}</label>
									</div>
								</div>
								<div class="form-group">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="imgLicense" v-model="imgLicense">
										<label class="form-check-label">{{ trans('upload.images.license') }}</label>
									</div>
								</div>
								<button @click="submitFile" class="btn btn-primary btn-block">Start Upload</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row py-3" v-if="uploadedImage">
					<div class="col-12">
						<h3>Image Saved</h3>
						<ul class="list-unstyled alert alert-success">
							<li>Name: {{ uploadedImage.name }}</li>
							<li>Link: <a :href="uploadedImage.href">{{ uploadedImage.href }}</a></li>
						</ul>
					</div>
				</div>
				<div class="row py-3">
					<div class="col-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h3 class="card-title">Your Images</h3>
							</div>
							<div class="card-body">
								<table class="table table-responsive-sm table-sm table-bordered">
									<thead>
										<tr>
											<th>Name</th>
											<th>Link</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(image, index) in uploadedImages" v-bind:key="index">
											<td>{{ image.name }}</td>
											<td>{{ image.href }}</td>
											<td>
												<a class="btn btn-sm btn-default" :href="image.href" target="_blank">View</a>
												<button class="btn btn-sm btn-danger" v-on:click="deleteImage(image)">Delete</button>
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
	export default {
		data() {
			return {
				uploadedImages: [],
				imgFile: null,
				imgLicense: false,
				uploadedImage: null,
				errors: false
			}
		},
		methods: {
			loadUploadedImages() {
				axios.get('/api/administration/myimages')
					.then(res => {
						this.uploadedImages = res.data;
					});
			},
			handleFileUpload: function () {
				this.imgFile = this.$refs.file.files[0];
			},
			submitFile: function () {
				this.errors = false;
				let formData = new FormData();
				formData.append('imgFile', this.imgFile);
				formData.append('imgLicense', this.imgLicense);
				axios.post(
					'/api/administration/images/upload',
					formData,
					{
						headers: {
							'Content-Type': 'multipart/form-data'
						}
					}
				).then(res => {
					this.imgFile = null;
					this.uploadedImage = res.data.image;
					this.uploadedImages.push(this.uploadedImage);
				}).catch(error => {
					if(error.response.status == 413) {
						this.errors = {
							'fileSize': "Image is too large. Max payload is 5MB"
						}
					}
					if(error.response.status == 422) {
						this.errors = error.response.data.errors;
					}
				});
			},
			deleteImage(image) {
				axios.delete('/api/administration/images/'+image.id)
					.then(res => {
						this.loadUploadedImages();
					})
			},
		},
		computed: {
			validationErrors(){
				let errors = Object.values(this.errors);
				errors = errors.flat();
				return errors;
			}
		},
		activated() {
			this.loadUploadedImages();
		}
	}
</script>