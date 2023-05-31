<template>
	<div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <router-link to="/administration">
                                    Administration
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
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mitglieder</span>
                                <span class="info-box-number">{{ statistic.members }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Inaktive</span>
                                <span class="info-box-number">{{ statistic.inactive }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">vACC Zugehörige</span>
                                <span class="info-box-number">{{ statistic.division }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Anderer vACC Zugeordnete</span>
                                <span class="info-box-number">{{ statistic.nonDivision }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-plane"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Flüge</span>
                                <span class="info-box-number">{{ statistic.flights }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-wifi"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">ATC Abdeckung</span>
                                <span class="info-box-number">{{ statistic.atcs }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    
	export default{
        data() {
            return {
                statistic: {}
            }
        },
        methods: {
            loadStatistic() {
                axios.get('/api/administration')
                    .then(res => {
                        this.statistic = res.data;
                    });
            }
        },
        activated() {
            this.loadStatistic();
        }
	}
</script>