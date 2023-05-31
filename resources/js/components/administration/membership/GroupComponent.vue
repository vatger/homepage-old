<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Gruppenverwaltung</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <a v-on:click="deselectGroup">Gruppenverwaltung</a>
                            </li>
                            <li class="breadcrumb-item active" v-if="selectedGroup != null">
                                {{ selectedGroup.name }}
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
    		<div class="container-fluid" v-if="selectedGroup != null">
    			<div class="row">
                    <div class="col-md-3">

                        
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">

                                <h3 class="profile-username text-center">{{ selectedGroup.name }}</h3>

                                <p class="text-muted text-center">{{ selectedGroup.description }}</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Mitglieder</b> <a class="float-right">{{ selectedGroup.users.length }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Berechtigungen</b> <a class="float-right">{{ selectedGroup.permissions.length }}</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#members" data-toggle="tab">Mitglieder</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#permissions" data-toggle="tab">Berechtigungen</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#fgroups" data-toggle="tab">Forengruppen</a></li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="members">
                                        <table class="table table-borderless table-hover table-responsive-sm" id="membersTable" data-order='[[2, "asc"]]' data-page-length='50'>
                                            <thead>
                                                <tr>
                                                    <th>CID</th>
                                                    <th>Vorname</th>
                                                    <th>Nachname</th>
                                                    <th>E-Mail</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(account, index) in selectedGroup.users" v-bind:key="index">
                                                    <td>{{ account.id }}</td>
                                                    <td>{{ account.firstname }}</td>
                                                    <td>{{ account.lastname }}</td>
                                                    <td>{{ account.email }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger" v-on:click="removeAccount(account)">Entfernen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-muted text-sm text-right">Account zu dieser Gruppe hinzufügen</td>
                                                    <td><input type="text" id="newGroupAccount" placeholder="CID" class="form-control form-control-sm"></td>
                                                    <td><button class="btn btn-sm btn-default" v-on:click="addAccount">Hinzufügen</button></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="permissions">
                                        <table class="table table-borderless table-hover table-responsive-sm" id="permissionsTable" data-order='[[1, "asc"]]' data-page-length='50'>
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Slug</th>
                                                    <th>Beschreibung</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(permission, index) in allPermissions" v-bind:key="index">
                                                    <td>{{ permission.name }}</td>
                                                    <td>{{ permission.slug }}</td>
                                                    <td>{{ permission.description }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" v-if="!groupHasPermission(permission)" v-on:click="groupAddPermission(permission)">Hinzufügen</button>
                                                        <button class="btn btn-sm btn-danger" v-else v-on:click="groupRemovePermission(permission)">Entfernen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="fgroups">
                                        <table class="table table-borderless table-hover table-responsive-sm" id="permissionsTable" data-order='[[1, "asc"]]' data-page-length='50'>
                                            <thead>
                                                <tr>
                                                    <th>Forengruppe</th>
                                                    <th>ID im Forum</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(f, index) in forumgroups" v-bind:key="index">
                                                    <td>{{ f.name }}</td>
                                                    <td>{{ f.forum_id }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" v-if="!groupHasForumgroup(f)" v-on:click="groupAssignForumgroup(f)">Hinzufügen</button>
                                                        <button class="btn btn-sm btn-danger" v-else v-on:click="groupUnassignForumgroup(f)">Entfernen</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
    		</div>
    		<div class="card card-primary card-outline" v-else>
    			<div class="card-header">
    				<h5 class="card-title">Gruppenverwaltung</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-borderless table-hover table-responsive-sm" id="groupTable" data-order='[[1, "asc"]]' data-page-length='50'>
    					<thead>
    						<tr>
    							<th>ID</th>
    							<th>Name</th>
    							<th>Slug</th>
    							<th>Beschreibung</th>
    							<th></th>
    						</tr>
    					</thead>
    					<tbody>
    						<tr v-for="(group, index) in groups" v-bind:key="index">
    							<td>{{ group.id }}</td>
    							<td>{{ group.name }}</td>
    							<td>{{ group.slug }}</td>
    							<td>{{ group.description }}</td>
    							<td>
                                    <button class="btn btn-sm btn-default" v-on:click="selectGroup(group)">Details</button>
                                </td>
    						</tr>
    					</tbody>
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
				accessDenied: false,
				groups: {},
                allPermissions: {},
                forumgroups: {},
				selectedGroup: null
			}
		},
		methods: {
            /**
             * Get all groups
             * @return {[type]} [description]
             */
			loadGroups: function () {
				axios.get('/api/administration/group')
					.then(res => {
						this.groups = res.data;
					}).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			},
            /**
             * Select a group and enter detailed view
             * @param  {[type]} group [description]
             * @return {[type]}       [description]
             */
			selectGroup: function (group) {
                axios.get('/api/administration/group/'+group.slug)
                    .then(res => {
                        $('#groupTable').DataTable().destroy();
                        this.selectedGroup = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
			},
            /**
             * Deselect current group and return to the overview
             * @return {[type]} [description]
             */
            deselectGroup: function () {
                this.selectedGroup = null;
                $('#membersTable').DataTable().destroy();
                $('#permissionsTable').DataTable().destroy();
                this.$nextTick(() => {
                    $('#groupTable').DataTable();
                });
            },
            /**
             * Get all available forum groups
             * @return {[type]} [description]
             */
            loadForumgroups: function () {
                axios.get('/api/administration/forum/groups')
                    .then(res => {
                        this.forumgroups = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            /**
             * Is the selected group assigned the given forumgroup?
             * @param  {[type]} fg [description]
             * @return {[type]}    [description]
             */
            groupHasForumgroup: function(fg) {
                for(var i = 0; i < this.selectedGroup.forumgroups.length; i++)
                {
                    if(this.selectedGroup.forumgroups[i].id == fg.id) return true;
                }
                return false;
            },
            /**
             * Assign a forumgroup to the selected group
             * @param  {[type]} fg [description]
             * @return {[type]}    [description]
             */
            groupAssignForumgroup: function(fg) {
                axios.post('/api/administration/group/'+this.selectedGroup.slug+'/forumgroups', {
                    fg: fg.id
                }).then(res => {
                    this.selectedGroup = res.data;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            /**
             * Remove the forumgroup from the selected group
             * @param  {[type]} fg [description]
             * @return {[type]}    [description]
             */
            groupUnassignForumgroup: function(fg) {
                axios.delete('/api/administration/group/'+this.selectedGroup.slug+'/forumgroups', {
                    data: {
                        fg: fg.id
                    }
                }).then(res => {
                    this.selectedGroup = res.data;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            /**
             * Get all available permissions
             * @return {[type]} [description]
             */
            loadAllPermissions: function() {
                axios.get('/api/administration/permission')
                    .then(res => {
                        this.allPermissions = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            /**
             * Does a group has a permission
             * @param  {[type]} perm [description]
             * @return {[type]}      [description]
             */
            groupHasPermission: function(perm) {
                for(var i = 0; i < this.selectedGroup.permissions.length; i++)
                {
                    if(this.selectedGroup.permissions[i].id == perm.id) return true;
                }
                return false;
            },
            /**
             * Add a permission to a group
             * @param  {[type]} perm [description]
             * @return {[type]}      [description]
             */
            groupAddPermission: function (perm) {
                axios.post('/api/administration/group/'+this.selectedGroup.slug+'/permission', {
                    permission: perm.slug
                }).then(res => {
                    this.selectedGroup = res.data; // We will receive the updated group from the api
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                });
            },
            /**
             * Remove a permission from a group
             * @param  {[type]} perm [description]
             * @return {[type]}      [description]
             */
            groupRemovePermission: function (perm) {
                axios.delete('/api/administration/group/'+this.selectedGroup.slug+'/permission', {
                    data: {
                        permission: perm
                    }
                }).then(res => {
                    this.selectedGroup = res.data;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                })
            },
            addAccount: function () {
                axios.post('/api/administration/group/'+this.selectedGroup.slug+'/account', {
                    cid: $('#newGroupAccount').val()
                }).then(res => {
                    this.selectedGroup = res.data;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                })
            },
            removeAccount: function (account) {
                axios.delete('/api/administration/group/'+this.selectedGroup.slug+'/account', {
                    data: {
                        cid: account.id
                    }
                }).then(res => {
                    this.selectedGroup = res.data;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
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
			groups: function(newVal, oldVal) {
                if ($.fn.DataTable.isDataTable( '#groupTable' ) ) {
                    $('#groupTable').DataTable().destroy();
                }
				this.$nextTick(() => {
					$("#groupTable").DataTable();
				});
			},
            /**
             * Watch the selected group to initialize DataTables
             * @param  {[type]} newVal [description]
             * @param  {[type]} oldVal [description]
             * @return {[type]}        [description]
             */
            selectedGroup: function(newVal, oldVal) {
                if(newVal != null) {
                    this.$nextTick(() => {
                        $('#membersTable').DataTable();
                        $('#permissionsTable').DataTable();
                    });
                }
            }
		},
		activated() {
            this.loadAllPermissions();
            this.loadForumgroups();
			this.loadGroups();
		}
	}
</script>