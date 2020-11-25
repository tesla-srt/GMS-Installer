/**
 * Contains functions used to dynamically get event logs from the server.
 */
function LogUpdater(){var t=!1;return{update:function e(n){var a=parseInt(999999999*Math.random());$.getJSON("mattLib/SdServer.php?element=get_"+n+"&rand="+a,function(a){$("#"+n+"Text").html(a),t&&setTimeout(e,3e3,n)})},setIsLogUpdating:function(e){t=e}}}