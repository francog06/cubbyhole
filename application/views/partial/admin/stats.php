<div class="inner cover admin">
    <h1>Statistiques</h1>
    <form class="form-inline" role="form" action="/admin/stats" method="post" style="margin-bottom:15px;">
        <div class="form-group">
            <label for="from">Du : </label> <input id="from" class="form-control" type="date" name="from" value="<?= $from; ?>" />
        </div>
        <div class="form-group">
            <label for="to">au : </label> <input id="to" class="form-control" type="date" name="to" value="<?= $to; ?>" /> 
        </div>
        &nbsp; <button type="submit" class="btn btn-info">Créer rapport</button>
    </form>

    <script type="text/javascript">$.bootstrapSortable();</script>
    <table class="table table-bordered ">
        <tr>
            <td style="border:0;" colspan="2"><div style="height:200px" id="adminChart"></div></td>
        </tr>
        <tr>
            <td style="width:50%;padding-top:16px;">
                <h4 style="margin-top:0;text-align:center;font-family: 'Lucida Grande', 'Lucida Sans Unicode', Arial;padding-bottom:8px;">Les chiffres</h4>
                <table class="table table-bordered sortable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Total</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nombre d'utilisateurs</td>
                        <td><?= $nbUser ?></td>
                        <td>100%</td>
                    </tr>
                    <?php foreach($users_plan as $plan=>$nb): ?>
                        <tr>
                            <td>Utilisateurs avec le plan "<?= $plan; ?>"</td>
                            <td><?= $nb ?></td>
                            <td><?= @round(($nb/$nbUser)*100,1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </td>
            <td style="width:50%;"><div id="adminPie" style="height:250px;"></div></td>
        </tr>
        <tr>
            <td colspan="2">          
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <?php for($i=1;$i<=sizeof($nbPlans);$i++): ?>
                        <li class="<?= $i==1?"active":""; ?>"><a href="#usePlan<?= $i; ?>" data-toggle="tab"><?= $nbPlans[$i-1]["name"]; ?></a></li>
                    <?php endfor; ?>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <?php for($i=1;$i<=sizeof($nbPlans);$i++): ?>
                    <div class="tab-pane fade <?= $i==1?"in active":""; ?>" id="usePlan<?= $i; ?>">
                        <table>
                            <tr>
                                <td>
                                    <? if(isset($fsByUser[$nbPlans[$i-1]["id"]])) { ?>
                                        <div style="width:20%;height:180px;" id="adminPlansGauge<?= $i; ?>_1"></div>
                                    <? }else echo '<h4 style="text-align:center;font-family: "Lucida Grande", "Lucida Sans Unicode", Arial;padding-bottom:8px;">Aucune data à analyser</h4>'; ?>
                                </td>
                                <td>
                                   <div style="width:610px;height:180px;" id="adminChart_gauge<?= $i; ?>"></div>
                                </td>
                            </tr>
                        </table> 
                    </div>
                    <?php endfor; ?>
                </div>
            </td>
            
            
        </tr>
    </table>
    
    
</div>
<script type="text/javascript">
$(function () {
    var gaugeOptions = {
        chart: {
            type: 'solidgauge'
        },
        title: null,
        pane: {
            center: ['50%', '65%'],
            size: '130%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },
        tooltip: {
            enabled: false
        }, 
        yAxis: {
            stops: [
                [0.1, '#55BF3B'], // green
                [0.5, '#DDDF0D'], // yellow
                [0.9, '#DF5353'] // red
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 400,
            tickWidth: 0,
            title: {
                y: -100
            },
            labels: {
                y: 16
            }        
        },
        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    <?php for($i=1;$i<=sizeof($nbPlans);$i++): 
        if(isset($fsByUser[$nbPlans[$i-1]["id"]])):
    ?>
    $('#adminPlansGauge<?= $i; ?>_1').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: {
            min: 0,
            max: 100,     
        },
        title: {
            text: 'Utilisation de l\'espace'
        }, 
        credits: {
            enabled: false
        },
        series: [{
            name: 'Storage',
            data: [<?php 
                $data = 0;          
                    foreach($fsByUser[$nbPlans[$i-1]["id"]] as $user){ 
                        //echo "console.log('".$user["size"]." / ".$user["usableStorage"]."');";
                        $data+=round($user["size"]/$user["usableStorage"],2);
                    }
                    $data= round($data/sizeof($fsByUser[$nbPlans[$i-1]["id"]]),2)*100; 
                    echo $data;
                ?>],
            dataLabels: {
                format: '<div style="text-align:center"><span style="font-size:16px;color:' + 
                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' + 
                    '<span style="font-size:12px;color:silver">%</span></div>'
            },
            tooltip: {
                valueSuffix: '%'
            }
        }]
    }));

    //get the date selon input date
    var from = $("#from").val();
    from = from.split("-");
    if(from[1][0] == "0")
        from[1] = from[1][1];
    $('#adminChart_gauge<?= $i; ?>').highcharts({
        title:{
            text:"Utilisation du partage de fichiers"
        },
        chart: {
            zoomType: 'x'
        },
        xAxis: {
            type: 'datetime',
            maxZoom:24 * 3600 * 1000 * 31 // par mois

        },
        yAxis: {
            min:0
        },
        plotOptions: {
                area: {
                    marker: {
                        radius: 1
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
        tooltip:{
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e %b. %Y} : <b>{point.y}</b>'
        },
        series: [{
            name:"Nombre de téléchargements",
            <?php 
                $data="";
                $value = 0;
                foreach ($nbDownloads[$i] as $k => $v) {
                    $data .= $v.", ";
                }
                substr($data,0,-1);
            ?>
            data: [<?= $data; ?>],
            pointStart: Date.UTC(from[0], from[1]-1, from[2]),
            pointInterval: 24 * 3600 * 1000 // one day
            //pointInterval: 24 * 3600 * 1000 * 31 // par mois
        },
        {
            name:"Nombre de partages créés",
            <?php 
                $data="";
                foreach ($nbShares[$i] as $k => $v) {
                    $data .= $v.", ";
                }
                substr($data,0,-1);
            ?>
            data: [<?= $data; ?>],
            pointStart: Date.UTC(from[0], from[1]-1, from[2]),
            pointInterval: 24 * 3600 * 1000 // one day
            //pointInterval: 24 * 3600 * 1000 * 31 // par mois
        }]
    });
    
    <?php endif; endfor; ?>

});
$(function () {
    //get the date selon input date
    var from = $("#from").val();
    from = from.split("-");
    if(from[1][0] == "0")
        from[1] = from[1][1];
    $('#adminChart').highcharts({
        title:{
            text:"Nombre d'utilisateurs du service"
        },
        chart: {
            zoomType: 'x'
        },
        xAxis: {
            type: 'datetime',
            maxZoom:24 * 3600 * 1000 * 31 // par mois

        },
        yAxis: {
            min:0,
            title:{
                text:"Inscriptions"
            }
        },
        plotOptions: {
                area: {
                    marker: {
                        radius: 1
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
        tooltip:{
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e %b. %Y} : <b>{point.y}</b>'
        },
        series: [{
            name:"Nombre d'inscriptions cumulées",
            <?php 
                $data="";
                $value = 0;
                foreach ($users_reult as $k => $v) {
                    $value += $v;
                    $data .= $value.", ";
                }
                substr($data,0,-1);
            ?>
            data: [<?= $data; ?>],
            pointStart: Date.UTC(from[0], from[1]-1, from[2]),
            pointInterval: 24 * 3600 * 1000 // one day
            //pointInterval: 24 * 3600 * 1000 * 31 // par mois
        },
        {
            name:"Nombre d'inscriptions",
            <?php 
                $data="";
                foreach ($users_reult as $k => $v) {
                    $data .= $v.", ";
                }
                substr($data,0,-1);
            ?>
            data: [<?= $data; ?>],
            pointStart: Date.UTC(from[0], from[1]-1, from[2]),
            pointInterval: 24 * 3600 * 1000 // one day
            //pointInterval: 24 * 3600 * 1000 * 31 // par mois
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
            text: 'Localisation des utilisateurs'
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
            },
            area: {
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 2
                        }
                    },
                    threshold: null
                }
        },
        series: [{
            type: 'pie',
            name: 'Utilisateurs',
            data: [
                <?php 
                $data="";
                foreach ($users_country as $k=>$v) {
                    $data .= "['".$k."', ".$v."],";
                }
                substr($data,0,-1); echo $data;
                ?>

            ]
        }]
    });
});

</script>