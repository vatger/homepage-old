<template>
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Administration - Navigation - Aerodrome</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/administration">
                                    Administration
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                <a v-on:click="deselectAerodrome">Flugplatzverwaltung</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" v-if="error404">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-info">
                            <div class="card-header">
                                <h3 class="card-title">Flugplatz Nicht Gefunden</h3>
                            </div>
                            <div class="card-body">
                                Der Flugplatz mit dem ICAO {{ searchAerodromeIcao }} ist uns nicht bekannt.
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-block btn-default" v-on:click="createAerodrome">Flugplatz Anlegen</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" v-if="createModeActive">
                        <div class="card bg-default">
                            <div class="card-header">
                                <h3 class="card-title">Flugplatz Anlegen</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled alert alert-danger" v-if="errors">
                                    <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
                                </ul>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" v-model="newAerodrome.name">
                                </div>
                                <div class="form-group">
                                    <label for="description">Beschreibung</label>
                                    <textarea class="form-control" id="description" v-model="newAerodrome.description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="icao">ICAO</label>
                                    <input type="text" class="form-control" id="icao" v-model="newAerodrome.icao">
                                </div>
                                <div class="form-group">
                                    <label for="iata">IATA</label>
                                    <input type="text" class="form-control" id="iata" v-model="newAerodrome.iata">
                                </div>
                                <div class="form-group">
                                    <label for="country">Land</label>
                                    <input type="text" class="form-control" id="country" v-model="newAerodrome.country">
                                </div>
                                <div class="form-group">
                                    <label for="state">Bundesland</label>
                                    <input type="text" class="form-control" id="state" v-model="newAerodrome.state">
                                </div>
                                <div class="form-group">
                                    <label for="city">Stadt</label>
                                    <input type="text" class="form-control" id="city" v-model="newAerodrome.city">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-block btn-default" v-on:click="doCreateAerodrome">Flugplatz Speichern</button>
                            </div>
                        </div>
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
        <section class="content" v-if="selectedAerodrome == null">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title">Aerodromes</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="icao">ICAO</label>
                        <input type="text" class="form-control" id="icao" v-model="searchAerodromeIcao">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-block" v-on:click="searchAerodrome">Suchen</button>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" v-if="selectedAerodrome != null && !accessDenied">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">{{ selectedAerodrome.name }}</h3>
                                <p class="text-muted text-center">{{ selectedAerodrome.icao }}</p>
                                <p class="text-muted text-center">{{ selectedAerodrome.iata }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <ul class="list-unstyled alert alert-danger" v-if="errors">
                            <li v-for="(value, key) in validationErrors" v-bind:key="key">{{ value }}</li>
                        </ul>
                        <div class="card card-primary card-outline">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Allgemeines</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#editGeneral" data-toggle="tab">Allgemeine Angaben Bearbeiten</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#stations" data-toggle="tab">ATC Stationen</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#charts" data-toggle="tab">Charts</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#runways" data-toggle="tab">Runways</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#navaids" data-toggle="tab">Navaids</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#stands" data-toggle="tab">Stands</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="general">
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">ICAO / IATA</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.icao+' / '+selectedAerodrome.iata }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Breitengrad</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.latitude }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Längengrad</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.longitude }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Feldhöhe in Fuß</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.elevation }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Major - Klassifizierung</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.major ? 'Yes' : 'No' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Militärische Nutzung</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.military ? 'Yes' : 'No' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Zivile Nutzung</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.civilian ? 'Yes' : 'No' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-globe"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Land</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.country_detail != null ? selectedAerodrome.country_detail.name : selectedAerodrome.country }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-globe-europe"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Bundesland</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.state }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-12">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-city"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Stadt</span>
                                                        <span class="info-box-number">{{ selectedAerodrome.city }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Wikieintrag</h5>
                                                <p v-if="selectedAerodrome.wiki_link">{{ selectedAerodrome.wiki_link }}</p>
                                                <p class="text-warning" v-else>Kein Eintrag hinterlegt.</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Beschreibung des Flugplatzes</h5>
                                                <p v-if="selectedAerodrome.description.length > 0">{{ selectedAerodrome.description }}</p>
                                                <p class="text-warning" v-else>Keine Beschreibung vorhanden.</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Zugewiesen zu folgenden Regionalgruppen</h5>
                                                <ul class="list-unstyled" v-if="selectedAerodrome.regionalgroups.length > 0">
                                                    <li v-for="rg in selectedAerodrome.regionalgroups" v-bind:key="rg.id">{{ rg.name }}</li>
                                                </ul>
                                                <p class="text-warning" v-else>Keine Zuweisungen gefunden.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="editGeneral">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" v-model="selectedAerodrome.name">
                                        </div>
                                        <div class="form-group">
                                            <label for="icao">ICAO</label>
                                            <input type="text" class="form-control" id="icao" v-model="selectedAerodrome.icao">
                                        </div>
                                        <div class="form-group">
                                            <label for="iata">IATA</label>
                                            <input type="text" class="form-control" id="iata" v-model="selectedAerodrome.iata">
                                        </div>
                                        <div class="form-group">
                                            <label for="country">Land</label>
                                            <input type="text" class="form-control" id="country" v-model="selectedAerodrome.country">
                                        </div>
                                        <div class="form-group">
                                            <label for="state">Bundesland</label>
                                            <input type="text" class="form-control" id="state" v-model="selectedAerodrome.state">
                                        </div>
                                        <div class="form-group">
                                            <label for="city">Stadt</label>
                                            <input type="text" class="form-control" id="city" v-model="selectedAerodrome.city">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" id="amaSwitch" v-model="selectedAerodrome.major">
                                                <label class="custom-control-label" for="amaSwitch">Major - Klassifizierung</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" id="amSwitch" v-model="selectedAerodrome.military">
                                                <label class="custom-control-label" for="amSwitch">Militärische Nutzung</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" id="acSwitch" v-model="selectedAerodrome.civilian">
                                                <label class="custom-control-label" for="acSwitch">Zivile Nutzung</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" id="aacSwitch" v-model="selectedAerodrome.active">
                                                <label class="custom-control-label" for="aacSwitch">Aktiv</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="latitude">Breitengrad</label>
                                            <input type="text" class="form-control" id="latitude" v-model="selectedAerodrome.latitude">
                                        </div>
                                        <div class="form-group">
                                            <label for="longitude">Längengrad</label>
                                            <input type="text" class="form-control" id="longitude" v-model="selectedAerodrome.longitude">
                                        </div>
                                        <div class="form-group">
                                            <label for="elevation">Feldhöhe in Fuß</label>
                                            <input type="text" class="form-control" id="elevation" v-model="selectedAerodrome.elevation">
                                        </div>
                                        <div class="form-group">
                                            <label for="wikiLink">Wikieintrag</label>
                                            <input type="text" class="form-control" id="wikiLink" v-model="selectedAerodrome.wiki_link">
                                        </div>
                                        <div class="form-group" v-if="availableRegionalgroups.length > 0">
                                            <label for="regionalgroup">Regionalgruppe(n)</label>
                                            <select class="form-control" id="regionalgroup" v-model="selectedAerodrome.regionalgroups" multiple="true">
                                                <option v-for="rg in availableRegionalgroups" :value="rg" v-bind:key="rg.id">{{ rg.name }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-default btn-block" v-on:click="updateGeneralInformation">Speichern</button>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="stations">
                                        <div class="row">
                                            <table class="table table-borderless table-hover table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Station</th>
                                                        <th>Frequency</th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <draggable v-model="stations" @change="updateStationOrder" :tag="'tbody'">
                                                    <tr v-for="station in stations" :key="station.id">
                                                        <td>{{ station.pivot.order }}</td>
                                                        <td>{{ station.name + '( ' + station.ident + ' )' }}</td>
                                                        <td>{{ station.fixedFrequency }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" v-on:click="removeStation(station)">Löschen</button>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-arrows-alt-v order-handle"></i>
                                                        </td>
                                                    </tr>
                                                </draggable>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2" class="text-muted text-sm text-right">Station Hinzufügen</td>
                                                        <td colspan="2">
                                                            <select id="newStation" class="form-control form-control-sm" v-model="selectedStation">
                                                                <option :value="station.id" v-for="(station, index) in availableStations" v-bind:key="index">{{ station.ident }} - {{ station.name }}</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-default" v-on:click="addStation">Hinzufügen</button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="charts">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Chart Suchen</label>
                                                    <autocomplete
                                                        :search="searchChart"
                                                        :get-result-value="getSearchChartValue"
                                                        placeholder="Chart Name / Path"
                                                        aria-label="Chart Suchen"
                                                        @submit="handleSearchChartSubmit"
                                                    ></autocomplete>
                                                </div>
                                                <div class="form-group">
                                                    <label for="charts">Verfügbare Charts</label>
                                                    <select class="form-control" id="charts" v-model="selectedChart">
                                                        <option v-for="(chart, index) in availableCharts" :value="chart" v-bind:key="index">{{ chart.name }} [{{chart.href}}]</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-default btn-block" v-on:click="addChart">Hinzufügen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <table class="table table-borderless table-hover table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Pfad <small>Relativ zu: nav.vatsim-germany.org/files</small></th>
                                                        <th>FRI</th>
                                                        <th>Type</th>
                                                        <th>Airac</th>
                                                        <th>Publiziert</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(chart, index) in selectedAerodrome.charts" :class="'chart-'+chart.type" v-bind:key="index">
                                                        <td>{{ chart.name }}</td>
                                                        <td>
                                                            <a :href="chart.href" target="_blank">{{ chart.href.split('files/').pop() }}</a>
                                                        </td>
                                                        <td>{{ chart.fri }}</td>
                                                        <td>{{ chart.type }}</td>
                                                        <td>{{ chart.airac }}</td>
                                                        <td>{{ chart.published ? 'Ja' : 'Nein' }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" v-on:click="removeChart(chart)">Löschen</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="runways">
                                        <div class="card card-primary card-outline" v-if="createRunwayMode">
                                            <div class="card-header">
                                                <h5 class="card-title">Runway Hinzufügen</h5>
                                                <div class="card-tools">
                                                    <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="createRunwayMode = false; newRunway = {}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="ident">Ident</label>
                                                    <input class="form-control" id="ident" type="text" v-model="newRunway.ident" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="hdg">Heading</label>
                                                    <input type="number" step="1" class="form-control" id="hdg" v-model="newRunway.heading">
                                                </div>
                                                <div class="form-group">
                                                    <label for="lng">Length (Meters)</label>
                                                    <input type="number" step="1" class="form-control" id="lng" v-model="newRunway.length">
                                                </div>
                                                <div class="form-group">
                                                    <label for="wds">Width (Meters)</label>
                                                    <input type="number" step="1" class="form-control" id="wds" v-model="newRunway.width">
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Surface</label>
                                                    <select id="type" class="form-control" v-model="newRunway.surface_type">
                                                        <option value="1">Asphalt</option>
                                                        <option value="2">Concrete</option>
                                                        <option value="3">Gras</option>
                                                        <option value="4">Water</option>
                                                        <option value="5">Sand</option>
                                                        <option value="6">Graded / Rolled Earth</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Opposite End</label>
                                                    <select id="type" class="form-control" v-model="newRunway.opposite_id">
                                                        <option value="-1">None</option>
                                                        <option :value="rwy.id" v-for="(rwy, index) in selectedAerodrome.runways" v-bind:key="index">{{ rwy.ident }}</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-default btn-block" v-on:click="createRunway">Speichern</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-primary card-outline" v-if="editRunwayMode">
                                            <div class="card-header">
                                                <h5 class="card-title">Runway Bearbeiten</h5>
                                                <div class="card-tools">
                                                    <button type="button" data-card-widget="collapse" class="btn btn-tool" v-on:click="editRunwayMode = false; selectedRunway = null">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="ident">Ident</label>
                                                    <input class="form-control" id="ident" type="text" v-model="selectedRunway.ident" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="hdg">Heading</label>
                                                    <input type="number" step="1" class="form-control" id="hdg" v-model="selectedRunway.heading">
                                                </div>
                                                <div class="form-group">
                                                    <label for="lng">Length (m)</label>
                                                    <input type="number" step="1" class="form-control" id="lng" v-model="selectedRunway.length">
                                                </div>
                                                <div class="form-group">
                                                    <label for="wds">Width (m)</label>
                                                    <input type="number" step="1" class="form-control" id="wds" v-model="selectedRunway.width">
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Surface</label>
                                                    <select id="type" class="form-control" v-model="selectedRunway.surface_type">
                                                        <option value="1">Asphalt</option>
                                                        <option value="2">Concrete</option>
                                                        <option value="3">Gras</option>
                                                        <option value="4">Water</option>
                                                        <option value="5">Sand</option>
                                                        <option value="6">Graded / Rolled Earth</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="type">Opposite End</label>
                                                    <select id="type" class="form-control" v-model="selectedRunway.opposite_id">
                                                        <option value="-1">None</option>
                                                        <option :value="rwy.id" v-for="(rwy, index) in selectedAerodrome.runways" v-bind:key="index">{{ rwy.ident }}</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-default btn-block" v-on:click="editRunway">Speichern</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <table class="table table-hover table-borderless table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Ident</th>
                                                        <th>Heading</th>
                                                        <th>Size</th>
                                                        <th>Surface</th>
                                                        <th>Opposite</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(rwy, index) in selectedAerodrome.runways" v-bind:key="index">
                                                        <td>{{ rwy.ident }}</td>
                                                        <td>{{ rwy.heading }}</td>
                                                        <td>{{ rwy.length }} Meters x {{ rwy.width }} Meters</td>
                                                        <td>{{ rwy.surfaceTypeString }}</td>
                                                        <td v-if="rwy.opposite != null">{{ rwy.opposite.ident }}</td>
                                                        <td v-else>None</td>
                                                        <td>
                                                            <button class="btn btn-default btn-sm" v-on:click="selectedRunway = rwy; editRunwayMode = true;">Bearbeiten</button>
                                                            <button class="btn btn-sm btn-danger" v-on:click="removeRunway(rwy)">Entfernen</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6">
                                                            <button class="btn btn-default btn-block" v-on:click="createRunwayMode = true">Runway Hinzufügen</button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="navaids">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Navaid Suchen</label>
                                                    <autocomplete
                                                        :search="searchNavaid"
                                                        :get-result-value="getSearchNavaidValue"
                                                        placeholder="Navaid Suchen"
                                                        aria-label="Navaid Suchen"
                                                        @submit="handleSearchNavaidSubmit"
                                                    ></autocomplete>
                                                </div>
                                                <div class="form-group">
                                                    <label for="navaids">Verfügbare Navaids</label>
                                                    <select class="form-control" id="navaids" v-model="selectedNavaid">
                                                        <option v-for="(navaid, index) in availableNavaids" :value="navaid" v-bind:key="index">{{ navaid.name }} [{{navaid.ident}}]</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-default btn-block" v-on:click="addNavaid">Hinzufügen</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <table class="table table-hover table-bordered table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Bearing</th>
                                                        <th>Frequency</th>
                                                        <th>Type</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(n, index) in selectedAerodrome.navaids" v-bind:key="index">
                                                        <td>{{ n.name }} ({{ n.ident }})</td>
                                                        <td>{{ n.heading }}</td>
                                                        <td>{{ n.frequency }} {{ n.frequencyBandString }}</td>
                                                        <td>{{ n.typeString }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" v-on:click="removeNavaid(n)">Löschen</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="stands">
                                        <div class="row">
                                            <div class="callout callout-info">
                                                <h5>Aerodrome Stand Coordinates</h5>
                                                <p>The stand coordinate definition for all aerodromes within the vACC Germany are defined using simple .csv files. Those files can be adjusted by any member of the navigation department. We will not check for regionalgroup membership at this point. Please keep this in mind for the moment.</p>
                                                <p class="text-danger">By uploading a new definition file the currently existing one will be overwritten without backup. Best practice should be to keep a local backup of the current file in case something brakes.</p>
                                                <p>
                                                    A few things on the file itself:
                                                    <ul>
                                                        <li>It must be a comma separated .csv file.</li>
                                                        <li>The headerline ( first line of the file ) must be exactly this ( without the quotation marks ): "id,latcoord,longcoord".</li>
                                                        <li>A line defines a stand.</li>
                                                        <li>A stand looks like this: "1,52.459521,9.693105"</li>
                                                    </ul>
                                                </p>
                                                <p>So the header of the .csv file contains the "names" for the columns. This MUST be the first line in the file!!! Any line thereafter defines ONE stand, so one line is one stand. The first column is just the id/name of the stand and is the column displayed on the aerodrome page. The second and third column are the coordinates the stand is located at. The coordinates must be provided in a decimal format as shown in the example above. Please take note, that a positive latitude value indicates north and a positive longitude does indicate east. So for all of Germany we should expect only positive coordinates, but do not exclude negative ( south / west ).</p>
                                            </div>
                                            <button class="btn btn-block btn-info" @click="downloadCurrentFile">Download Current Definitionfile</button>
                                        </div>
                                        <hr class="my-3">
                                        <div class="form-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" ref="file" id="newStandDefinitionFile" v-on:change="handleFileUpload">
                                                <label class="custom-file-label" for="newStandDefinitionFile">Choose New Definition File</label>
                                            </div>
                                        </div>
                                        <button @click="submitFile" class="btn btn-primary btn-block">Replace Current</button>
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
    import draggable from 'vuedraggable'
    export default{
        components: {
            draggable
        },
        data() {
            return {
                accessDenied: false,
                error404: false,
                errors: false,
                selectedAerodrome: null,
                searchAerodromeIcao: '',
                availableRegionalgroups: {},
                stations: null,
                availableStations: {},
                selectedStation: null,
                addChartMode: false,
                availableCharts: {},
                selectedChart: null,
                addNavaidMode: false,
                availableNavaids: {},
                selectedNavaid: null,
                newStandDefinition: null,
                newRunway: {},
                selectedRunway: null,
                createRunwayMode: false,
                editRunwayMode: false,
                createModeActive: false,
                newAerodrome: {}
            }
        },
        methods: {
            searchAerodrome() {
                this.error404 = false;
                axios.get('/api/administration/navigation/aerodrome/'+this.searchAerodromeIcao)
                    .then(res => {
                        this.accessDenied = false;
                        this.getAvailableRegionalgroups();
                        this.selectedAerodrome = res.data;
                        this.stations = this.selectedAerodrome.stations;
                    }).catch(error => {
                        if(error.response.status == 404) {
                            this.error404 = true;
                        }
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                    });
            },
            deselectAerodrome() {
                this.error404 = false;
                this.selectedAerodrome = null;
                this.stations = {}
            },
            getAvailableRegionalgroups() {
                axios.get('/api/administration/navigation/regionalgroups')
                    .then(res => {
                        this.availableRegionalgroups = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.availableRegionalgroups = {};
                        }
                    });
            },
            updateGeneralInformation() {
                axios.put('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id, {
                    aerodrome: this.selectedAerodrome
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            updateStationOrder() {
                this.stations.map((station, index) => {
                    station.pivot.order = index + 1;
                });
                axios.put('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/stations', {
                    stations: this.stations
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            getAvailableStations() {
                axios.get('/api/navigation/stations/0')
                    .then(res => {
                        this.availableStations = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            this.accessDenied = true;
                        }
                        if(error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        }
                    })
            },
            addStation() {
                axios.post('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/stations', {
                    station: this.selectedStation
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    this.stations = this.selectedAerodrome.stations;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            removeStation(station) {
                axios.delete('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/stations/'+station.id)
                .then(res => {
                    this.selectedAerodrome = res.data;
                    this.stations = this.selectedAerodrome.stations;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            getAvailableCharts() {
                axios.get('/api/administration/navigation/chart')
                    .then(res => {
                        this.availableCharts = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            console.log('Loading Charts Failed 403');
                            this.accessDenied = true;
                        }
                    });
            },
            searchChart(input) {
                if(input.length < 1) {return [] }
                var count = 0;
                return this.availableCharts.filter(chart => {
                    if (count > 20) return false;
                    var matches = (chart.name.toLowerCase().includes(input.toLowerCase().split(' [')[0])
                        || chart.href.toLowerCase().includes(input.toLowerCase().split(' [')[0]));
                    if (matches) return count++ < 20;
                    return false;
                });
            },
            getSearchChartValue(result) {
                return result.name + " [" + result.href + "]";
            },
            handleSearchChartSubmit(result) {
                if(result != undefined && result != null) {
                    this.selectedChart = result;
                }
            },
            addChart() {
                axios.put('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/charts', {
                    chart: this.selectedChart.id
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            removeChart(chart) {
                axios.delete('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/charts/'+chart.id)
                .then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            getAvailableNavaids() {
                axios.get('/api/administration/navigation/navaids')
                    .then(res => {
                        this.availableNavaids = res.data;
                    }).catch(error => {
                        if(error.response.status == 403) {
                            console.log('Loading Navaids Failed 403');
                            this.accessDenied = true;
                        }
                    });
            },
            searchNavaid(input) {
                if(input.length < 1) {return [] }
                var count = 0;
                return this.availableNavaids.filter(navaid => {
                    if (count > 20) return false;
                    var matches = (navaid.name != null && navaid.name.toLowerCase().includes(input.toLowerCase().split(' [')[0]))
                        || (navaid.ident != null && navaid.ident.toLowerCase().includes(input.toLowerCase().split(' [')[0]));
                    if (matches) return count++ < 20;
                    return false;
                });
            },
            getSearchNavaidValue(result) {
                return result.name + " [" + result.ident + "]";
            },
            handleSearchNavaidSubmit(result) {
                if(result != undefined && result != null) {
                    this.selectedNavaid = result;
                }
            },
            addNavaid() {
                axios.put('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/navaids', {
                    navaid: this.selectedNavaid.id
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            removeNavaid(navaid) {
                axios.delete('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/navaids/'+navaid.id)
                .then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            createRunway() {
                if(this.newRunway.opposite_id == -1) this.newRunway.opposite_id = null;
                axios.post('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/runways', {
                    newRunway: this.newRunway
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            editRunway() {
                if(this.selectedRunway.opposite_id == -1) this.selectedRunway.opposite_id = null;
                axios.put('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/runways/'+this.selectedRunway.id, {
                    editRunway: this.selectedRunway
                }).then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            removeRunway(rwy) {
                axios.delete('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/runways/'+rwy.id)
                .then(res => {
                    this.selectedAerodrome = res.data;
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich aktualisiert.</p>');
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            },
            downloadCurrentFile: function () {
                axios.get('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/stands')
                    .then(res => {
                        if(!window.navigator.msSaveOrOpenBlob) {
                            const url = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href= url;
                            link.setAttribute('download', this.selectedAerodrome.icao+'.csv');
                            document.body.appendChild(link);
                            link.click();
                        } else {
                            const url = window.navigator.msSaveOrOpenBlob(new Blob([res.data]), this.selectedAerodrome.icao+'.csv');
                        }
                    });
            },
            handleFileUpload: function () {
                this.newStandDefinition = this.$refs.file.files[0];
            },
            submitFile: function () {
                let formData = new FormData();
                formData.append('newStandFile', this.newStandDefinition);
                axios.post('/api/administration/navigation/aerodrome/'+this.selectedAerodrome.id+'/stands',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(res => {
                        if(res.data.result !== false) {
                            this.loadAerodrome();
                        }
                    });
            },
            createAerodrome: function () {
                this.newAerodrome.name = '';
                this.newAerodrome.description = '';
                this.newAerodrome.icao = this.searchAerodromeIcao;
                this.newAerodrome.iata = '';
                this.newAerodrome.country = '';
                this.newAerodrome.city = '';
                this.newAerodrome.state = '';
                this.createModeActive = true;
            },
            doCreateAerodrome: function () {
                axios.post('/api/administration/navigation/aerodrome', {
                    newAerodrome: this.newAerodrome
                }).then(res => {
                    $('#sysMessages').append('<p class="text-success mb-2">Flugplatzdaten erfolgreich angelegt.</p>');
                    this.createModeActive = false;
                    this.newAerodrome = {};
                    this.accessDenied = false;
                    this.error404 = false;
                    this.selectedAerodrome = null;
                    this.searchAerodromeIcao = null;
                }).catch(error => {
                    if(error.response.status == 403) {
                        this.accessDenied = true;
                    }
                    if(error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
            }
        },
        computed: {
            validationErrors(){
                let errors = Object.values(this.errors);
                errors = errors.flat();
                return errors;
            }
        },
        activated() {
            this.getAvailableStations();
            this.getAvailableCharts();
            this.getAvailableNavaids();
        }
    }
</script>
