Vtiger.Class("Telcom_Js",{
    instance:false,
    getInstance: function(){
        if(Telcom_Js.instance == false){
            var instance = new Telcom_Js();
            Telcom_Js.instance = instance;
            return instance;
        }
        return Telcom_Js.instance;
    },
    syncUser: function (record) {
        var params = {};
        params['module'] = 'Telcom';
        params['action'] = 'IntegrationActions';
        params['mode'] = 'syncUserWithProvider';
        params['record'] = record;
        app.helper.showProgress();
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null && data!=""){
                    app.helper.hideProgress();
                    app.helper.showSuccessNotification({
                        message : data.message
                    });
                } else {
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({
                        message: err.message
                    })
                }
            },
            function(error) {
            }
        );
    }
},{
    registerAddButtonToUserPageEvent: function () {
        var record = app.getRecordId();
        if (record && app.getModuleName() == 'Users') {
            var btn = $('<button class="btn btn-default " id="Users_Sync_With_Telcom" onclick="Telcom_Js.syncUser(' + record + ')">Sync With Telcom</button>');
            jQuery('#userPageHeader').find('.detailViewButtoncontainer').find('.btn-group').prepend(btn);
        }
    },

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
                    title : app.vtranslate('JS_PBX_OUTGOING_SUCCESS'),
                    type : 'info'
                };
            } else if (e){
                params = {
                    title : app.vtranslate('JS_PBX_OUTGOING_FAILURE'),
                    type : 'info'
                };
            }
            Vtiger_Helper_Js.showPnotify(params);
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

    registerEvents: function(){
        this._super();
        this.registerAddButtonToUserPageEvent();
        this.checkRegisterClick2Call();
    }
});

jQuery(document).ready(function() {
    var instance = new Telcom_Js();
    instance.registerEvents();
});