<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - Groundlayout Generator</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">Dashboard</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <router-link to="/administration/navigation/glg">Groundlayout Generator</router-link>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="state == 0">
            <div class="container-fluid">
                <h5 class="mb-2">vACC Germany GlG</h5>
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h4 class="card-title">Build Your Own Groundlayouts</h4>
                            </div>
                            <div class="card-body">
                                <p>
                                    With the form below upload either an .kmz archive or a .kml file that covers your area.
                                </p>
                                <dl class="row">
                                    <dt class="col-md-3">Groundlayouts</dt>
                                    <dd class="col-md-9">
                                        Folderstructure:
                                        <ul>
                                            <li>Groundlayouts</li>
                                            <ul>
                                                <li>Name of the Layout (one or more of)</li>
                                                <ul>
                                                    <li>Perimiter</li>
                                                    <li>Runways</li>
                                                    <li>Taxiways</li>
                                                    <li>Aprons</li>
                                                    <li>Buildings</li>
                                                    <li>Markings</li>
                                                    <li>Labels</li>
                                                </ul>
                                            </ul>
                                        </ul>
                                    </dd>
                                    <dt class="col-md-3">Prerequisites</dt>
                                    <dd class="col-md-9">
                                        "Root Folder / The Layout Name" must contain the following description:
                                        <pre>COLOR_AIRPORT_PERIMITER #1e1e1e,<br>COLOR_AIRPORT_APRON #333333,<br>COLOR_AIRPORT_BUILDING #004d7a,<br>COLOR_AIRPORT_TAXIWAY #333333,<br>COLOR_AIRPORT_RUNWAY #333333,<br>COLOR_AIRPORT_MARKING_BLUE #007cc4,<br>COLOR_AIRPORT_MARKING_ORANGE #c46200,<br>COLOR_AIRPORT_MARKING #ddda00,<br>COLOR_AIRPORT_LABELS #cccccc,<br>COLOR_AIRPORT_CENTERLINE #cccccc,<br>COLOR_AIRPORT_CENTERLINE_EXTENDED #cccccc,</pre>
                                        Obviously the color codes can be adjusted to your personal likeing!<br><br>
                                        Perimiter / Runways / Taxiways / Aprons must be filled Polygons.<br>
                                        Buildings and Markings shall be closed paths.<br>
                                        Labels are Placemarks.
                                    </dd>
                                </dl>
                                <hr class="my-3">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" ref="file" id="gekml" v-on:change="handleFileUpload">
                                        <label class="custom-file-label" for="gekml" v-if="gekml == null">Select .kml/.kmz Archive</label>
                                        <label class="custom-file-label" for="gekml" v-else>{{ gekml.name }}</label>
                                    </div>
                                </div>
                                <button @click="submitFile" class="btn btn-primary btn-block">Start Generation</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-if="state == 1">
            <div class="container-fluid">
                <h5 class="mb-2">vACC Germany GlG</h5>
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h4 class="card-title">Generating Your Groundlayouts</h4>
                            </div>
                            <div class="card-body">
                                Your submitted file is beeing processed.<br/>
                                You will be notified when the process is completed.
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
        data () {
            return {
                state: 0,
                gekml: null,
                output: ''
            }
        },
        methods: {
            handleFileUpload: function () {
                this.gekml = this.$refs.file.files[0];
            },
            submitFile: function () {
                let formData = new FormData();
                formData.append('gekml', this.gekml);
                axios.post(
                    '/api/administration/navigation/sct/glg',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(res => {
                    this.state = 1;
                });
            },
            reset: function () {
                this.state = 0;
                this.gekml = null,
                this.output = '';
            }
        },
        mounted() {
            this.state = 0;
            this.gekml = null;
            this.output = '';
        }
    }
</script>