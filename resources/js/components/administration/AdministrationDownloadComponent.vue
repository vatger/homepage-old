<!-- Membership View Container Component -->
<template>
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Download</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <router-link to="/administration">
                                    Administration
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
                        <button class="btn btn-block btn-primary" v-on:click="download">Download File</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    export default {
    	methods: {
    		download: function () {
                axios.get('/api/administration/download/', {
                		params: {
					      filePath: this.$route.params.filepath
					    }
                	})
                    .then(res => {
                        if(!window.navigator.msSaveOrOpenBlob) {
                            const url = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href= url;
                            link.setAttribute('download', 'FILE.FILE');
                            document.body.appendChild(link);
                            link.click();
                        } else {
                            const url = window.navigator.msSaveOrOpenBlob(new Blob([res.data]), 'FILE.FILE');
                        }
                        this.$router.push('/administration');
                    }).catch(error => {
                    	if(error.response.status == 404) {
                    		console.log(error.response.data);
                    	}
                    });
            },
    	},
    	mounted() {
    		
    	}
    }
</script>