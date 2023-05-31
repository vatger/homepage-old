<template>
    <div class="container-fluid">
        <h5 class="mb-2">{{ trans('membership.setup.welcome') }}</h5>
        <div class="row">
            <div class="col">
                <div class="callout callout-danger" v-html="trans('membership.setup.text')"></div>
            </div>
        </div>
        <div class="row" v-if="errors">
            <div class="col">
                <ul class="alert alert-danger">
                    <li v-for="(value, key) in validationErrors" v-bind:key="key">@{{ value }}</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card card-primary card-outline card-outline-tabs m-0">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="setup-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="setup-tabs-gdpr-tab" data-toggle="pill" href="#setup-tabs-gdpr" role="tab" aria-controls="setup-tabs-gdpr" aria-selected="true">Datenschutzerklärung / data protection rules</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="setup-tabs-settings-tab" data-toggle="pill" href="#setup-tabs-settings" role="tab" aria-controls="setup-tabs-settings" aria-selected="false">{{ trans('membership.setup.settings') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="setup-tabs-finish-tab" data-toggle="pill" href="#setup-tabs-finish" role="tab" aria-controls="setup-tabs-finish" aria-selected="false">{{ trans('membership.setup.finish.button') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="setup-tabsContent">
                            <div class="tab-pane fade active show" id="setup-tabs-gdpr" role="tabpanel" aria-labelledby="setup-tabs-gdpr-tab">
                                <h2>Datenschutzerklärung / data protection rules</h2>
                                <div class="form-group">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input class="custom-control-input" id="dsgvoSwitch" type="checkbox" v-model="gdpr" />
                                        <label class="custom-control-label" for="dsgvoSwitch">{{ trans('membership.setup.gdpraccept') }}</label>
                                    </div>
                                </div>
                                <hr class="my-5" />
                                <p v-html="gdprContent"></p>
                            </div>
                            <div class="tab-pane fade" id="setup-tabs-settings" role="tabpanel" aria-labelledby="setup-tabs-settings-tab">
                                <h2>Einstellungen</h2>
                                <div class="form-group">
                                    <label>{{ trans('membership.setup.localpassword') }}</label>
                                    <input class="form-control" v-model="backupPassword" type="password" />
                                </div>
                                <div class="form-group">
                                    <label>{{ trans('membership.setup.confirmlocalpassword') }}</label>
                                    <input class="form-control" v-model="backupPasswordConfirm" type="password" />
                                </div>
                                <div class="alert alert-secondary" role="alert" v-if="backupPasswordConfirm !='' && backupPassword!=backupPasswordConfirm">{{ trans('membership.setup.passwordsnotmatch') }}</div>
                                <div class="form-group">
                                    <label>{{ trans('membership.setup.preferredlanguage') }}</label>
                                    <select class="form-control" v-model="language">
                                        <option value="de" selected>Deutsch</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="setup-tabs-finish" role="tabpanel" aria-labelledby="setup-tabs-finish-tab">
                                <h2>{{ trans('membership.setup.finishregistration') }}</h2>

                                <div class="alert alert-secondary" role="alert" v-if="!gdpr">{{ trans('membership.setup.nogdpr') }}</div>
                                <div class="alert alert-secondary" role="alert" v-if="backupPasswordConfirm ==''">{{ trans('membership.setup.nopassword') }}</div>
                                <div class="alert alert-secondary" role="alert" v-if="backupPasswordConfirm !='' && backupPassword!=backupPasswordConfirm">{{ trans('membership.setup.passwordsnotmatch') }}</div>
                                <div v-if="gdpr && backupPassword != '' && backupPassword === backupPasswordConfirm">
                                    <div v-html="trans('membership.setup.finish.text')"></div>
                                    <button class="btn btn-block btn-primary mt-5" v-on:click="finish">{{ trans('membership.setup.finish.button') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                gdprContent: "",
                gdpr: false,
                backupPassword: "",
                backupPasswordConfirm: "",
                language: "de",
                errors: false,
            };
        },
        methods: {
            finish: function () {
                axios
                    .put("/api/membership/account", {
                        gdpr: this.gdpr,
                        backupPassword: this.backupPassword,
                        backupPassword_confirmation: this.backupPasswordConfirm,
                        language: this.language,
                    })
                    .then((res) => {
                        if (res.data) {
                            window.location = "/membership";
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
        mounted() {
            axios.get("/api/static/legal/gdpr").then((res) => {
                this.gdprContent = res.data;
            });
        },
    };
</script>
