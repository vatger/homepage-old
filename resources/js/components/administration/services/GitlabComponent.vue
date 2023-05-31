<template>
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Services - Gitlab</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <router-link to="services/gitlab">
                                    Services - Gitlab
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
                                <h5 class="card-title">Gitlab Account</h5>
                            </div>
                            <div class="card-body">
                                <div v-if="account_settings">
                                    <div v-if="account_settings.gitlab_id">
                                        du hast einen gitlab account:<br><br>
                                        - email ist deine Vatsim email<br>
                                        - benutzername ist deine Vatsim id<br>
                                        - wenn du deinen account grade erst erstellt hast bekommst du eine email, sonst kannst du dein passwort zurücksetzen<br>
                                    </div>
                                    <div v-else>
                                        du aktuell hast keinen gitlab account<br><br>
                                        <button v-on:click="createaccount()">create new account</button>
                                    </div>
                                </div>
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
            account_settings: null
        }
    },
    methods: {
        createaccount() {
            axios.post('/api/administration/gitlab/')
                .then(res => {
                  this.getsettings();
                })
        },
        getsettings() {
            axios.get('/api/administration/gitlab/')
                .then(res => {
                    this.account_settings = res.data;
                });
        },
    },
    activated() {
        this.getsettings();
    }
}
</script>

