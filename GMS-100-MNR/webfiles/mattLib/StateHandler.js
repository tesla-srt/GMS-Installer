function StateHandler(autoPcShutdownState) {
    //alert
    var _isTamperFault;
    var _isDoorOpen;
    var _isSurgeFault;
    var _isSwitchFault;
    //relay
    var _isSystemPowerCycling;
    var _isFanOff;
    //custom vars
    var _isPcRebooting;
    var _hasPcBeenTriggered;
    var _hasPcOffBeenTriggered;
    var _isPcOn;
    var _pingState;
    var _isBatteryLow;
    var _isAutoPcShutdownOn = (autoPcShutdownState == 1);
    
    function update(data) {
        //alert
        _isTamperFault = (data.a1 == 1);
        _isDoorOpen = (data.a2 == 1);
        _isSurgeFault = (data.a3 == 1);
        // /**
        //      NOTE: MNR data.a4 == 1;
        // *//
        _isSwitchFault = (data.a4 == 1);
        //relay
        _isSystemPowerCycling = (data.r1 == 0);
        _isFanOff = (data.r2 == 1);
        //custom vars
        _isPcOn = (data.pcstate == 1);
        _isPcRebooting = (data.isrebooting == 1);
        _hasPcBeenTriggered = (data.hasPcOnBeenTriggered == 1);
        _hasPcOffBeenTriggered = (data.hasPcOffBeenTriggered == 1);
        _isBatteryLow = (data.vm2 <= 22.5);
        _pingState = data.pingstate;
    }
    
    function setIsAutoPcShutdownOn(state) { _isAutoPcShutdownOn = state;}

    function isTamperFault() {return _isTamperFault;}
    function isDoorOpen() {return _isDoorOpen;}
    function isSurgeFault() {return _isSurgeFault;}
    function isSwitchFault() {return _isSwitchFault;}

    function isSystemPowerCycling() {return _isSystemPowerCycling; }
    function isFanOff() {return _isFanOff;}

    function isPcOn() {return _isPcOn;}
    function pingState() {return _pingState;}
    function isPcRebooting() {return _isPcRebooting;}
    function hasPcOnBeenTriggered() {return _hasPcBeenTriggered;}
    function hasPcOffBeenTriggered() {return _hasPcOffBeenTriggered;}
    function isBatteryLow() {return _isBatteryLow;}
    function isAutoShutdownOn() {return _isAutoPcShutdownOn;}

    function isPcOnButtonNotTriggerable() {var onState = _pingState; return (_isAutoPcShutdownOn || _hasPcBeenTriggered || _isPcRebooting || _isSystemPowerCycling || onState);}
    function isPcOffButtonNotTriggerable() { return (_isAutoPcShutdownOn || _isPcRebooting || _isSystemPowerCycling || !_isPcOn || _hasPcOffBeenTriggered);}

    function isBatteryLowAndAutoShutdownEnabled() { return (_isBatteryLow && _isAutoPcShutdownOn);}

    return {
        update: update,
        setIsAutoPcShutdownOn: setIsAutoPcShutdownOn,

        isTamperFault : isTamperFault,
        isDoorOpen : isDoorOpen,
        isSurgeFault : isSurgeFault,
        isSwitchFault : isSwitchFault,

        isSystemPowerCycling : isSystemPowerCycling,
        isFanOff : isFanOff,

        isPcOn : isPcOn,
        pingState : pingState,
        isPcRebooting : isPcRebooting,
        hasPcOnBeenTriggered : hasPcOnBeenTriggered,
        hasPcOffBeenTriggered : hasPcOffBeenTriggered,
        isBatteryLow : isBatteryLow,
        isAutoShutdownOn : isAutoShutdownOn,

        isPcOnButtonNotTriggerable : isPcOnButtonNotTriggerable,
        isPcOffButtonNotTriggerable : isPcOffButtonNotTriggerable,
        isBatteryLowAndAutoShutdownEnabled : isBatteryLowAndAutoShutdownEnabled
    };
}