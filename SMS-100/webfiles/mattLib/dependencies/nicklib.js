function setTempGraphImage(select) {
    var selectedvalue = select;
    var s = $('#tempStartDate');
    var e = $('#tempEndDate');
    let hostname = getHostName();
    var result = '';
    
    if (selectedvalue == '8hr') {
        var todaysdate = new Date();
        var dt = new Date(todaysdate - 28800000);
        getCustomTempGraph(getDateTimeString(dt), getDateTimeString(todaysdate));
    } else {
    
    $.getJSON('mattLib/tempGraph.php',

        function(data) {

        result = data.temp[selectedvalue];

        $('#tempGraphPng').attr('src', 'data:image/png;base64,' + result);
    },
             );
    }
    var todaysDate = new Date();
    var aWeek = new Date(todaysDate - 604800000); //604800000 milliseconds in a week
    var weekString = getDateString(aWeek);
                
    var aMonth = new Date(todaysDate - 2628000000); //2628000000 milliseconds in a month
    var monthString = getDateString(aMonth);
    var aYear = new Date(todaysDate - 31540000000); //31540000000 milliseconds in a year
    var yearString = getDateString(aYear);    

    if (selectedvalue == 'day' || selectedvalue == 'hour') {
        s.val(moment(todaysDate).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'week') {
        s.val(moment(weekString).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'month') {
        s.val(moment(monthString).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'year') {
        s.val(moment(yearString).format('MM/DD/YYYY'));
    }
    e.val(moment(todaysDate).format('MM/DD/YYYY'));
}

function setVMGraphImage(select) {
    var vmnum = $("input[name='vmRadio']:checked").val();
    
    var selectedvalue = select;
    var s = $('#vmStartDate');
    var e = $('#vmEndDate');
    
    var result = '';
    
    if (selectedvalue == '8hr') {
        var todaysdate = new Date();
        var dt = new Date(todaysdate - 28800000);
        getCustomVMGraph(getDateTimeString(dt), getDateTimeString(todaysdate));
    } else {
    
    $('.loading-modal').modal('show'); 

    $.getJSON('mattLib/vm_graph.php',

        function(data) {

       // result = data[vmnum][selectedvalue];
        result = data[vmnum][selectedvalue];

        $('#vmGraphPng').attr('src', 'data:image/png;base64,' + result);
            $('.loading-modal').modal('hide');
    },
             );
    // setTimeout(function () {
    //     $('.loading-modal').modal('hide');
    //    }, 2500);
    }
    var todaysDate = new Date();
    var aWeek = new Date(todaysDate - 604800000); //604800000 milliseconds in a week
    var weekString = getDateString(aWeek);
                
    var aMonth = new Date(todaysDate - 2628000000); //2628000000 milliseconds in a month
    var monthString = getDateString(aMonth);
    var aYear = new Date(todaysDate - 31540000000); //31540000000 milliseconds in a year
    var yearString = getDateString(aYear);

    if (selectedvalue == 'day' || selectedvalue == 'hour') {
        s.val(moment(todaysDate).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'week') {
        s.val(moment(weekString).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'month') {
        s.val(moment(monthString).format('MM/DD/YYYY'));
    } else if (selectedvalue == 'year') {
        s.val(moment(yearString).format('MM/DD/YYYY'));
    }
    e.val(moment(todaysDate).format('MM/DD/YYYY'));
}

function downloadTempGraph() {
    var s = $('#tempStartDate').val();
    var e = $('#tempEndDate').val();
    var a = document.createElement("a"); //Create <a>
    a.href = $('#tempGraphPng').attr('src'); //Image Base64 Goes here
    a.download = "Temperature-" + s + '.' + e + ".png"; //File name Here
    a.click(); //Downloaded file
}

function downloadVMGraph() {
    var vm = $("input[name='vmRadio']:checked").siblings(0).html();
    var s = $('#vmStartDate').val();
    var e = $('#vmEndDate').val();
    var a = document.createElement("a"); //Create <a>
    a.href = $('#vmGraphPng').attr('src'); //Image Base64 Goes here
    a.download = getHostName() + " - " + vm + "-" + s + '.' + e + ".png"; //File name Here
    a.click(); //Downloaded file
}

function downloadTempCsv() {
    var s = $('#tempStartDate').val();
    var e = $('#tempEndDate').val();
    var ans = '';
    var result;
    
    $.getJSON('mattLib/download_csv.php', function(data) {
        ans = data.temp;
        var a = document.createElement("a"); //Create <a>
        a.href = 'data:text/plain;base64,' + ans; //Image Base64 Goes here
        a.download = getHostName() + " - temperature-report." + new Date() +".csv"; //File name Here
        a.click(); //Downloaded file
    },
             );
}

function downloadVMCsv() {
    var s = $('#tempStartDate').val();
    var e = $('#tempEndDate').val();
    var ans = '';
    var result;
    
    $.getJSON('mattLib/download_csv.php', function(data) {
        ans = data.vm;
        var a = document.createElement("a"); //Create <a>
        a.href = 'data:text/plain;base64,' + ans; //Image Base64 Goes here
        a.download = "vm-report.csv"; //File name Here
        a.click(); //Downloaded file
    },
             );
}

function getCustomTempGraph(startDate, endDate) {
    var s = startDate;
    var e = endDate;
    var result = '';

    if (s == "" || e == "" || s == null || e == null) {
        alert('Please choose a date.');
        void(0);
    } else {
        
        if ((startDate == endDate) && (endDate == startDate)) {
            var i = new Date(new Date(s) - 86400000);
            s = getDateTimeString(i);
        }
        
        
        $('.loading-modal').modal('show');
        
        $.getJSON('mattLib/tempGraph.php?s=' + s + '&e=' + e,
                  function(data) {
             
            result = data.temp.custom;
            $('#tempGraphPng').attr('src', 'data:image/png;base64,' + result);
                      $('.loading-modal').modal('hide');
        },
                 );
    // setTimeout(function () {
    //    	$('.loading-modal').modal('hide');
    //    }, 2500);
    }
}

function getCustomVMGraph(startDate, endDate) {
    var s = startDate;
    var e = endDate;
    var result = '';
    var vmnum = $("input[name='vmRadio']:checked").val();

    if (s == "" || e == "" || s == null || e == null) {
        alert('Please choose a date.');
        void(0);
    } else {        
        
        if ((startDate == endDate) && (endDate == startDate)) {
            var i = new Date(new Date(s) - 86400000);
            s = getDateTimeString(i);
        }
        $('.loading-modal').modal('show'); 
        $.getJSON('mattLib/vm_graph.php?s=' + s + '&e=' + e,
                  function(data) {
            result = data[vmnum].custom;
            $('#vmGraphPng').attr('src', 'data:image/png;base64,' + result);
            $('.loading-modal').modal('hide');
        },
                 );
       //  setTimeout(function () {
       // 	$('.loading-modal').modal('hide');
       // }, 4500);
    }
//    if ($("#vmRange").prop("selectedIndex") != 1) {
//        $("#vmRange").prop("selectedIndex", 0);
//    }
    
}

function openGraphOnClick(btn, name) {
    $("#" + btn).click(function() {
        $("#" + name).modal("show");
    });
}

function getDateString(dt) {
  return ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][dt.getMonth()] +
      '-' + dt.getDate() + '-' + dt.getFullYear();
}

function getDateTimeString(dt) {
    return getDateString(dt) + " " + dt.getHours() + ":" + dt.getMinutes();
}

var stickyOffset = $('.sticky').offset().top + 10;

$(window).scroll(function(){
  var sticky = $('.sticky'),
      scroll = $(window).scrollTop();

  if (scroll >= stickyOffset) sticky.addClass('fixed');
  else sticky.removeClass('fixed');
});


$("#alertSettingForm").submit(function(event) {
    var loTempVal = $('#textLoTempAlert').val();
    var hiTempVal = $('#textHiTempAlert').val();

    var loTempPrev = $('#textLoTempAlert').prop('placeholder').replace('Low Temp: ', '');
    var hiTempPrev = $('#textHiTempAlert').prop('placeholder').replace('High Temp: ', '');

    if(loTempVal.length < 1 && hiTempVal.length < 1) {
        $('#textLoTempAlert').val(loTempPrev);
        $('#textHiTempAlert').val(hiTempPrev);
    } else {
    
    var loTempFloat = parseFloat(loTempVal);
    var hiTempFloat = parseFloat(hiTempVal);
    if (isNaN(loTempFloat) || loTempFloat < (-1*20)) {
        event.preventDefault();
        alert('Please double check your temperature inputs');
    } else if(isNaN(hiTempFloat) || hiTempFloat > 200) {
        event.preventDefault();
        alert('Please double check your temperature inputs');
    }
    }
});

/*$(document).ready(function() {

    $('button[name=btnTempGraphUpdate]').click(function() {
        $("#tempRange").prop("selectedIndex", 0);
    }); 

    $('button[name=btnVMGraphUpdate]').click(function() {
        $("#tempRange").prop("selectedIndex", 0);
    });  
});*/