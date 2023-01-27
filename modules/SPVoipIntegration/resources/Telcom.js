jQuery.Class("Telcom_Js", {}, {
    
    registerClick2Call : function() {
        var thisInstance = this;
        var params = {};
        params['mode'] = 'getOutgoingPermissions';
        params['module'] = 'Telcom';        
        params['action'] = 'IntegrationActions';
        app.request.post({data: params}).then(function (e, result) {
            if(result.permission) {
                Vtiger_PBXManager_Js.makeOutboundCall = function(number, record){
                    thisInstance.click2Call(number);
                };
            }            
        });        
    },
    
    click2Call : function(phoneNumber) {
        var params = {};
        params['mode'] = 'startOutgoingCall';
        params['module'] = 'Telcom';        
        params['action'] = 'IntegrationActions';        
        params['number'] = phoneNumber;
        app.request.post({data: params}).then(function (e, result) {
            if (result) {    
                params = {
                    text : app.vtranslate('JS_PBX_OUTGOING_SUCCESS'),
                    type : 'info'
                }                                                         
            } else if (e){
                params = {
                    text : app.vtranslate('JS_PBX_OUTGOING_FAILURE'),
                    type : 'info'
                }
            }
            Vtiger_PBXManager_Js.showPnotify(params);
        });
    },
    
    checkRegisterClick2Call : function() {
        var thisInstance = this;
        app.request.post({data: {
            module : 'Telcom',
            action : 'IntegrationActions',
            mode : 'checkClickToCall'
        }}).then(function (error, response) {
            if(response.enabled) {
                thisInstance.registerClick2Call();  
            }
        });
    },
    
    registerEvents : function () {              
        this.checkRegisterClick2Call();    
    }
});

jQuery(document).ready(function () {
    var controller = new Telcom_Js();
    controller.registerEvents();
});

