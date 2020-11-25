/**
 * Contains functions to dynamically update gui values. 
 */
function updateValues(timeOut, graphHandler, states, serverLink) {
    update();
    const colBlue = 'rgb(86, 156, 189)';
    const colGray ='#6c757d';
    const colYellow = '#ffc107';
    
    function update() {
        $.getJSON(serverLink + getRandomInt(),
            function (data) {
                states.update(data);
                updateVoltmeters(data);
                updateAlarms(data);
                updatePcControls();
                updateSystemRebootRelay(data);
                updateClimateControlRelay(data);
                updateInfo(data);
            
                var offTemp = $('#fanOffTemp').html();
                var onTemp = $('#fanOnTemp').html();
                var curTemp = $('#tempf').html();
                if (curTemp <= onTemp) {
                    $('#tempf').attr('style','color:green;font-weight:bold');
                    $('#tempfLabel').attr('style','color:green;font-weight:bold');
                } else {
                    $('#tempf').attr('style','color:red;font-weight:bold');
                    $('#tempfLabel').attr('style','color:red;font-weight:bold');
                    $('#tempf').fadeOut(250).fadeIn(250);
                    $('#tempfLabel').fadeOut(250).fadeIn(250);
                }
                
                

                setTimeout(update, timeOut);
            }
        ).fail(lostConnection);
    }
    function getRandomInt() { return parseInt(Math.random() * 999999999);}

    function updateVoltmeters(data) {
        graphHandler.setGraphValues(round2Nearest100th(data.vm1),
            round2Nearest100th(data.vm2), round2Nearest100th(data.vm3));

        var vms = graphHandler.getGraphValues();
        var scaledVm1 = scaleValueto60(vms[0]);
        var scaledVm2 = scaleValueto60(vms[1]);
        var scaledVm3 = scaleValueto60(vms[2]);

        $("#progressbar1").css("width", scaledVm1 + "%").text(vms[0] + "v");
        $("#progressbar2").css("width", scaledVm2 + "%").text(vms[1] + "v");
        $("#progressbar3").css("width", scaledVm3 + "%").text(vms[2] + "v");
    }
    function round2Nearest100th(val) { return Math.round(100 * val) / 100; }
    function scaleValueto60(val) {return Math.abs(val) * (100 / 60);}

    function updateAlarms(data) {
        if (states.isTamperFault()) {
            setAlarmLabel('a1', data.a1hi, "red");
            toggleAlarm('alarm1', true);
        } else {
            setAlarmLabel('a1', data.a1lo, "green");
            toggleAlarm('alarm1', false);
        }

        if (states.isDoorOpen()) {
            setAlarmLabel('a2', data.a2hi, "red");
            toggleAlarm('alarm2', true);
        } else {
            setAlarmLabel('a2', data.a2lo, "green");
            toggleAlarm('alarm2', false);
        }

        if (states.isSurgeFault()) {
            setAlarmLabel('a3', data.a3hi, "red");
            toggleAlarm('alarm3', true);
        } else {
            setAlarmLabel('a3', data.a3lo, "green");
            toggleAlarm('alarm3', false);
        }

        if (states.isSwitchFault()) {
            setAlarmLabel('a4', data.a4lo, "red");
            toggleAlarm('alarm4', true);
        } else {
            setAlarmLabel('a4', data.a4hi, "green");
            toggleAlarm('alarm4', false);
        }
    }
    function setAlarmLabel(name, value, color) {
        $('#' + name + 'state').replaceWith("<span id='" + name + "state' style='color:" + color + ";font-size:1em'>" + value + "</span>");
    }
    function toggleAlarm(name, state) {
        $('#' + name)
            .find('[data-fa-i2svg]')
            .toggleClass('fa-check-circle', !state)
            .toggleClass('fa-exclamation-triangle', state)
            .toggleClass('text-success', !state)
            .toggleClass('text-danger', state);
    }

    function updatePcControls() {
        if (states.isPcOnButtonNotTriggerable()) {
            enableButton("btnPcOn", "success", false);
            var stateMsg = "PINGING";
            var boldClass = "class='text-primary'";
            $("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
        } else {
            enableButton("btnPcOn", "success", true);
        }

        if (states.isPcOffButtonNotTriggerable()) {
            enableButton("btnPcOff", "danger", false);
            var stateMsg = "PINGING";
            var boldClass = "class='text-primary'";
            $("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
        } else {
            enableButton("btnPcOff", "danger", true);
        }

        //set pc state
        var stateMsg = "PINGING";
        var boldClass = "class='text-warning'";
        //$("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
        
        if (states.hasPcOnBeenTriggered() || states.hasPcOffBeenTriggered()) {
            stateMsg = "PINGING";
            boldClass = "class='text-primary'";
            //$("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
        } else if (states.isPcOn() == true && !states.hasPcOffBeenTriggered()) {
            stateMsg = "ON";
            boldClass = "class='text-success'";
        }
        
        if(states.pingState() == false || !states.isPcOn()) {
            if (states.hasPcOnBeenTriggered() && !states.isPcOn()) {
                stateMsg = "PINGING";
                boldClass = "class='text-primary'";
            } else if (states.hasPcOffBeenTriggered() && states.isPcOn()) {
                stateMsg = "PINGING";
                boldClass = "class='text-primary'";
            } else {
                stateMsg = "OFF";
                boldClass = "class='text-danger'";
            }
        } 
            /*if(states.isPcOn()) {
                stateMsg = "ON";
                boldClass = "class='text-success'";
                //updatePcControls();
            }*/
        
        $("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
    }
    function enableButton(name, type, enable) {
        $("#" + name)
            .prop("disabled", !enable)
            .toggleClass('btn-outline-' + type, !enable)
            .toggleClass("btn-" + type, enable);
    }

    function updateSystemRebootRelay(data) {
        //var interval = setInterval(function(){}, 10000);
        if (states.isSystemPowerCycling()) {
            $('#r1N').text(data.r1NO).css('color', colYellow);
            var stateMsg = "OFF";
            var boldClass = "class='text-danger'";
            $("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
            $('#btnSystemReboot').removeClass("btn-outline-warning");
            $('#btnSystemReboot').addClass("btn-outline-primary");
            $('svg.fa-power-off').remove();
            $('#r1Left').addClass("spinner-border text-primary");
            // clearInterval(interval);
            //interval = setInterval('$("#r1Left").toggleClass("active");', 1250);
            
        } else if (states.isPcRebooting()) {
            enableSystemRebootButton(false, "<b>PLEASE WAIT</b>");
            $("#r1N").text("Shutting Down PC").css('color', colBlue);
            $('#btnSystemReboot').removeClass("btn-outline-warning");
            $('#btnSystemReboot').addClass("btn-outline-primary");
            var stateMsg = "PINGING";
            var boldClass = "class='text-primary'";
            $("#pcState").html("Status: " + "<b " + boldClass + ">" + stateMsg + "</b>");
            $("#chBxAutoShutdown").prop("disabled", true);
            
            $('svg.fa-power-off').remove();
            $('#r1Left').addClass("spinner-border text-primary");
           // clearInterval(interval);
           // interval = setInterval('$("#r1Left").toggleClass("active");', 1250);

        } else if (states.isBatteryLowAndAutoShutdownEnabled()) {
            enableSystemRebootButton(false, "SYSTEM REBOOT");
           // toggleRelayIconsOn('r1', false, "warning");
            $("#r1N").text("Low Power Mode").css('color', colGray);
            $("#chBxAutoShutdown").prop("disabled", false);
          //  clearInterval(interval);
         //   interval = setInterval('$("#r1Left").toggleClass("active");', 1250);

        } else { //normal operation
            enableSystemRebootButton(true, "SYSTEM REBOOT");
           // toggleRelayIconsOn('r1', false, "secondary");
            $('#r1N').text(data.r1NC).css('color', "green");
            $("#chBxAutoShutdown").prop("disabled", false);
           // clearInterval(interval);
        }
    }
    function enableSystemRebootButton(state, text) {
        $("#btnSystemReboot")
            .html(text)
            .prop("disabled", !state)
            .toggleClass('btn-outline-warning', !state)
            .toggleClass("btn-warning", state);
    }

    /**function updateClimateControlRelay(data) {
        $("#tempf").text(data.tempf);

        if (states.isFanOff()) {
            $("#btnFanOff").prop("disabled", true).toggleClass("btn-danger", true)
            .toggleClass("btn-warning", false);
            $("#btnFanOn").prop("disabled", false).toggleClass("btn-warning", true)
            .toggleClass("btn-danger", false);
            toggleRelayIconsOn('r2', false, "danger");
            $('#r2N').text(data.r2NC).css('color', "red");
        } else {
            $("#btnFanOn").prop("disabled", true).toggleClass("btn-danger", true)
            .toggleClass("btn-warning", false);
            $("#btnFanOff").prop("disabled", false).toggleClass("btn-warning", true)
            .toggleClass("btn-danger", false);
            toggleRelayIconsOn('r2', true, "primary");
            $('#r2N').text(data.r2NO).css('color', "green");
        }
    } **/
    
    function updateClimateControlRelay(data) {
        $("#tempf").text(data.tempf);
        if (states.isFanOff()) {
            $("#btnFanState").prop("checked", false);
            toggleRelayIconsOn('r2', false, "danger");
            $('#r2N').text(data.r2NC).css('color', "red");
        } else {
            $("#btnFanState").prop("checked", true);
            toggleRelayIconsOn('r2', true, "primary");
            $('#r2N').text(data.r2NO).css('color', "green");
        }
    }
    
    function toggleRelayIconsOn(name, onState, onColor) {
        $('#' + name + 'Left')
            .find('[data-fa-i2svg]')
            .toggleClass('fa-circle-notch', onState)
            .toggleClass('fa-circle', !onState)
            .toggleClass('text-secondary', onState)
            .toggleClass('text-secondary', !onState)
            .toggleClass('text-' + onColor, !onState);
        $('#' + name + 'Right')
            .find('[data-fa-i2svg]')
            .toggleClass('fa-circle-notch', !onState)
            .toggleClass('fa-circle', onState)
            .toggleClass('text-secondary', !onState)
            .toggleClass('text-' + onColor, onState);
    }

    function updateInfo(data) {
        $("#uptime").text(data.uptime);
        //$("#progressbar9").css("width", data.meminfo + "%").text(data.meminfo + "%");
        //$("#progressbar10").css("width", data.diskinfo + "%").text(data.diskinfo + "%");
    }

    
    function lostConnection() {
        setTimeout(function(){
            $('#r1N').text("Connection Lost.").css('color', 'red');
            $('#btnSystemReboot').removeClass("btn-outline-warning");
            $('#btnSystemReboot').addClass("btn-outline-danger");
        },2000);
        setTimeout(function(){
            document.location.href = "index.php";
        }, 60000);
    }
}