<?php

use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{

	// All ATC Stations in the vACC
    protected $stations = [
        0 => [
            'callsign' => 'EDAB_I_TWR',
            'freq' => '120.60',
            'name' => 'Bautzen Info',
          ],
        1 => [
            'callsign' => 'EDAC_I_TWR',
            'freq' => '123.57',
            'name' => 'Altenburg Info',
          ],
        2 => [
            'callsign' => 'EDAG_I_TWR',
            'freq' => '123.00',
            'name' => 'Großrückerswalde Info',
          ],
        3 => [
            'callsign' => 'EDAH_TWR',
            'freq' => '132.82',
            'name' => 'Heringsdorf Tower',
          ],
        4 => [
            'callsign' => 'EDAK_I_TWR',
            'freq' => '122.70',
            'name' => 'Grossenhain Info',
          ],
        5 => [
            'callsign' => 'EDAU_I_TWR',
            'freq' => '122.60',
            'name' => 'Riesa Info',
          ],
        6 => [
            'callsign' => 'EDAV_I_TWR',
            'freq' => '119.05',
            'name' => 'Finow Info',
          ],
        7 => [
            'callsign' => 'EDAY_I_TWR',
            'freq' => '123.05',
            'name' => 'Strausberg Info',
          ],
        8 => [
            'callsign' => 'EDAZ_I_TWR',
            'freq' => '131.15',
            'name' => 'Schönhagen Info',
          ],
        9 => [
            'callsign' => 'EDBB_DEP',
            'freq' => '120.62',
            'name' => 'Bremen Radar (Berlin departure)',
          ],
        10 => [
            'callsign' => 'EDBB_F_APP',
            'freq' => '121.12',
            'name' => 'Berlin Director',
          ],
        11 => [
            'callsign' => 'EDBB_N_APP',
            'freq' => '119.62',
            'name' => 'Bremen Radar (Berlin Arrival North)',
          ],
        12 => [
            'callsign' => 'EDBB_S_APP',
            'freq' => '126.42',
            'name' => 'Bremen Radar (Berlin Arrival South)',
          ],
        13 => [
            'callsign' => 'EDBC_GND',
            'freq' => '121.82',
            'name' => 'Cochstedt Ground',
          ],
        14 => [
            'callsign' => 'EDBC_TWR',
            'freq' => '131.12',
            'name' => 'Cochstedt Tower',
          ],
        15 => [
            'callsign' => 'EDBH_I_TWR',
            'freq' => '118.07',
            'name' => 'Barth Info',
          ],
        16 => [
            'callsign' => 'EDBK_I_TWR',
            'freq' => '122.72',
            'name' => 'Kyritz Info',
          ],
        17 => [
            'callsign' => 'EDBM_I_TWR',
            'freq' => '119.30',
            'name' => 'Magdeburg Info',
          ],
        18 => [
            'callsign' => 'EDBN_I_TWR',
            'freq' => '119.17',
            'name' => 'Neubrandenburg Info',
          ],
        19 => [
            'callsign' => 'EDBW_I_TWR',
            'freq' => '122.60',
            'name' => 'Werneuchen Info',
          ],
        20 => [
            'callsign' => 'EDBY_I_TWR',
            'freq' => '122.85',
            'name' => 'Schmoldow Info',
          ],
        21 => [
            'callsign' => 'EDCA_I_TWR',
            'freq' => '122.65',
            'name' => 'Anklam Info',
          ],
        22 => [
            'callsign' => 'EDCD_I_TWR',
            'freq' => '118.12',
            'name' => 'Drewitz Info',
          ],
        23 => [
            'callsign' => 'EDCG_I_TWR',
            'freq' => '123.00',
            'name' => 'Rügen Info',
          ],
        24 => [
            'callsign' => 'EDCJ_I_TWR',
            'freq' => '122.50',
            'name' => 'Jahnsdorf Info',
          ],
        25 => [
            'callsign' => 'EDCM_I_TWR',
            'freq' => '122.05',
            'name' => 'Kamenz Info',
          ],
        26 => [
            'callsign' => 'EDCP_I_TWR',
            'freq' => '122.47',
            'name' => 'Peenemünde Info',
          ],
        27 => [
            'callsign' => 'EDCS_I_TWR',
            'freq' => '123.65',
            'name' => 'Saarmund Info',
          ],
        28 => [
            'callsign' => 'EDDB_A_GND',
            'freq' => '129.60',
            'name' => 'Schönefeld Ground',
          ],
        29 => [
            'callsign' => 'EDDB_ATIS',
            'freq' => '124.95',
            'name' => 'Schönefeld ATIS',
          ],
        30 => [
            'callsign' => 'EDDB_DEL',
            'freq' => '121.60',
            'name' => 'Schönefeld Delivery',
          ],
        31 => [
            'callsign' => 'EDDB_N_GND',
            'freq' => '129.50',
            'name' => 'Schönefeld Ground',
          ],
        32 => [
            'callsign' => 'EDDB_N_TWR',
            'freq' => '120.02',
            'name' => 'Schönefeld Tower',
          ],
        33 => [
            'callsign' => 'EDDB_S_GND',
            'freq' => '121.70',
            'name' => 'Schönefeld Ground',
          ],
        34 => [
            'callsign' => 'EDDB_S_TWR',
            'freq' => '118.80',
            'name' => 'Schönefeld Tower (South)',
          ],
        35 => [
            'callsign' => 'EDDB_V_TWR',
            'freq' => '119.57',
            'name' => 'Schönefeld Tower (VFR)',
          ],
        36 => [
            'callsign' => 'EDDC_A_GND',
            'freq' => '121.75',
            'name' => 'Dresden Apron',
          ],
        37 => [
            'callsign' => 'EDDC_APP',
            'freq' => '125.87',
            'name' => 'München Radar (SASL Sachsen Low)',
          ],
        38 => [
            'callsign' => 'EDDC_ATIS',
            'freq' => '118.87',
            'name' => 'Dresden ATIS',
          ],
        39 => [
            'callsign' => 'EDDC_GND',
            'freq' => '121.97',
            'name' => 'Dresden Ground',
          ],
        40 => [
            'callsign' => 'EDDC_TWR',
            'freq' => '122.92',
            'name' => 'Dresden Tower',
          ],
        41 => [
            'callsign' => 'EDDE_A_GND',
            'freq' => '121.90',
            'name' => 'Erfurt Apron',
          ],
        42 => [
            'callsign' => 'EDDE_ATIS',
            'freq' => '133.42',
            'name' => 'Erfurt ATIS',
          ],
        43 => [
            'callsign' => 'EDDE_GND',
            'freq' => '121.75',
            'name' => 'Erfurt Ground',
          ],
        44 => [
            'callsign' => 'EDDE_TWR',
            'freq' => '121.15',
            'name' => 'Erfurt Tower',
          ],
        45 => [
            'callsign' => 'EDDF_ATIS',
            'freq' => '118.02',
            'name' => 'Frankfurt ATIS',
          ],
        46 => [
            'callsign' => 'EDDF_D_APP',
            'freq' => '120.15',
            'name' => 'Langen Radar (Frankfurt Departure)',
          ],
        47 => [
            'callsign' => 'EDDF_DEL',
            'freq' => '121.90',
            'name' => 'Frankfurt Delivery',
          ],
        48 => [
            'callsign' => 'EDDF_E_GND',
            'freq' => '121.95',
            'name' => 'Frankfurt East Apron',
          ],
        49 => [
            'callsign' => 'EDDF_F_APP',
            'freq' => '127.27',
            'name' => 'Frankfurt North Director',
          ],
        50 => [
            'callsign' => 'EDDF_GND',
            'freq' => '121.80',
            'name' => 'Frankfurt Ground',
          ],
        51 => [
            'callsign' => 'EDDF_H_APP',
            'freq' => '119.02',
            'name' => 'Langen Radar (Frankfurt High Approach)',
          ],
        52 => [
            'callsign' => 'EDDF_N_APP',
            'freq' => '120.80',
            'name' => 'Langen Radar (Frankfurt North Approach)',
          ],
        53 => [
            'callsign' => 'EDDF_P_GND',
            'freq' => '121.85',
            'name' => 'Frankfurt Pushback Apron',
          ],
        54 => [
            'callsign' => 'EDDF_S_APP',
            'freq' => '125.35',
            'name' => 'Langen Radar (Frankfurt South Approach)',
          ],
        55 => [
            'callsign' => 'EDDF_TWR',
            'freq' => '119.90',
            'name' => 'Frankfurt Main Tower',
          ],
        56 => [
            'callsign' => 'EDDF_U_APP',
            'freq' => '118.50',
            'name' => 'Frankfurt South Director',
          ],
        57 => [
            'callsign' => 'EDDF_W_GND',
            'freq' => '121.75',
            'name' => 'Frankfurt West Apron',
          ],
        58 => [
            'callsign' => 'EDDF_W_TWR',
            'freq' => '124.85',
            'name' => 'Frankfurt Tower (West)',
          ],
        59 => [
            'callsign' => 'EDDG_APP',
            'freq' => '129.30',
            'name' => 'Langen Radar (HMML Hamm Low)',
          ],
        60 => [
            'callsign' => 'EDDG_ATIS',
            'freq' => '127.17',
            'name' => 'Münster ATIS',
          ],
        61 => [
            'callsign' => 'EDDG_GND',
            'freq' => '121.87',
            'name' => 'Münster Ground',
          ],
        62 => [
            'callsign' => 'EDDG_TWR',
            'freq' => '129.80',
            'name' => 'Münster Tower',
          ],
        63 => [
            'callsign' => 'EDDH_ATIS',
            'freq' => '123.12',
            'name' => 'Hamburg ATIS',
          ],
        64 => [
            'callsign' => 'EDDH_DEL',
            'freq' => '121.80',
            'name' => 'Hamburg Ground',
          ],
        65 => [
            'callsign' => 'EDDH_E_APP',
            'freq' => '127.67',
            'name' => 'Bremen Radar (HAME Hamburg Arrival East)',
          ],
        66 => [
            'callsign' => 'EDDH_F_APP',
            'freq' => '118.20',
            'name' => 'Hamburg Director',
          ],
        67 => [
            'callsign' => 'EDDH_GND',
            'freq' => '121.70',
            'name' => 'Hamburg Apron',
          ],
        68 => [
            'callsign' => 'EDDH_TWR',
            'freq' => '126.85',
            'name' => 'Hamburg Tower',
          ],
        69 => [
            'callsign' => 'EDDH_W_APP',
            'freq' => '134.25',
            'name' => 'Bremen Radar (HAMW Hamburg Arrival West)',
          ],
          70 => [
            'callsign' => 'EDDI_ATIS',
            'freq' => '126.02',
            'name' => 'Tempelhof ATIS',
          ],
          71 => [
            'callsign' => 'EDDI_GND',
            'freq' => '121.95',
            'name' => 'Tempelhof Ground',
          ],
          72 => [
            'callsign' => 'EDDI_TWR',
            'freq' => '119.57',
            'name' => 'Tempelhof Tower',
          ],
          73 => [
            'callsign' => 'EDDK_APP',
            'freq' => '118.75',
            'name' => 'Langen Radar (KAE Köln Arrival East)',
          ],
          74 => [
            'callsign' => 'EDDK_ATIS',
            'freq' => '124.10',
            'name' => 'Köln/Bonn ATIS',
          ],
          75 => [
            'callsign' => 'EDDK_DEL',
            'freq' => '121.85',
            'name' => 'Köln/Bonn Delivery',
          ],
          76 => [
            'callsign' => 'EDDK_F_APP',
            'freq' => '121.05',
            'name' => 'Köln/Bonn Director',
          ],
          77 => [
            'callsign' => 'EDDK_GND',
            'freq' => '121.72',
            'name' => 'Köln/Bonn Ground',
          ],
          78 => [
            'callsign' => 'EDDK_TWR',
            'freq' => '124.97',
            'name' => 'Köln/Bonn Tower',
          ],
          79 => [
            'callsign' => 'EDDK_W_APP',
            'freq' => '135.35',
            'name' => 'Langen Radar (KAW Köln Arrival West)',
          ],
          80 => [
            'callsign' => 'EDDL_APP',
            'freq' => '128.55',
            'name' => 'Langen Radar (Düsseldorf Arrival)',
          ],
          81 => [
            'callsign' => 'EDDL_ATIS',
            'freq' => '123.77',
            'name' => 'Düsseldorf ATIS',
          ],
          82 => [
            'callsign' => 'EDDL_DEL',
            'freq' => '121.77',
            'name' => 'Düsseldorf Delivery',
          ],
          83 => [
            'callsign' => 'EDDL_E_GND',
            'freq' => '121.60',
            'name' => 'Düsseldorf Ground (East)',
          ],
          84 => [
            'callsign' => 'EDDL_F_APP',
            'freq' => '128.65',
            'name' => 'Düsseldorf Director',
          ],
          85 => [
            'callsign' => 'EDDL_N_APP',
            'freq' => '128.50',
            'name' => 'Langen Radar (Departure North)',
          ],
          86 => [
            'callsign' => 'EDDL_S_APP',
            'freq' => '121.35',
            'name' => 'Langen Radar (Departure South)',
          ],
          87 => [
            'callsign' => 'EDDL_TWR',
            'freq' => '118.30',
            'name' => 'Düsseldorf Tower',
          ],
          88 => [
            'callsign' => 'EDDL_W_GND',
            'freq' => '121.90',
            'name' => 'Düsseldorf Ground (West)',
          ],
          89 => [
            'callsign' => 'EDDM_1_APP',
            'freq' => '128.02',
            'name' => 'München Radar (North High)',
          ],
          90 => [
            'callsign' => 'EDDM_1_GND',
            'freq' => '121.77',
            'name' => 'München Apron (Apron 1 and 6-9)',
          ],
          91 => [
            'callsign' => 'EDDM_2_APP',
            'freq' => '120.77',
            'name' => 'München Radar (South High)',
          ],
          92 => [
            'callsign' => 'EDDM_2_GND',
            'freq' => '121.70',
            'name' => 'München Apron (Apron 2)',
          ],
          93 => [
            'callsign' => 'EDDM_3_GND',
            'freq' => '121.92',
            'name' => 'München Apron (Apron 3)',
          ],
          94 => [
            'callsign' => 'EDDM_ATIS',
            'freq' => '123.12',
            'name' => 'München ATIS',
          ],
          95 => [
            'callsign' => 'EDDM_DEL',
            'freq' => '121.72',
            'name' => 'München Delivery',
          ],
          96 => [
            'callsign' => 'EDDM_F_APP',
            'freq' => '118.82',
            'name' => 'München Director',
          ],
          97 => [
            'callsign' => 'EDDM_N_APP',
            'freq' => '123.90',
            'name' => 'München Radar (North Low)',
          ],
          98 => [
            'callsign' => 'EDDM_N_GND',
            'freq' => '121.97',
            'name' => 'München Ground (North)',
          ],
          99 => [
            'callsign' => 'EDDM_N_TWR',
            'freq' => '118.70',
            'name' => 'München Tower (North)',
          ],
          100 => [
            'callsign' => 'EDDM_S_APP',
            'freq' => '127.95',
            'name' => 'München Radar (South Low)',
          ],
          101 => [
            'callsign' => 'EDDM_S_GND',
            'freq' => '121.82',
            'name' => 'München Ground (South)',
          ],
          102 => [
            'callsign' => 'EDDM_S_TWR',
            'freq' => '120.50',
            'name' => 'München Tower (South)',
          ],
          103 => [
            'callsign' => 'EDDN_APP',
            'freq' => '129.52',
            'name' => 'München Radar (FRKL Franken Low)',
          ],
          104 => [
            'callsign' => 'EDDN_ATIS',
            'freq' => '123.07',
            'name' => 'Nürnberg ATIS',
          ],
          105 => [
            'callsign' => 'EDDN_F_APP',
            'freq' => '119.47',
            'name' => 'Nürnberg Director',
          ],
          106 => [
            'callsign' => 'EDDN_GND',
            'freq' => '118.10',
            'name' => 'Nürnberg Ground',
          ],
          107 => [
            'callsign' => 'EDDN_TWR',
            'freq' => '118.30',
            'name' => 'Nürnberg Tower',
          ],
          108 => [
            'callsign' => 'EDDP_APP',
            'freq' => '126.17',
            'name' => 'München Radar (TRGL Thüringen Low)',
          ],
          109 => [
            'callsign' => 'EDDP_ATIS',
            'freq' => '123.95',
            'name' => 'Leipzig ATIS',
          ],
          110 => [
            'callsign' => 'EDDP_DEL',
            'freq' => '121.80',
            'name' => 'Leipzig Delivery',
          ],
          111 => [
            'callsign' => 'EDDP_F_APP',
            'freq' => '128.47',
            'name' => 'Leipzig Director',
          ],
          112 => [
            'callsign' => 'EDDP_GND',
            'freq' => '121.67',
            'name' => 'Leipzig Ground',
          ],
          113 => [
            'callsign' => 'EDDP_N_TWR',
            'freq' => '125.95',
            'name' => 'Leipzig Tower North',
          ],
          114 => [
            'callsign' => 'EDDP_S_TWR',
            'freq' => '121.10',
            'name' => 'Leipzig Tower South',
          ],
          115 => [
            'callsign' => 'EDDR_APP',
            'freq' => '129.67',
            'name' => 'Langen Radar (PFA Pfalz)',
          ],
          116 => [
            'callsign' => 'EDDR_ATIS',
            'freq' => '125.30',
            'name' => 'Saarbrücken ATIS',
          ],
          117 => [
            'callsign' => 'EDDR_TWR',
            'freq' => '118.35',
            'name' => 'Saarbrücken Tower',
          ],
          118 => [
            'callsign' => 'EDDS_2_TWR',
            'freq' => '119.05',
            'name' => 'Stuttgart Tower (VFR)',
          ],
          119 => [
            'callsign' => 'EDDS_ATIS',
            'freq' => '126.12',
            'name' => 'Stuttgart ATIS',
          ],
          120 => [
            'callsign' => 'EDDS_DEL',
            'freq' => '121.90',
            'name' => 'Stuttgart Delivery',
          ],
          121 => [
            'callsign' => 'EDDS_F_APP',
            'freq' => '119.85',
            'name' => 'Stuttgart Director',
          ],
          122 => [
            'callsign' => 'EDDS_GND',
            'freq' => '118.60',
            'name' => 'Stuttgart Ground',
          ],
          123 => [
            'callsign' => 'EDDS_N_APP',
            'freq' => '125.05',
            'name' => 'Langen Radar (STG Stuttgart)',
          ],
          124 => [
            'callsign' => 'EDDS_S_APP',
            'freq' => '119.20',
            'name' => 'Langen Radar (RTL Reutlingen)',
          ],
          125 => [
            'callsign' => 'EDDS_TWR',
            'freq' => '118.80',
            'name' => 'Stuttgart Tower',
          ],
          126 => [
            'callsign' => 'EDDT_ATIS',
            'freq' => '125.90',
            'name' => 'Tegel ATIS',
          ],
          127 => [
            'callsign' => 'EDDT_DEL',
            'freq' => '121.92',
            'name' => 'Tegel Delivery',
          ],
          128 => [
            'callsign' => 'EDDT_GND',
            'freq' => '121.75',
            'name' => 'Tegel Ground',
          ],
          129 => [
            'callsign' => 'EDDT_TWR',
            'freq' => '124.52',
            'name' => 'Tegel Tower',
          ],
          130 => [
            'callsign' => 'EDDV_APP',
            'freq' => '131.32',
            'name' => 'Bremen Radar (HAN Hannover)',
          ],
          131 => [
            'callsign' => 'EDDV_ATIS',
            'freq' => '132.12',
            'name' => 'Hannover ATIS',
          ],
          132 => [
            'callsign' => 'EDDV_DEL',
            'freq' => '120.40',
            'name' => 'Hannover Delivery',
          ],
          133 => [
            'callsign' => 'EDDV_F_APP',
            'freq' => '119.60',
            'name' => 'Hannover Director',
          ],
          134 => [
            'callsign' => 'EDDV_GND',
            'freq' => '121.95',
            'name' => 'Hannover Ground',
          ],
          135 => [
            'callsign' => 'EDDV_TWR',
            'freq' => '120.17',
            'name' => 'Hannover Tower',
          ],
          136 => [
            'callsign' => 'EDDW_APP',
            'freq' => '124.80',
            'name' => 'Bremen Radar (ALEL Aller East Low)',
          ],
          137 => [
            'callsign' => 'EDDW_ATIS',
            'freq' => '120.12',
            'name' => 'Bremen ATIS',
          ],
          138 => [
            'callsign' => 'EDDW_DEL',
            'freq' => '134.82',
            'name' => 'Bremen Delivery',
          ],
          139 => [
            'callsign' => 'EDDW_F_APP',
            'freq' => '125.85',
            'name' => 'Bremen Director',
          ],
          140 => [
            'callsign' => 'EDDW_GND',
            'freq' => '121.75',
            'name' => 'Bremen Ground',
          ],
          141 => [
            'callsign' => 'EDDW_TWR',
            'freq' => '120.32',
            'name' => 'Bremen Tower',
          ],
          142 => [
            'callsign' => 'EDEH_I_TWR',
            'freq' => '132.05',
            'name' => 'Herrenteich Info',
          ],
          143 => [
            'callsign' => 'EDEL_I_TWR',
            'freq' => '122.87',
            'name' => 'Langenlonsheim Info',
          ],
          144 => [
            'callsign' => 'EDER_I_TWR',
            'freq' => '118.65',
            'name' => 'Wasserkuppe Info',
          ],
          145 => [
            'callsign' => 'EDFA_I_TWR',
            'freq' => '121.02',
            'name' => 'Anspach Info',
          ],
          146 => [
            'callsign' => 'EDFB_I_TWR',
            'freq' => '120.42',
            'name' => 'Reichelsheim Info',
          ],
          147 => [
            'callsign' => 'EDFC_I_TWR',
            'freq' => '132.42',
            'name' => 'Aschaffenburg Info',
          ],
          148 => [
            'callsign' => 'EDFE_GND',
            'freq' => '121.72',
            'name' => 'Egelsbach Apron',
          ],
          149 => [
            'callsign' => 'EDFE_I_TWR',
            'freq' => '118.40',
            'name' => 'Egelsbach Info',
          ],
          150 => [
            'callsign' => 'EDFG_I_TWR',
            'freq' => '123.05',
            'name' => 'Gelnhausen Info',
          ],
          151 => [
            'callsign' => 'EDFH_APP',
            'freq' => '125.60',
            'name' => 'Langen Radar (EIF Eifel)',
          ],
          152 => [
            'callsign' => 'EDFH_ATIS',
            'freq' => '120.90',
            'name' => 'Hahn ATIS',
          ],
          153 => [
            'callsign' => 'EDFH_GND',
            'freq' => '121.97',
            'name' => 'Hahn Ground',
          ],
          154 => [
            'callsign' => 'EDFH_TWR',
            'freq' => '119.65',
            'name' => 'Hahn Tower',
          ],
          155 => [
            'callsign' => 'EDFM_APP',
            'freq' => '129.35',
            'name' => 'Langen Radar (NKRL Neckar Low)',
          ],
          156 => [
            'callsign' => 'EDFM_ATIS',
            'freq' => '122.50',
            'name' => 'Mannheim ATIS',
          ],
          157 => [
            'callsign' => 'EDFM_TWR',
            'freq' => '129.77',
            'name' => 'Mannheim Tower',
          ],
          158 => [
            'callsign' => 'EDFQ_APP',
            'freq' => '124.72',
            'name' => 'Langen Radar (GIN Giessen)',
          ],
          159 => [
            'callsign' => 'EDFQ_ATIS',
            'freq' => '118.82',
            'name' => 'Allendorf ATIS',
          ],
          160 => [
            'callsign' => 'EDFQ_I_TWR',
            'freq' => '118.17',
            'name' => 'Allendorf Info',
          ],
          161 => [
            'callsign' => 'EDFU_I_TWR',
            'freq' => '122.37',
            'name' => 'Mainbullau Info',
          ],
          162 => [
            'callsign' => 'EDFV_I_TWR',
            'freq' => '124.60',
            'name' => 'Worms Info',
          ],
          163 => [
            'callsign' => 'EDFY_I_TWR',
            'freq' => '123.60',
            'name' => 'Elz Info',
          ],
          164 => [
            'callsign' => 'EDFZ_I_TWR',
            'freq' => '122.92',
            'name' => 'Mainz Info',
          ],
          165 => [
            'callsign' => 'EDGG_A_CTR',
            'freq' => '134.20',
            'name' => 'Langen Radar (HAB Hammelburg)',
          ],
          166 => [
            'callsign' => 'EDGG_B_CTR',
            'freq' => '127.05',
            'name' => 'Langen Radar (BAD Baden)',
          ],
          167 => [
            'callsign' => 'EDGG_CTR',
            'freq' => '135.72',
            'name' => 'Langen Radar (Complete)',
          ],
          168 => [
            'callsign' => 'EDGG_D_CTR',
            'freq' => '125.20',
            'name' => 'Langen Radar (DKB Dinkelsbühl)',
          ],
          169 => [
            'callsign' => 'EDGG_E_CTR',
            'freq' => '127.72',
            'name' => 'Langen Radar (HEF Hersfeld)',
          ],
          170 => [
            'callsign' => 'EDGG_F_CTR',
            'freq' => '128.95',
            'name' => 'Langen Information (FIS, South and East)',
          ],
          171 => [
            'callsign' => 'EDGG_G_CTR',
            'freq' => '124.42',
            'name' => 'Langen Radar (GED Gedern)',
          ],
          172 => [
            'callsign' => 'EDGG_H_CTR',
            'freq' => '129.17',
            'name' => 'Langen Radar (HMMM Hamm Medium)',
          ],
          173 => [
            'callsign' => 'EDGG_I_CTR',
            'freq' => '124.37',
            'name' => 'Langen Radar (KIR Kirn)',
          ],
          174 => [
            'callsign' => 'EDGG_K_CTR',
            'freq' => '125.67',
            'name' => 'Langen Radar (KNG König)',
          ],
          175 => [
            'callsign' => 'EDGG_L_CTR',
            'freq' => '131.30',
            'name' => 'Langen Radar (LBU Luburg)',
          ],
          176 => [
            'callsign' => 'EDGG_M_CTR',
            'freq' => '119.67',
            'name' => 'Langen Radar (MAN Main)',
          ],
          177 => [
            'callsign' => 'EDGG_N_CTR',
            'freq' => '127.50',
            'name' => 'Langen Radar (NKRH Neckar High)',
          ],
          178 => [
            'callsign' => 'EDGG_P_CTR',
            'freq' => '135.65',
            'name' => 'Langen Radar (PAD Paderborn High)',
          ],
          179 => [
            'callsign' => 'EDGG_R_CTR',
            'freq' => '124.47',
            'name' => 'Langen Radar (RUD Rüdesheim)',
          ],
          180 => [
            'callsign' => 'EDGG_S_CTR',
            'freq' => '125.40',
            'name' => 'Langen Radar (PSA Spessart)',
          ],
          181 => [
            'callsign' => 'EDGG_T_CTR',
            'freq' => '127.62',
            'name' => 'Langen Radar (TAU Taunus)',
          ],
          182 => [
            'callsign' => 'EDGG_U_CTR',
            'freq' => '123.52',
            'name' => 'Langen Information (FIS, West)',
          ],
          183 => [
            'callsign' => 'EDGG_V_CTR',
            'freq' => '119.15',
            'name' => 'Langen Information (FIS, North and East)',
          ],
          184 => [
            'callsign' => 'EDGG_W_CTR',
            'freq' => '129.87',
            'name' => 'Langen Information (FIS, North and West)',
          ],
          185 => [
            'callsign' => 'EDGG_X_CTR',
            'freq' => '130.97',
            'name' => 'Langen Radar (Special event)',
          ],
          186 => [
            'callsign' => 'EDGG_Z_CTR',
            'freq' => '120.57',
            'name' => 'Langen Radar (KTG Kitzingen)',
          ],
          187 => [
            'callsign' => 'EDGP_I_TWR',
            'freq' => '122.00',
            'name' => 'Oppenheim Information',
          ],
          188 => [
            'callsign' => 'EDGS_APP',
            'freq' => '124.90',
            'name' => 'Langen Radar (SIG Siegen)',
          ],
          189 => [
            'callsign' => 'EDGS_ATIS',
            'freq' => '128.70',
            'name' => 'Siegerland ATIS',
          ],
          190 => [
            'callsign' => 'EDGS_I_TWR',
            'freq' => '120.37',
            'name' => 'Siegerland Info',
          ],
          191 => [
            'callsign' => 'EDHE_I_TWR',
            'freq' => '122.70',
            'name' => 'Uetersen Info',
          ],
          192 => [
            'callsign' => 'EDHG_I_TWR',
            'freq' => '122.17',
            'name' => 'Lüneburg Info',
          ],
          193 => [
            'callsign' => 'EDHI_TWR',
            'freq' => '123.25',
            'name' => 'Finkenwerder Tower',
          ],
          194 => [
            'callsign' => 'EDHK_I_TWR',
            'freq' => '119.97',
            'name' => 'Kiel Info',
          ],
          195 => [
            'callsign' => 'EDHL_ATIS',
            'freq' => '119.92',
            'name' => 'Lübeck ATIS',
          ],
          196 => [
            'callsign' => 'EDHL_GND',
            'freq' => '121.77',
            'name' => 'Lübeck Ground',
          ],
          197 => [
            'callsign' => 'EDHL_TWR',
            'freq' => '128.70',
            'name' => 'Lübeck Tower',
          ],
          198 => [
            'callsign' => 'EDJA_APP',
            'freq' => '129.45',
            'name' => 'München Radar (LCH Lech)',
          ],
          199 => [
            'callsign' => 'EDJA_ATIS',
            'freq' => '118.85',
            'name' => 'Memmingen ATIS',
          ],
          200 => [
            'callsign' => 'EDJA_GND',
            'freq' => '121.67',
            'name' => 'Memmingen Ground',
          ],
          201 => [
            'callsign' => 'EDJA_TWR',
            'freq' => '126.85',
            'name' => 'Memmingen Tower',
          ],
          202 => [
            'callsign' => 'EDKA_I_TWR',
            'freq' => '122.87',
            'name' => 'Aachen Info',
          ],
          203 => [
            'callsign' => 'EDKB_I_TWR',
            'freq' => '135.15',
            'name' => 'Bonn-Hangelar Info',
          ],
          204 => [
            'callsign' => 'EDKF_I_TWR',
            'freq' => '123.65',
            'name' => 'Bergneustadt Info',
          ],
          205 => [
            'callsign' => 'EDKL_I_TWR',
            'freq' => '122.42',
            'name' => 'Leverkusen Info',
          ],
          206 => [
            'callsign' => 'EDKM_I_TWR',
            'freq' => '122.05',
            'name' => 'Meschede Info',
          ],
          207 => [
            'callsign' => 'EDLD_I_TWR',
            'freq' => '122.70',
            'name' => 'Dinslaken Info',
          ],
          208 => [
            'callsign' => 'EDLE_I_TWR',
            'freq' => '119.75',
            'name' => 'Essen/Mülheim Info',
          ],
          209 => [
            'callsign' => 'EDLI_I_TWR',
            'freq' => '118.35',
            'name' => 'Bielefeld Info',
          ],
          210 => [
            'callsign' => 'EDLM_I_TWR',
            'freq' => '122.00',
            'name' => 'Marl Info',
          ],
          211 => [
            'callsign' => 'EDLN_GND',
            'freq' => '121.92',
            'name' => 'Mönchengladbach Ground',
          ],
          212 => [
            'callsign' => 'EDLN_TWR',
            'freq' => '118.12',
            'name' => 'Mönchengladbach Tower',
          ],
          213 => [
            'callsign' => 'EDLO_I_TWR',
            'freq' => '122.17',
            'name' => 'Oerlinghausen Info',
          ],
          214 => [
            'callsign' => 'EDLP_APP',
            'freq' => '125.22',
            'name' => 'Langen Radar (PADL Paderborn Low)',
          ],
          215 => [
            'callsign' => 'EDLP_ATIS',
            'freq' => '125.72',
            'name' => 'Paderborn ATIS',
          ],
          216 => [
            'callsign' => 'EDLP_GND',
            'freq' => '121.92',
            'name' => 'Paderborn Ground',
          ],
          217 => [
            'callsign' => 'EDLP_TWR',
            'freq' => '133.37',
            'name' => 'Paderborn Tower',
          ],
          218 => [
            'callsign' => 'EDLT_I_TWR',
            'freq' => '122.85',
            'name' => 'Telgte Info',
          ],
          219 => [
            'callsign' => 'EDLV_ATIS',
            'freq' => '124.45',
            'name' => 'Niederrhein ATIS',
          ],
          220 => [
            'callsign' => 'EDLV_TWR',
            'freq' => '129.40',
            'name' => 'Niederrhein Tower',
          ],
          221 => [
            'callsign' => 'EDLW_ATIS',
            'freq' => '125.12',
            'name' => 'Dortmund ATIS',
          ],
          222 => [
            'callsign' => 'EDLW_GND',
            'freq' => '121.82',
            'name' => 'Dortmund Ground',
          ],
          223 => [
            'callsign' => 'EDLW_TWR',
            'freq' => '134.17',
            'name' => 'Dortmund Tower',
          ],
          224 => [
            'callsign' => 'EDMA_ATIS',
            'freq' => '124.57',
            'name' => 'Augsburg ATIS',
          ],
          225 => [
            'callsign' => 'EDMA_TWR',
            'freq' => '124.97',
            'name' => 'Augsburg Tower',
          ],
          226 => [
            'callsign' => 'EDMB_I_TWR',
            'freq' => '122.75',
            'name' => 'Biberach Info',
          ],
          227 => [
            'callsign' => 'EDME_ATIS',
            'freq' => '125.07',
            'name' => 'Eggenfelden ATIS',
          ],
          228 => [
            'callsign' => 'EDME_I_TWR',
            'freq' => '120.30',
            'name' => 'Eggenfelden Info',
          ],
          229 => [
            'callsign' => 'EDMJ_I_TWR',
            'freq' => '122.42',
            'name' => 'Jesenwang Info',
          ],
          230 => [
            'callsign' => 'EDML_I_TWR',
            'freq' => '129.80',
            'name' => 'Landshut Info',
          ],
          231 => [
            'callsign' => 'EDMM_A_CTR',
            'freq' => '129.10',
            'name' => 'München Radar (ALB Allersberg)',
          ],
          232 => [
            'callsign' => 'EDMM_C_CTR',
            'freq' => '133.67',
            'name' => 'München Radar (CHI Chiem)',
          ],
          233 => [
            'callsign' => 'EDMM_CTR',
            'freq' => '124.05',
            'name' => 'München Radar (Complete)',
          ],
          234 => [
            'callsign' => 'EDMM_E_CTR',
            'freq' => '129.55',
            'name' => 'München Radar (EGG Eggenfelden)',
          ],
          235 => [
            'callsign' => 'EDMM_F_CTR',
            'freq' => '124.82',
            'name' => 'München Radar (FRKH Franken High)',
          ],
          236 => [
            'callsign' => 'EDMM_I_CTR',
            'freq' => '120.65',
            'name' => 'München Information (FIS)',
          ],
          237 => [
            'callsign' => 'EDMM_K_CTR',
            'freq' => '134.15',
            'name' => 'München Radar (KPT Kempten)',
          ],
          238 => [
            'callsign' => 'EDMM_N_CTR',
            'freq' => '126.45',
            'name' => 'München Radar (NDG Nördlingen)',
          ],
          239 => [
            'callsign' => 'EDMM_R_CTR',
            'freq' => '132.55',
            'name' => 'München Radar (RDG Roding)',
          ],
          240 => [
            'callsign' => 'EDMM_S_CTR',
            'freq' => '131.02',
            'name' => 'München Radar (SASH Sachsen High)',
          ],
          241 => [
            'callsign' => 'EDMM_T_CTR',
            'freq' => '133.57',
            'name' => 'München Radar (TRGH Thüringen High)',
          ],
          242 => [
            'callsign' => 'EDMO_TWR',
            'freq' => '119.55',
            'name' => 'Oberpfaffenhofen Tower',
          ],
          243 => [
            'callsign' => 'EDMS_ATIS',
            'freq' => '135.52',
            'name' => 'Straubing ATIS',
          ],
          244 => [
            'callsign' => 'EDMS_I_TWR',
            'freq' => '127.15',
            'name' => 'Straubing Info',
          ],
          245 => [
            'callsign' => 'EDMT_I_TWR',
            'freq' => '122.82',
            'name' => 'Tannheim Info',
          ],
          246 => [
            'callsign' => 'EDMV_I_TWR',
            'freq' => '119.17',
            'name' => 'Vilshofen Info',
          ],
          247 => [
            'callsign' => 'EDNC_I_TWR',
            'freq' => '118.35',
            'name' => 'Beilngries Info',
          ],
          248 => [
            'callsign' => 'EDNL_I_TWR',
            'freq' => '122.87',
            'name' => 'Leutkirch Info',
          ],
          249 => [
            'callsign' => 'EDNX_I_TWR',
            'freq' => '129.40',
            'name' => 'Schleissheim Info',
          ],
          250 => [
            'callsign' => 'EDNY_ATIS',
            'freq' => '129.60',
            'name' => 'Friedrichshafen ATIS',
          ],
          251 => [
            'callsign' => 'EDNY_TWR',
            'freq' => '120.07',
            'name' => 'Friedrichshafen Tower',
          ],
          252 => [
            'callsign' => 'EDOP_TWR',
            'freq' => '128.90',
            'name' => 'Parchim Tower',
          ],
          253 => [
            'callsign' => 'EDPA_I_TWR',
            'freq' => '121.40',
            'name' => 'Aalen Info',
          ],
          254 => [
            'callsign' => 'EDPH_I_TWR',
            'freq' => '135.42',
            'name' => 'Schwabach Info',
          ],
          255 => [
            'callsign' => 'EDPQ_I_TWR',
            'freq' => '123.00',
            'name' => 'Schmidgaden Info',
          ],
          256 => [
            'callsign' => 'EDPU_I_TWR',
            'freq' => '122.20',
            'name' => 'Bartholomä Segelflug',
          ],
          257 => [
            'callsign' => 'EDPY_I_TWR',
            'freq' => '122.00',
            'name' => 'Ellwangen Info',
          ],
          258 => [
            'callsign' => 'EDQC_ATIS',
            'freq' => '120.10',
            'name' => 'Coburg ATIS',
          ],
          259 => [
            'callsign' => 'EDQC_I_TWR',
            'freq' => '128.67',
            'name' => 'Coburg Info',
          ],
          260 => [
            'callsign' => 'EDQD_I_TWR',
            'freq' => '127.52',
            'name' => 'Bayreuth Info',
          ],
          261 => [
            'callsign' => 'EDQE_I_TWR',
            'freq' => '130.77',
            'name' => 'Feuerstein Info',
          ],
          262 => [
            'callsign' => 'EDQH_I_TWR',
            'freq' => '122.85',
            'name' => 'Herzogenaurach Info',
          ],
          263 => [
            'callsign' => 'EDQK_I_TWR',
            'freq' => '118.52',
            'name' => 'Kulmbach Info',
          ],
          264 => [
            'callsign' => 'EDQM_TWR',
            'freq' => '124.35',
            'name' => 'Hof Tower',
          ],
          265 => [
            'callsign' => 'EDQN_I_TWR',
            'freq' => '118.92',
            'name' => 'Neustadt/Aisch Info',
          ],
          266 => [
            'callsign' => 'EDQP_I_TWR',
            'freq' => '127.45',
            'name' => 'Rosenthal Info',
          ],
          267 => [
            'callsign' => 'EDQT_I_TWR',
            'freq' => '119.80',
            'name' => 'Hassfurt Info',
          ],
          268 => [
            'callsign' => 'EDQW_I_TWR',
            'freq' => '120.25',
            'name' => 'Weiden Info',
          ],
          269 => [
            'callsign' => 'EDQY_I_TWR',
            'freq' => '129.80',
            'name' => 'Steinrücken Info',
          ],
          270 => [
            'callsign' => 'EDRA_I_TWR',
            'freq' => '122.35',
            'name' => 'Neuenahr Info',
          ],
          271 => [
            'callsign' => 'EDRF_I_TWR',
            'freq' => '122.40',
            'name' => 'Dürkheim Info',
          ],
          272 => [
            'callsign' => 'EDRJ_I_TWR',
            'freq' => '122.60',
            'name' => 'Saarlouis Info',
          ],
          273 => [
            'callsign' => 'EDRK_I_TWR',
            'freq' => '122.65',
            'name' => 'Koblenz Info',
          ],
          274 => [
            'callsign' => 'EDRM_I_TWR',
            'freq' => '123.00',
            'name' => 'Traben-Trarbach Info',
          ],
          275 => [
            'callsign' => 'EDRY_I_TWR',
            'freq' => '118.07',
            'name' => 'Speyer Info',
          ],
          276 => [
            'callsign' => 'EDRZ_TWR',
            'freq' => '123.82',
            'name' => 'Zweibrücken Tower (Info when CTR not active)',
          ],
          277 => [
            'callsign' => 'EDSB_ATIS',
            'freq' => '121.27',
            'name' => 'Baden ATIS',
          ],
          278 => [
            'callsign' => 'EDSB_GND',
            'freq' => '121.82',
            'name' => 'Baden Ground',
          ],
          279 => [
            'callsign' => 'EDSB_TWR',
            'freq' => '134.10',
            'name' => 'Baden Tower',
          ],
          280 => [
            'callsign' => 'EDSH_I_TWR',
            'freq' => '126.50',
            'name' => 'Backnang Info',
          ],
          281 => [
            'callsign' => 'EDTC_I_TWR',
            'freq' => '128.37',
            'name' => 'Bruchsal Info',
          ],
          282 => [
            'callsign' => 'EDTD_I_TWR',
            'freq' => '124.25',
            'name' => 'Donaueschingen Info',
          ],
          283 => [
            'callsign' => 'EDTF_I_TWR',
            'freq' => '118.25',
            'name' => 'Freiburg Info',
          ],
          284 => [
            'callsign' => 'EDTH_I_TWR',
            'freq' => '123.02',
            'name' => 'Heubach Info',
          ],
          285 => [
            'callsign' => 'EDTL_TWR',
            'freq' => '125.17',
            'name' => 'Lahr Tower (Info when CTR not active)',
          ],
          286 => [
            'callsign' => 'EDTM_I_TWR',
            'freq' => '135.17',
            'name' => 'Mengen Info',
          ],
          287 => [
            'callsign' => 'EDTO_I_TWR',
            'freq' => '119.75',
            'name' => 'Offenburg Info',
          ],
          288 => [
            'callsign' => 'EDTQ_I_TWR',
            'freq' => '123.65',
            'name' => 'Pattonville Info',
          ],
          289 => [
            'callsign' => 'EDTS_I_TWR',
            'freq' => '122.85',
            'name' => 'Schwenningen Info',
          ],
          290 => [
            'callsign' => 'EDTU_I_TWR',
            'freq' => '123.60',
            'name' => 'Saulgau Info',
          ],
          291 => [
            'callsign' => 'EDTW_I_TWR',
            'freq' => '123.65',
            'name' => 'Winzeln Info',
          ],
          292 => [
            'callsign' => 'EDTY_ATIS',
            'freq' => '133.87',
            'name' => 'Schwäbisch Hall ATIS',
          ],
          293 => [
            'callsign' => 'EDTY_I_TWR',
            'freq' => '129.22',
            'name' => 'Schwäbisch Hall Info',
          ],
          294 => [
            'callsign' => 'EDTZ_I_TWR',
            'freq' => '124.35',
            'name' => 'Konstanz Info',
          ],
          295 => [
            'callsign' => 'EDUB_I_TWR',
            'freq' => '130.12',
            'name' => 'Briest Info',
          ],
          296 => [
            'callsign' => 'EDUU_CTR',
            'freq' => '132.32',
            'name' => 'Rhein Radar (Complete)',
          ],
          297 => [
            'callsign' => 'EDUU_D_CTR',
            'freq' => '132.72',
            'name' => 'Rhein Radar (DON Donau)',
          ],
          298 => [
            'callsign' => 'EDUU_E_CTR',
            'freq' => '128.07',
            'name' => 'Rhein Radar (East)',
          ],
          299 => [
            'callsign' => 'EDUU_L_CTR',
            'freq' => '127.30',
            'name' => 'Rhein Radar (ALP Alpen)',
          ],
          300 => [
            'callsign' => 'EDUU_N_CTR',
            'freq' => '132.77',
            'name' => 'Rhein Radar (NTM Nattenheim)',
          ],
          301 => [
            'callsign' => 'EDUU_S_CTR',
            'freq' => '128.97',
            'name' => 'Rhein Radar (South)',
          ],
          302 => [
            'callsign' => 'EDUU_T_CTR',
            'freq' => '132.40',
            'name' => 'Rhein Radar (TGO Tango)',
          ],
          303 => [
            'callsign' => 'EDUU_W_CTR',
            'freq' => '133.65',
            'name' => 'Rhein Radar (West)',
          ],
          304 => [
            'callsign' => 'EDUW_I_TWR',
            'freq' => '130.12',
            'name' => 'Tutow Info',
          ],
          305 => [
            'callsign' => 'EDVC_I_TWR',
            'freq' => '123.65',
            'name' => 'Arloh Info (Celle)',
          ],
          306 => [
            'callsign' => 'EDVE_ATIS',
            'freq' => '134.45',
            'name' => 'Braunschweig ATIS',
          ],
          307 => [
            'callsign' => 'EDVE_TWR',
            'freq' => '120.05',
            'name' => 'Braunschweig Tower',
          ],
          308 => [
            'callsign' => 'EDVI_I_TWR',
            'freq' => '130.27',
            'name' => 'Höxter Info',
          ],
          309 => [
            'callsign' => 'EDVK_ATIS',
            'freq' => '129.20',
            'name' => 'Kassel ATIS',
          ],
          310 => [
            'callsign' => 'EDVK_GND',
            'freq' => '121.90',
            'name' => 'Kassel Ground',
          ],
          311 => [
            'callsign' => 'EDVK_TWR',
            'freq' => '118.10',
            'name' => 'Kassel Tower',
          ],
          312 => [
            'callsign' => 'EDVY_I_TWR',
            'freq' => '122.37',
            'name' => 'Porta Westfalica Info',
          ],
          313 => [
            'callsign' => 'EDWB_I_TWR',
            'freq' => '129.05',
            'name' => 'Bremerhaven Info',
          ],
          314 => [
            'callsign' => 'EDWE_I_TWR',
            'freq' => '118.60',
            'name' => 'Emden Info',
          ],
          315 => [
            'callsign' => 'EDWG_I_TWR',
            'freq' => '122.40',
            'name' => 'Wooge Info',
          ],
          316 => [
            'callsign' => 'EDWI_ATIS',
            'freq' => '124.32',
            'name' => 'Wilhelmshaven ATIS',
          ],
          317 => [
            'callsign' => 'EDWI_I_TWR',
            'freq' => '129.25',
            'name' => 'Wilhelmshaven Info',
          ],
          318 => [
            'callsign' => 'EDWJ_I_TWR',
            'freq' => '120.50',
            'name' => 'Juist Info',
          ],
          319 => [
            'callsign' => 'EDWL_I_TWR',
            'freq' => '122.02',
            'name' => 'Langeoog Info',
          ],
          320 => [
            'callsign' => 'EDWN_I_TWR',
            'freq' => '122.65',
            'name' => 'Nordhorn Info',
          ],
          321 => [
            'callsign' => 'EDWR_I_TWR',
            'freq' => '123.00',
            'name' => 'Borkum Info',
          ],
          322 => [
            'callsign' => 'EDWS_I_TWR',
            'freq' => '120.50',
            'name' => 'Norddeich Info',
          ],
          323 => [
            'callsign' => 'EDWW_A_CTR',
            'freq' => '123.92',
            'name' => 'Bremen Radar (ALEH Aller East High)',
          ],
          324 => [
            'callsign' => 'EDWW_B_CTR',
            'freq' => '123.22',
            'name' => 'Bremen Radar (BOR Börde)',
          ],
          325 => [
            'callsign' => 'EDWW_CTR',
            'freq' => '125.02',
            'name' => 'Bremen Radar (Complete)',
          ],
          326 => [
            'callsign' => 'EDWW_D_CTR',
            'freq' => '128.75',
            'name' => 'Bremen Radar (DST Deister)',
          ],
          327 => [
            'callsign' => 'EDWW_E_CTR',
            'freq' => '120.22',
            'name' => 'Bremen Radar (EID Eider)',
          ],
          328 => [
            'callsign' => 'EDWW_I_CTR',
            'freq' => '119.82',
            'name' => 'Bremen Information (FIS)',
          ],
          329 => [
            'callsign' => 'EDWW_M_CTR',
            'freq' => '124.17',
            'name' => 'Bremen Radar (MRZ Müritz)',
          ],
          330 => [
            'callsign' => 'EDWY_I_TWR',
            'freq' => '122.60',
            'name' => 'Norderney Info',
          ],
          331 => [
            'callsign' => 'EDXF_I_TWR',
            'freq' => '122.85',
            'name' => 'Flensburg Info',
          ],
          332 => [
            'callsign' => 'EDXH_I_TWR',
            'freq' => '122.45',
            'name' => 'Helgoland Info',
          ],
          333 => [
            'callsign' => 'EDXO_I_TWR',
            'freq' => '129.77',
            'name' => 'St. Peter  Info',
          ],
          334 => [
            'callsign' => 'EDXP_I_TWR',
            'freq' => '122.40',
            'name' => 'Harle Info',
          ],
          335 => [
            'callsign' => 'EDXW_ATIS',
            'freq' => '118.42',
            'name' => 'Sylt ATIS',
          ],
          336 => [
            'callsign' => 'EDXW_TWR',
            'freq' => '119.75',
            'name' => 'Sylt Tower',
          ],
          337 => [
            'callsign' => 'EDYY_C_CTR',
            'freq' => '133.95',
            'name' => 'Maastricht Radar (CE Celle)',
          ],
          338 => [
            'callsign' => 'EDYY_H_CTR',
            'freq' => '120.95',
            'name' => 'Maastricht Radar (HO Holstein)',
          ],
          339 => [
            'callsign' => 'EDYY_J_CTR',
            'freq' => '134.70',
            'name' => 'Maastricht Radar (JE Jever)',
          ],
          340 => [
            'callsign' => 'EDYY_M_CTR',
            'freq' => '133.85',
            'name' => 'Maastricht Radar (MN Münster)',
          ],
          341 => [
            'callsign' => 'EDYY_O_CTR',
            'freq' => '132.85',
            'name' => 'Maastricht Radar (Olno - high traffic loads)',
          ],
          342 => [
            'callsign' => 'EDYY_R_CTR',
            'freq' => '132.62',
            'name' => 'Maastricht Radar (RH Ruhr)',
          ],
          343 => [
            'callsign' => 'EDYY_S_CTR',
            'freq' => '131.37',
            'name' => 'Maastricht Radar (SO Solling)',
          ],
          344 => [
            'callsign' => 'ETAR_GND',
            'freq' => '121.77',
            'name' => 'Ramstein Ground',
          ],
          345 => [
            'callsign' => 'ETAR_TWR',
            'freq' => '123.55',
            'name' => 'Ramstein Tower',
          ],
          346 => [
            'callsign' => 'ETHN_APP',
            'freq' => '123.30',
            'name' => 'Stetten Radar',
          ],
          347 => [
            'callsign' => 'ETHN_I_TWR',
            'freq' => '119.77',
            'name' => 'Stetten Info (when CTR is not active)',
          ],
          348 => [
            'callsign' => 'ETHN_TWR',
            'freq' => '122.10',
            'name' => 'Stetten Tower',
          ],
          349 => [
            'callsign' => 'ETHR_TWR',
            'freq' => '122.10',
            'name' => 'Roth Tower',
          ],
          350 => [
            'callsign' => 'ETIC_TWR',
            'freq' => '122.10',
            'name' => 'Grafenwöhr Tower',
          ],
          351 => [
            'callsign' => 'ETNG_APP',
            'freq' => '123.72',
            'name' => 'Frisbee Radar (Geilenkirchen Arrival)',
          ],
          352 => [
            'callsign' => 'ETNG_TWR',
            'freq' => '120.05',
            'name' => 'Frisbee Tower',
          ],
          353 => [
            'callsign' => 'ETNH_APP',
            'freq' => '123.30',
            'name' => 'Hohn Radar',
          ],
          354 => [
            'callsign' => 'ETNH_F_APP',
            'freq' => '125.60',
            'name' => 'Hohn Precision',
          ],
          355 => [
            'callsign' => 'ETNH_TWR',
            'freq' => '122.10',
            'name' => 'Hohn Tower',
          ],
          356 => [
            'callsign' => 'ETNL_APP',
            'freq' => '133.62',
            'name' => 'Laage Radar',
          ],
          357 => [
            'callsign' => 'ETNL_TWR',
            'freq' => '118.42',
            'name' => 'Laage Tower',
          ],
          358 => [
            'callsign' => 'ETNN_APP',
            'freq' => '123.30',
            'name' => 'Noervenich Radar',
          ],
          359 => [
            'callsign' => 'ETNN_TWR',
            'freq' => '122.10',
            'name' => 'Noervenich Tower',
          ],
          360 => [
            'callsign' => 'ETNT_APP',
            'freq' => '123.60',
            'name' => 'Wittmund Radar',
          ],
          361 => [
            'callsign' => 'ETNT_TWR',
            'freq' => '118.72',
            'name' => 'Wittmund Tower',
          ],
          362 => [
            'callsign' => 'ETNW_TWR',
            'freq' => '118.05',
            'name' => 'Wunstorf Tower',
          ],
          363 => [
            'callsign' => 'ETSA_APP',
            'freq' => '130.50',
            'name' => 'Landsberg Radar',
          ],
          364 => [
            'callsign' => 'ETSA_TWR',
            'freq' => '122.10',
            'name' => 'Landsberg Tower',
          ],
          365 => [
            'callsign' => 'ETSI_APP',
            'freq' => '120.60',
            'name' => 'Ingo Radar',
          ],
          366 => [
            'callsign' => 'ETSI_TWR',
            'freq' => '125.25',
            'name' => 'Ingo Tower',
          ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('navigation_stations')->truncate();
        $this->command->getOutput()->writeln('Truncated stations table.');

        $this->command->getOutput()->writeln('Starting seeding of new information...');
        $this->command->getOutput()->progressStart(count($this->stations));
        foreach ($this->stations as $s) {
            $ns = new \App\Models\Navigation\Station();
            $ns->name = $s['name'];
            $ns->ident = $s['callsign'];
            $ns->frequency = $s['freq'];
            $ns->description = '';
            $ns->bookable = \Illuminate\Support\Str::endsWith($s['callsign'], 'ATIS') ? false : true;
            $ns->atis = \Illuminate\Support\Str::endsWith($s['callsign'], 'ATIS') ? true : false;
            $ns->save();
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->getOutput()->writeln('Finished seeding.');

    }
}
