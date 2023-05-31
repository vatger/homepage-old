<template>
    <div class="content-wrapper">
        <!-- Dashboard Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ trans('dashboard.membership') }} - {{ trans('notifications.notifications') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/membership">{{ trans('dashboard.membership') }}</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <router-link to="/membership/notifications">{{ trans('notifications.notifications') }}</router-link>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="card-title">{{ trans('notifications.notifications') }}</h5>
                            </div>
                            <div class="card-body">
                                <ul class="alert alert-danger" v-if="errors">
                                    <li v-for="(value, key) in validationErrors" v-bind:key="key">@{{ value }}</li>
                                </ul>

                                <div class="card" v-if="selectedNotification != null">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ selectedNotification.data.title }}</h5>
                                        <div class="card-tools">
                                            <button type="button" data-card-widget="collapse" class="btn btn-tool">
                                                <i class="fas fa-times" v-on:click="selectedNotification = null"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ selectedNotification.data.message }}</p>
                                        <b>Details:</b>
                                        <p v-html="parse(selectedNotification.data.details)"></p>
                                        <b>Nachricht vom:</b>
                                        <p>{{ selectedNotification.created_at | moment("utc", "DD.MM.YYYY HH:mm") }}z</p>
                                    </div>
                                    <div class="card-footer" v-if="selectedNotification.read_at == null">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="description-block">
                                                    <span class="description-text">
                                                        <button class="btn btn-sm btn-default" v-on:click="markAsRead(selectedNotification.id)">Als Gelesen markieren</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-borderless table-hover table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('notifications.system') }}</th>
                                            <th>{{ trans('notifications.notification') }}</th>
                                            <th>{{ trans('notifications.fromdate') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="notification in notifications" v-bind:key="notification.id" :class="notification.read_at != null ? 'table-success' : 'table-danger'">
                                            <td>{{ notification.data.title }}</td>
                                            <td>{{ notification.data.message }}</td>
                                            <td>{{ notification.created_at | moment("utc", "DD.MM.YYYY HH:mm") }}z</td>
                                            <td>
                                                <button class="btn btn-sm btn-default" v-on:click="selectedNotification = notification" v-if="notification.data.details != null">Details</button>
                                                <button class="btn btn-sm btn-default" v-on:click="markAsRead(notification.id)" v-if="notification.data.details == null && notification.read_at == null">Gelesen</button>
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
                notifications: {},
                selectedNotification: null,
                errors: false,
            };
        },
        methods: {
            getNotifications: function () {
                axios.get("/api/membership/notifications").then((res) => {
                    this.notifications = res.data;
                });
            },
            markAsRead: function (id) {
                axios
                    .put("/api/membership/notifications", {
                        notification: id,
                    })
                    .then((res) => {
                        if (res.data.read_at != null) {
                            // This is the success we wanted
                            this.getNotifications(); // Reload notifications
                            this.selectedNotification = null;
                        }
                    })
                    .catch((error) => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        }
                    });
            },
            parse(mdText){
                return mdText
                    .replace('<', ' &lt;')
                    .replace('>', ' &gt;')
                    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                    .replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>')
                    .replace(/\*\*(.*)\*\*/gim, '<b>$1</b>')
                    .replace(/\*(.*)\*/gim, '<i>$1</i>')
                    .replace(/!\[(.*?)\]\((.*?)\)/gim, "<img alt='$1' src='$2' />")
                    .replace(/\[(.*?)\]\((.*?)\)/gim, "<a href='$2'>$1</a>")
                    .replace(/^\s*\n\*/gm, '<ul>\n*')
                    .replace(/^(\*.+)\s*\n([^\*])/gm, '$1\n</ul>\n\n$2')
                    .replace(/^\*(.+)/gm, '<li>$1</li>')
                    .replace(/^\>(.+)/gm, '<blockquote>$1</blockquote>')
                    .replace(/^\s*\n\d\./gm, '<ol>\n1.')
                    .replace(/^(\d\..+)\s*\n([^\d\.])/gm, '$1\n</ol>\n\n$2')
                    .replace(/^\d\.(.+)/gm, '<li>$1</li>')
                    .replace(/\n$/gim, '<br />')
            },
        },
        computed: {
            validationErrors() {
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            },
        },
        activated() {
            this.getNotifications();
        },
    };
</script>
