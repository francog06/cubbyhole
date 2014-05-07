<div class="site-wrapper">
    <div class="">
        <div class="cover-container">
            <!-- Menu -->
            <div class="masthead clearfix">
                <div class="">
                    <h3 class="masthead-brand"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></h3>
                    <ul class="nav nav-pills pull-right" style="margin-top:20px;">
                        <li><a href="/">Retour au site</a></li>
                        <li class="active"><a href="/admin/index">Accueil</a></li>
                        <li><a href="/home/price">Users</a></li>
                        <li><a href="/home/download">Plans</a></li>
                    </ul>
                </div>
            </div>

            <div class="inner cover admin">
               <h1>Actions rapides</h1>
               <ul id="adminGridPanel">
                    <li class="adminGrid">
                        <a href="/admin/users">
                            <img src="<?=img("Admin/home/user.png")?>" alt="user" />
                            <p>Gestion <br>Utilisateurs</p>
                        </a>
                    </li>
                    <li class="adminGrid">
                        <a href="/admin/plans">
                            <img src="<?=img("Admin/home/plan.png")?>" alt="plan" />
                            <p>Gestion <br>Plans</p>
                        </a>
                    </li>
                    <li class="adminGrid">
                        <a href="/admin/plans">
                            <img src="<?=img("Admin/home/settings.png")?>" alt="plan" />
                            <p>Configuration <br>Générale</p>
                        </a>
                    </li>
               </ul>

               <br />

                <h1 id="stats">Statistiques <span class="caret"></span></h1>
                <script type="text/javascript">
                $(document).ready(function(){
                    $('#stats').click(function(){
                        $("#adminChart").toggleClass("hidden");
                        $("#adminPie").toggleClass("hidden");
                    });
                });
                
                </script>
                <div id="adminChart" class="hidden" style="width:40%;float:left; margin-right:10%;"></div>
                <div id="adminPie" class="hidden" style="float:left;width:30%;"></div>

                </div>


              
               
               <script type="text/javascript">
                $(document).ready(function() {
                    $('#adminChart').highcharts({
                        chart: {
                            type: 'area'
                        },
                        title: {
                            text: 'Nombre de visites annuelles'
                        },
                        subtitle: {
                            text: 'Source: <a href="http://google.fr">'+
                                'Moi</a>'
                        },
                        xAxis: {
                            allowDecimals: false,
                            labels: {
                                formatter: function() {
                                    return this.value; // clean, unformatted number for year
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Quantité'
                            },
                            labels: {
                                formatter: function() {
                                    return this.value / 1000 +'k';
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '{series.name} produced <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
                        },
                        plotOptions: {
                            area: {
                                pointStart: 1940,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Label 1',
                            data: [null, null, null, null, null, 6 , 11, 32, 110, 235, 369, 640,
                                1005, 1436, 2063, 3057, 4618, 6444, 9822, 15468, 20434, 24126,
                                27387, 29459, 31056, 31982, 32040, 31233, 29224, 27342, 26662,
                                26956, 27912, 28999, 28965, 27826, 25579, 25722, 24826, 24605,
                                24304, 23464, 23708, 24099, 24357, 24237, 24401, 24344, 23586,
                                22380, 21004, 17287, 14747, 13076, 12555, 12144, 11009, 10950,
                                10871, 10824, 10577, 10527, 10475, 10421, 10358, 10295, 10104 ]
                        }, {
                            name: 'Label 2',
                            data: [null, null, null, null, null, null, null , null , null ,null,
                            5, 25, 50, 120, 150, 200, 426, 660, 869, 1060, 1605, 2471, 3322,
                            4238, 5221, 6129, 7089, 8339, 9399, 10538, 11643, 13092, 14478,
                            15915, 17385, 19055, 21205, 23044, 25393, 27935, 30062, 32049,
                            33952, 35804, 37431, 39197, 45000, 43000, 41000, 39000, 37000,
                            35000, 33000, 31000, 29000, 27000, 25000, 24000, 23000, 22000,
                            21000, 20000, 19000, 18000, 18000, 17000, 16000]
                        }]
                    });
                });
               </script>

               
               <script type="text/javascript">
                $(function () {
                    $('#adminPie').highcharts({
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false
                        },
                        title: {
                            text: 'Browser market shares at a specific website, 2014'
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                    style: {
                                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                    }
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            name: 'Browser share',
                            data: [
                                ['Firefox',   45.0],
                                ['IE',       26.8],
                                {
                                    name: 'Chrome',
                                    y: 12.8,
                                    sliced: true,
                                    selected: true
                                },
                                ['Safari',    8.5],
                                ['Opera',     6.2],
                                ['Others',   0.7]
                            ]
                        }]
                    });
                });
    
               </script>
            </div>

            <div class="mastfoot">
                <div class="inner">
                    <p>Cubbyhole powered baby !</p>
                </div>
            </div>
        </div>
    </div>
</div>