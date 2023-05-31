<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - AIP Generator</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">Dashboard</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <router-link to="/administration/navigation/aip">AIP Generator</router-link>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="state == 0">
            <div class="container-fluid">
                <h5 class="mb-2">vACC Germany AIP</h5>
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h4 class="card-title">AIP TO SCT/ESE</h4>
                            </div>
                            <div class="card-body">
                                <!-- <p>
                                    Klicke die entsprechenden Schaltflächen, um die SCT oder ESE Version der AIP zu generieren.
                                </p>
                                <button @click="generateSct" class="btn btn-primary btn-block">SCT Version Generieren</button>
                                <button @click="generateEse" class="btn btn-primary btn-block">ESE Version Generieren</button> -->
                                <p>Aufgrund von rechtlichen Bedenken ist dieses Feature seitens NAV-Germany gesperrt und bis zu einer abschließenden Klärung der Rechtsgrundlage nicht nutzbar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-if="state == 1">
            <div class="container-fluid">
                <h5 class="mb-2">vACC Germany AIP</h5>
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h4 class="card-title">Generating AIP</h4>
                            </div>
                            <div class="card-body">
                                Your request is beeing processed.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-if="state == 2">
            <div class="container-fluid">
                <h5 class="mb-2">vACC Germany AIP</h5>
                <div class="row">
                    <div class="col">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h4 class="card-title">Generated AIP</h4>
                            </div>
                            <div class="card-body" v-html="output">
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
                output: ''
            }
        },
        methods: {
            generateSct() {
                this.state = 1;
                // axios.get('/api/administration/navigation/aip/sct')
                //     .then(res => {
                //         this.output = res.data.replace(/\n/g, '<br/>');
                //         this.state = 2;
                //     });
            },
            generateEse() {
                this.state = 1;
                // axios.get('/api/administration/navigation/aip/ese')
                //     .then(res => {
                //         this.output = res.data.replace(/\n/g, '<br/>');
                //         this.state = 2;
                //     });
            }
        },
        mounted() {
        }
    }
</script>