(function($){
    var chart;

    function mainChart(id, data){
        chart = new Highcharts.Chart({
            chart: {
                renderTo: id
            },
            credits: {
                enabled: false
            },
            legend: {
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 100
            },
            title: {
                text: ''
            },
            tooltip: {
                formatter: function () {
                    if (this.series.name == 'Accumulated') {
                        return this.y + '%';
                    }
                    return this.x + '<br/>' + '<b> ' + this.y.toString().replace('.', ',') + ' </b>';
                }
            },
            xAxis: {
                categories: ['18м назад', '16м назад', '14м назад', '12м назад', '10м назад', '8м назад', '6м назад', '4м назад', '2м назад', 'Сейчас']
            },
            yAxis: [{
                title: {
                    text: ''
                }
            }, {
                labels: {
                    formatter: function () {
                        return this.value + '%';
                    }
                },
                max: 100,
                min: 0,
                opposite: true,
                title: {
                    text: ''
                }
            }],
            series: data
        });
        $('.highcharts-legend > * > * > *').last().click();
        $('.highcharts-legend > * > * > *:nth-of-type(3)').click();
        $('.highcharts-legend > * > * > *:nth-of-type(4)').click();
    }
    function createMiniChart(id, data) {
        $('#'+id+' .mini_chart').html(data);
    }

    $(document).ready(function () {
        var chart       = $('#chart'),
            temperature = {
                chamber     : [],
                flow        : [],
                street      : [],
                processing  : []
            },
            humidity    = [],
            limit       = chart.data('chart-limit');

        var i;
        for (i = 0; i < limit; i++) {
            temperature.chamber.push(chart.data('chart-ia13-'+i)/10);
            temperature.flow.push(chart.data('chart-ia9-'+i)/10);
            temperature.street.push(chart.data('chart-ia8-'+i)/10);
            temperature.processing.push(chart.data('chart-ia7-'+i)/10);
            humidity.push(chart.data('chart-ia14-'+i)/10);
        }

        mainChart('chart', [{
            data: humidity,
            name: 'Влажность',
            type: 'column',
            yAxis: 1,
            id: 'accumulated'
        }, {
            data: temperature.chamber,
            name: 'Температура в Камере',
            type: 'spline'
        }, {
            data: temperature.flow,
            name: 'Температура подачи',
            type: 'spline'
        }, {
            data: temperature.processing,
            name: 'Температура обратки',
            type: 'spline'
        }, {
            data: temperature.street,
            name: 'Температура на Улице',
            type: 'spline'
        }]);

        createMiniChart('temperature_chamber', temperature.chamber[temperature.chamber.length - 1]+'&deg; C');
        createMiniChart('humidity', humidity[humidity.length - 1]+' &#37;');
        createMiniChart('temperature_flow', temperature.flow[temperature.flow.length - 1]+'&deg; C');
        createMiniChart('temperature_processing', temperature.processing[temperature.processing.length - 1]+'&deg; C');
    });
})(jQuery);