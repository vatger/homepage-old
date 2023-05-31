<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - EuroScope</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                            	EuroScope
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
        	<div class="container-fluid">
                <div class="row">
                    <scenario-creator></scenario-creator>
                </div>
        	</div>
        </section>
    </div>
</template>

<script>
    import ScenarioCreatorComponent from './ScenarioCreatorComponent.vue'

	export default{
		components: {
            'scenario-creator': ScenarioCreatorComponent
		}
	}
</script>