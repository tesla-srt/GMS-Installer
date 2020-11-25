/**
 * Contains all colors used for graphing.
 */
class Colors {
    static cyan() {return "86, 156, 189";}
    static navy() {return "19,40,72";}
    static blue() {return "86, 156, 189";}
    static green() {return "2, 170, 58";}
    static brown() {return "128, 97, 30";}
    static orange() {return "199, 78, 31";}
    static red() {return '212, 61, 61';}
    static purple() {return '60, 24, 91';}
    static yellow() {return '245, 208, 1';}
    static getColor(col) {return "rgb(" +col+ ")";}
    static getAlphaColor(col) {return "rgba("+col+ ",0.4)";}
}

/**
 * Contains all functions that handle the graph.
 * @param {*serverDate - A function that returns a Date() object of the server date.} serverDate 
 */
function GraphHandler(serverDate)
{
    var graphValues = [0,0,0];
    function setGraphValues(vm1,vm2,vm3) {graphValues = [vm1,vm2,vm3];}
    function getGraphValues() {return graphValues;}
    
    function createGraph(name1, name2, name3){
        var datasets = create3Datasets(name1, name2, name3);
        var config = createRealtimeConfig(datasets);
        var ctx = document.getElementById('voltmeterGraph').getContext('2d');
        return new Chart(ctx, config);
    }
    function create3Datasets(name1, name2, name3) {
        return  [{
            label: name1,
            fill: false,
            //borderDash: [5, 5],
            borderColor: Colors.getColor(Colors.green()),
            data: [],
        },{
            label: name2,
            fill: false,
            //borderDash: [5, 5],
            borderColor: Colors.getColor(Colors.orange()),
            data: [],
        },{
            label: name3,
            fill: false,
            borderColor: Colors.getColor(Colors.blue()),
            data: [],
        }];
    }

    function createRealtimeConfig(dSets) {
        return {
            type:"line",
            data: {datasets: dSets},
            options: {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        type: 'realtime',
                        realtime: {
                            duration: 20000,
                            delay: 2000,
                            refresh: 1000,
                            displayFormats: {
                                second: 'h:mm:ss'
                            },
                            onRefresh: onRefresh,
                        },
                        ticks: {
                            maxRotation: 0,
                        }
                    }],
                    yAxes: [{
                        //type: 'logarithmic',
                        ticks: {
                            max: 60,
                            min: 0,
                            // max: 5,
                            // min: 0,
                            //stepSize: 2,
                            callback: function(value, index, values) {
                                return value + 'v';
                            }
                        }
                    }]
                },
                tooltips: {
                    mode: 'nearest',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: false
                }
            }
        };
    }
    
    function onRefresh(chart) {
        var datasets = chart.data.datasets;
        var len = datasets.length;
        
        for(var i = 0; i < len; i++) {
            var vals = getGraphValues();
            var point = createNewDatePoint(vals[i]);
            datasets[i].data.push(point);
        }
    }

    function createNewDatePoint(value) {return {x:  serverDate(), y:value};}
    
    function runTest() {
        function progressBarFormatter(val)
        {
            var maxWidth = 60;
            return Math.abs(val)*(100/maxWidth);
        }
        function getRandomInt(max) {
            return Math.floor(Math.random() * Math.floor(max));
        }
        function tester(){
            var val1 = getRandomInt(50);
            var val2 = getRandomInt(20);
            var val3 = getRandomInt(30);
            $("#progressbar1").css("width", progressBarFormatter(val1) +"%").text(val1 + "v");
            $("#progressbar2").css("width", progressBarFormatter(val2) +"%").text(val2 + "v");
            $("#progressbar3").css("width", progressBarFormatter(val3) +"%").text(val3 + "v");
            setGraphValues(val1,val2,val3);
        }

        setInterval(tester,1000);
    }

    return {
        createGraph: createGraph,
        setGraphValues: setGraphValues,
        getGraphValues: getGraphValues,
        runTest: runTest

    };
}
