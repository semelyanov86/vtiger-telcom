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

    registerEvents: function(){
        this._super();
        this.registerAddButtonToUserPageEvent();
    }
});

jQuery(document).ready(function() {
    var instance = new Telcom_Js();
    instance.registerEvents();
});