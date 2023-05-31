<template>
    
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Membership</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">
                                Membership
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
            <div class="card card-primary card-outline">
    			<div class="card-header">
    				<h5 class="card-title">Membership</h5>
    			</div>
    			<div class="card-body">
    				<table class="table table-borderless table-hover table-responsive-sm" id="membershipTable" data-order='[[2, "asc"]]' data-page-length='50'>
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
    						<tr v-for="(account, index) in accounts" v-bind:key="index">
    							<td>{{ account.id }}</td>
    							<td>{{ account.firstname }}</td>
    							<td>{{ account.lastname }}</td>
    							<td>{{ account.email }}</td>
    							<td>
                                    <button class="btn btn-sm btn-default" v-on:click="viewAccount(account)">Details</button>
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
                accounts: {}
            }
        },
        methods: {
            /**
             * Loads all accounts
             * @return {[type]} [description]
             */
			loadAccounts: async function () {
				await axios.get('/api/administration/membership')
					.then(res => {
						this.accounts = res.data;
					})
                    .catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            /**
             * Handle view request
             */
            viewAccount: function(account) {
                this.$router.push({ path: `/administration/membership/${account.id}` });
            }
        },
		watch: {
            /**
             * Watch the accounts variable and rebuild the datatable if needed
             * @param  {[type]} newVal [description]
             * @param  {[type]} oldVal [description]
             * @return {[type]}        [description]
             */
			accounts: function(newVal, oldVal) {
                if ($.fn.DataTable.isDataTable( '#membershipTable' ) ) {
                    $('#membershipTable').DataTable().destroy();
                }
				this.$nextTick(() => {
					$("#membershipTable").DataTable();
				});
			}
		},
		activated() {
			this.loadAccounts();
		}

    }

</script>