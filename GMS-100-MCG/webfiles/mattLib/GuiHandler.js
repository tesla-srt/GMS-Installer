/**
 * Contains GUI functions used to respond to user input.
 * @param {logUpdater - Used to control log updates on certain js events.} logUpdater 
 */
function GuiHandler(logUpdater) 
{
    function printLog(logTitle, logText, hostname) {
        var printTitleContents = document.getElementById(logTitle).innerHTML;
        var printTextContents = document.getElementById(logText).innerHTML;
        document.body.innerHTML = 
            "<h5>" + hostname + "</h5><br>" +
            "<h6>" + printTitleContents + "</h6>" +
            "<span style='white-space: pre-line'>" + printTextContents + "</span>";

        window.print();
        function goBack() { document.location.href = "index.php"; }
        setInterval(goBack, 1000);
    }

    function resetOptionsOnModalExit() {
        $('#optionsModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
        });
    }

    function openLogOnClick(btn, name) {
        $("#" + btn).click(function () {
            logUpdater.setIsLogUpdating(true);
            //console.writeln(name);
            logUpdater.update(name);
            $("#" + name).modal("show");
        });
    }
    
    function disableLogLoopingOnExit() {
        $('.modal').on('hidden.bs.modal', function () {
            logUpdater.setIsLogUpdating(false);
        });
    }

    function openAlertOnClick(btn) {
        $("#"+btn).click(function(){
            let theBtn = $("#"+btn).attr('id');
            let theState = $("#btnFanState").prop('checked');
            let prevState = $("#r2N").html().substr(4);
            $("#alertSubmitBtn").val(theBtn);
            if (theBtn=='btnFanState') {
                if(prevState == "On")  {
                    $("#alertSubmitBtn").val("btnFanOff");
                } else {
                    $("#alertSubmitBtn").val("btnFanOn");
                }
            }
            $("#alertModal").modal("show");
        });
    }
    
   
    return {
        printLog:printLog,
        resetOptionsOnModalExit:resetOptionsOnModalExit,
        openLogOnClick:openLogOnClick,
        disableLogLoopingOnExit:disableLogLoopingOnExit,
        openAlertOnClick:openAlertOnClick,
    };
}
