/**
 * Contains functions used to dynamically get event logs from the server.
 */
function LogUpdater() 
{
    var isLogUpdating = false;
    function setIsLogUpdating(state){isLogUpdating = state;}

    function update(name) {
        var myRandom = parseInt(Math.random()*999999999);
        $.getJSON('mattLib/SdServer.php?element=get_'+name+'&rand='+ myRandom,
            function(data)
            {
                $('#'+name+'Text').html(data);
                if(isLogUpdating)
                {
                    setTimeout (update, 3000 ,name);
                }
            }
        );
    }

    return {
        update: update,
        setIsLogUpdating: setIsLogUpdating,
    };
}
