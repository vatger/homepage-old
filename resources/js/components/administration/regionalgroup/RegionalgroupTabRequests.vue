<template>
    <div>
        <div v-if="selectedRequest != null" class="card">
            <div class="card-header">
                <h5 class="card-title">{{ selectedRequest.topic }} | {{ selectedRequest.account.firstname }}
                    {{ selectedRequest.account.lastname }} ({{ selectedRequest.account.id }})</h5>
                <div class="card-tools">
                    <button class="btn btn-tool" type="button" v-on:click="selectedRequest = null">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li>Antragsteller: {{ selectedRequest.account.firstname }} {{ selectedRequest.account.lastname }}
                        ({{ selectedRequest.account.id }}) <button class="btn" v-on:click="openAccountDetails(selectedRequest.account.id)"><i class="fas fa-info-circle"></i></button>
                        <br>
                        <small>
                            Forenaccount: {{ selectedRequest.account.setting.forum_id != null }}<br>
                            Rating: {{ selectedRequest.account.data.controllerRating.short }} /
                            {{ selectedRequest.account.data.pilotRating }}<br>
                            Zuordnung: {{ selectedRequest.account.data.region_code }} /
                            {{ selectedRequest.account.data.division_code }} /
                            {{ selectedRequest.account.data.subdivision_code }}
                        </small>
                    </li>
                    <li>Art der Anfrage: {{ selectedRequest.topic }}</li>
                    <li>Anfragetyp: {{ selectedRequest.type }}</li>
                    <li>Grund: {{ selectedRequest.reason }}</li>
                </ul>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <div class="description-block">
                            <b>Vorlagenauswahl:</b>
                            <select class="form-control" v-model="selectedTemplate">
                                <option v-for="t in templates" :value="t"
                                        v-bind:key="t.id">{{ t.name }} [{{ t.order }}]
                                </option>
                            </select>
                            <button class="btn btn-primary mt-3" v-if="selectedTemplate != null" v-on:click="selectTemplate">Einfügen</button>
                        </div>
                        <div class="description-block">
                            <b>Benachrichtigung/Details:</b>
                            <textarea v-model="notificationDetails" class="form-control"></textarea>
                        </div>
                        <div class="description-block">
                            <span class="description-text">
                                <button class="btn btn-sm btn-success"
                                        v-on:click="acceptRequest()">Antrag Stattgeben</button>
                                <button class="btn btn-sm btn-danger" v-on:click="denyRequest()">Antrag Ablehnen</button>
                            </span>
                        </div>
                        <div class="description-block" v-if="notificationDetails.length > 5">
                            <b>Vorschau:</b>
                            <div v-html="selectedPreview"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table id="requestsTable" class="table table-borderless table-responsive-sm">
            <thead>
            <tr>
                <th>Von</th>
                <th>Betreff</th>
                <th>Eingereicht Am</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="r in regionalgroup.requests" v-bind:key="r.id">
                    <td>
                        {{ r.account.firstname }} {{ r.account.lastname }} ({{ r.account.id }})
                        <button class="btn" v-on:click="openAccountDetails(r.account.id)"><i class="fas fa-info-circle"></i></button>
                    </td>
                    <td>{{ r.topic }} / {{r.type}}</td>
                    <td>{{ r.created_at | moment("utc","YYYY-MM-DD HH:mm") }}</td>
                    <td>
                        <button class="btn btn-sm btn-default" v-on:click="selectRequest(r)">Ansehen</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "RegionalgroupTabRequests",
    props: {
        regionalgroup: Object,
    },
    data() {
        return {
            selectedRequest: null,
            selectedTemplate: null,
            notificationDetails: '',
        };
    },
    methods: {
        selectRequest(r){
            this.selectedRequest = r;
            this.notificationDetails = '';
        },
        acceptRequest() {
            axios
                .put(
                    "/api/administration/regionalgroups/" +
                    this.regionalgroup.id +
                    "/requests/" +
                    this.selectedRequest.id + "/accept",
                    {notificationDetails: this.notificationDetails.replace('$name', this.selectedRequest.account.firstname)}
                )
                .then((res) => {
                    this.requestsNotificationDetails = "";
                    this.regionalgroup.requests = _.without(this.regionalgroup.requests, this.selectedRequest);
                    this.$emit('update:regionalgroup', this.regionalgroup);
                    this.selectedRequest = null;
                    this.selectedTemplate = null;
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.$emit('update:accessDenied', true);
                    }
                });
        },
        denyRequest() {
            axios.put(
                "/api/administration/regionalgroups/" + this.regionalgroup.id + "/requests/"
                + this.selectedRequest.id + "/deny",
                { notificationDetails: this.notificationDetails.replace('$name', this.selectedRequest.account.firstname) }
                )
                .then((res) => {
                    this.requestsNotificationDetails = "";
                    this.regionalgroup.requests = _.without(this.regionalgroup.requests, this.selectedRequest);
                    this.$emit('update:regionalgroup', this.regionalgroup);
                    this.selectedRequest = null;
                    this.selectedTemplate = null;
                })
                .catch((error) => {
                    if (error.response.status == 403) this.$emit('update:accessDenied', true);
                });
        },
        selectTemplate(){
            this.notificationDetails += this.selectedTemplate.message.replace('$name', this.selectedRequest.account.firstname);
            this.selectedTemplate = null;
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
        openAccountDetails (id) {
            window.open('/administration/membership/' + id, 'Userinfo: ' + id, 'menubar=no,resizable=yes,scrollbars=yes')
        },
    },
    computed:{
        selectedPreview(){
            return this.parse(this.notificationDetails);
        },
        templates(){
            const templates = [... this.regionalgroup.templates];
            return templates.sort((first,second) => first.order - second.order);
        },
    }
}
</script>

<style scoped>

</style>
