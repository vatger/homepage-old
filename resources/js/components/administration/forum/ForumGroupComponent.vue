<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Forengruppen</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                Forengruppen
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="accessDenied">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-danger">
                            <div class="card-header">
                                <h3 class="card-title">Zugang Verweigert</h3>
                            </div>
                            <div class="card-body">
                                Du hast nicht die erforderlichen Rechte, um diese Quelle aufzurufen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-else>
        	<div class="card card-primary card-outline" v-if="createMode">
        		<div class="card-header">
                    <h5 class="card-title">Neue Forengruppe Anlegen</h5>
                    <div class="card-tools">
                        <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="createMode = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                	<div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="newGroup.name">
                    </div>
                    <div class="form-group">
                        <label for="ident">ID der Gruppe im Forum</label>
                        <input type="text" class="form-control" id="ident" v-model="newGroup.id">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="createGroup">Speichern</button>
                    </div>
                </div>
        	</div>
    		<div class="card card-primary card-outline">
    			<div class="card-header">
    				<h5 class="card-title">Gruppenverwaltung</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-borderless table-hover table-responsive-sm" id="fgroupTable" data-order='[[1, "asc"]]' data-page-length='50'>
    					<thead>
    						<tr>
    							<th>ID</th>
    							<th>Name</th>
    							<th>Foren ID</th>
    							<th></th>
    						</tr>
    					</thead>
    					<tbody>
    						<tr v-for="(group, index) in forumgroups" v-bind:key="index">
    							<td>{{ group.id }}</td>
    							<td>{{ group.name }}</td>
    							<td>{{ group.forum_id }}</td>
    							<td><button class="btn btn-danger btn-sm" v-on:click="removeGroup(group.id)">Löschen</button></td>
    						</tr>
    					</tbody>
    					<tfoot>
                            <tr>
                                <td colspan="4"><button class="btn btn-default btn-block" v-on:click="createMode = true">Neue Forengruppe Anlegen</button></td>
                            </tr>
                        </tfoot>
    				</table>
    			</div>
    		</div>
        </section>
    </div>
</template>

<script>
	export default {
		data() {
			return {
				createMode: false,
				accessDenied: false,
				forumgroups: {},
				newGroup: {
					name: '',
					id: 0
				}
			}
		},
		methods: {
			loadForumgroups() {
				axios.get('/api/administration/forum/groups')
					.then(res => {
						this.forumgroups = res.data;
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			},
			createGroup() {
				axios.post('/api/administration/forum/groups', {
					name: this.newGroup.name,
					id: this.newGroup.id
				}).then(res => {
					if(res.data !== false) {
						this.forumgroups.push(res.data);
					}
				}).catch(error => {
					if(error.response.status == 422) {
						console.log(error.response)
					}
				})
			},
			removeGroup(fgid) {
				axios.delete('/api/administration/forum/group/'+fgid)
					.then(res => {
						this.loadForumgroups();
					}).catch(error => {
						if(error.response.status == 422) {
							console.log(error.response)
						}
					})
			}
		},
		watch: {
			/**
             * Watch the groups variable and rebuild the datatable if needed
             * @param  {[type]} newVal [description]
             * @param  {[type]} oldVal [description]
             * @return {[type]}        [description]
             */
			forumgroups: function(newVal, oldVal) {
                if ($.fn.DataTable.isDataTable( '#fgroupTable' ) ) {
                    $('#fgroupTable').DataTable().destroy();
                }
				this.$nextTick(() => {
					$("#fgroupTable").DataTable();
				});
			}
		},
		activated() {
			this.loadForumgroups();
		}
	}
</script>