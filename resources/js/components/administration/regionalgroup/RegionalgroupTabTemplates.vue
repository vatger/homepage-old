<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Vorlagen verwalten</h5>
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
                        </div>
                        <div v-if="selectedTemplate != null">
                            <div class="description-block">
                                <b>Name:</b>
                                <input v-model.trim="selectedTemplate.name" class="form-control"></input>
                            </div>
                            <div class="description-block">
                                <b>Reinfolge:</b>
                                <input v-model.number="selectedTemplate.order" class="form-control"></input>
                            </div>
                            <div class="description-block">
                                <b>Vorlagentext:</b>
                                <textarea v-model.trim="selectedTemplate.message" class="form-control"></textarea>
                                <small>Markdown unterstützt</small>
                            </div>

                            <div class="description-block">
                                <span class="description-text">
                                    <button class="btn btn-sm btn-success" v-on:click="editTemplate">Vorlage bearbeiten</button>
                                    <button class="btn btn-sm btn-danger" v-on:click="deleteTemplate">Vorlage löschen</button>
                                </span>
                            </div>
                            <div class="description-block">
                                <b>Vorschau:</b>
                                <small>{{ selectedTemplate.name }}</small>
                                <div class="card-outline" v-html="selectedPreview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-block btn-default" v-if="!newTemplate" v-on:click="newTemplate = true">Neue Vorlage</button>
        <div class="card" v-else>
            <div class="card-header">
                <h5 class="card-title">Neue Vorlage anlegen</h5>
                <div class="card-tools" >
                    <button class="btn btn-tool" type="button" v-on:click="newTemplate = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <div class="description-block">
                            <b>Name:</b>
                            <input v-model.trim="newName" class="form-control"></input>
                        </div>
                        <div class="description-block">
                            <b>Reinfolge:</b>
                            <input v-model.number="newOrder" class="form-control"></input>
                        </div>
                        <div class="description-block">
                            <b>Vorlagentext:</b>
                            <textarea v-model.trim="newText" class="form-control"></textarea>
                            <small>Markdown unterstützt</small>
                        </div>

                        <div class="description-block">
                            <span class="description-text">
                                <button class="btn btn-sm btn-success" v-on:click="createTemplate">Vorlage anlegen</button>
                            </span>
                        </div>
                        <div class="description-block" v-if="newText.length > 3">
                            <b>Vorschau:</b>
                            <small>{{ newName }}</small>
                            <div class="card card-primary card-outline" v-html="newPreview"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RegionalgroupTabTemplates",
    props: {
        regionalgroup: Object,
    },
    data() {
        return {
            newTemplate: false,
            newName: '',
            newText: '',
            newOrder: 0,
            selectedTemplate: null,
        };
    },
    methods: {
        createTemplate() {
            if (this.newName.length < 3) return;
            axios.post("/api/administration/regionalgroups/" +
                this.regionalgroup.id + "/template/", {
                    name: this.newName,
                    message: this.newText,
                    order: this.newOrder > 0 ? this.newOrder : 0,
                })
                .then(res => {
                    this.newTemplate = false;
                    this.regionalgroup.templates.push(res.data);
                    this.$emit('update:regionalgroup', this.regionalgroup);
                }).catch(error => {
                    if(error.response.status == 403) this.$emit('update:accessDenied', true);
                    if(error.response.status == 422) this.$emit('update:errors', error.response.data.errors);
                })
        },
        editTemplate() {
            axios.put("/api/administration/regionalgroups/" + this.regionalgroup.id + "/template/" + this.selectedTemplate.id,{
                    name: this.selectedTemplate.name,
                    message: this.selectedTemplate.message,
                    order: this.selectedTemplate.order > 0 ?  this.selectedTemplate.order : 0,
                })
                .then(res => {
                    this.regionalgroup.templates = this.regionalgroup.templates.filter(t => t.id != this.selectedTemplate.id);
                    this.regionalgroup.templates.push(res.data);
                    this.$emit('update:regionalgroup', this.regionalgroup);
                }).catch(error => {
                    if(error.response.status == 403) this.$emit('update:accessDenied', true);
                    if(error.response.status == 422) this.$emit('update:errors', error.response.data.errors);
                })
        },
        deleteTemplate() {
            axios.delete("/api/administration/regionalgroups/" + this.regionalgroup.id + "/template/" + this.selectedTemplate.id)
                .then(res => {
                    this.regionalgroup.templates = this.regionalgroup.templates.filter(t => t.id != this.selectedTemplate.id);
                    this.$emit('update:regionalgroup', this.regionalgroup);
                    this.selectedTemplate = null;
                }).catch(error => {
                    if(error.response.status == 403) this.$emit('update:accessDenied', true);
                    if(error.response.status == 422) this.$emit('update:errors', error.response.data.errors);
                })
        },
        parse(mdText){
            return mdText
                .replace('<', ' &lt;')
                .replace('>', ' &gt;')
                .replace('$name', '<i>Max Mustermann</i>')
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
    computed:{
        newPreview(){
            return this.parse(this.newText);
        },
        selectedPreview(){
            return this.parse(this.selectedTemplate.message);
        },
        templates(){
            const templates = [... this.regionalgroup.templates];
            return templates.sort((first,second) => first.order - second.order);
        },
    },
}
</script>
