/**
 * FormPanel to handle main tabs (tabs container).
 * 
 * @class UserImport.IndexPanel
 * @extends MODx.FormPanel
 * @param {Object} config An object of options.
 * @xtype userimport-panel-index
 */

var topic = '/userimport/';
var register = 'mgr';

UserImport.IndexPanel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'userimport-panel-index'
        ,cls: 'container'
        ,bodyStyle: ''
        ,fileUpload: true
        ,items: [{
            html: _('userimport.cmp_title')
            ,xtype: 'modx-header'
        },{
            xtype: 'modx-tabs'
            ,itemId: 'tabs'
			,cls: 'structure-tabs'
            ,items: [{
                xtype: 'userimport-panel-import'
            },{
                xtype: 'userimport-panel-notification-template'
            },{
                xtype: 'userimport-panel-about'
            }]
        }]
        ,listeners: {
            'tabchange': {fn: function(panel) {
                panel.doLayout();
            }
            ,scope: this}
        }
    });
    Ext.Ajax.timeout = 0;
    UserImport.IndexPanel.superclass.constructor.call(this,config);
    this.init();
};
Ext.extend(UserImport.IndexPanel,MODx.FormPanel,{
    init: function(){
        this.actionToolbar = new Ext.Toolbar({
            renderTo: 'modx-action-buttons-container'
            ,id: 'modx-action-buttons'
            ,defaults: { scope: this }
            ,items: this.getButtons()
        });
        this.actionToolbar.doLayout();
        this.getSettings();
    }
    ,startUserImport: function(){
        Ext.MessageBox.show({
           title: _('userimport.import_users_msgbox_start_now')
           ,msg: _('userimport.import_users_msgbox_start_now_desc')
           ,buttons: Ext.MessageBox.OKCANCEL
           ,fn: this.importUsers
           ,icon: Ext.MessageBox.WARNING
           ,scope: this
        });
    }
    ,importUsers: function(btn,text){
        if (btn == 'cancel') {
            return;
        }
        this.console = MODx.load({
            xtype: 'modx-console'
            ,register: register
            ,topic: topic
            ,listeners: {
                'shutdown': {fn:function() {
                    //refresh page to reset fields
                    //location.href = MODx.config.manager_url + '?a=' + MODx.request.a + '&action=import';
                }
                ,scope:this}
            }
        });
        this.console.show(Ext.getBody());

        // get selected user groups from tree
        var nodeIDs = '';
        var selNodes;
        var tree = Ext.getCmp('userimport-tree-usergroups');
        selNodes = tree.getChecked();
        Ext.each(selNodes, function(node){
            if (nodeIDs!=='') {
                nodeIDs += ',';
            }
            nodeIDs += node.id;
        });
        
        // write selected nodes to hidden field
        this.getForm().setValues({
          usergroups: nodeIDs
        });
        
        this.getForm().submit({
            url: UserImport.config.connectorUrl
            ,params: {
                action: 'Bitego\\UserImport\\Processors\\Users\\Import'
                ,register: register
                ,topic: topic
            }
            ,success: function(form,action){
                if(action.result.success){
                    this.console.fireEvent('complete');
                }
            }
            ,failure: function(result,request) {
                this.console.fireEvent('complete');
            }
            ,scope: this
        });
    }
    ,getButtons: function() {
        var buttons = [];
        buttons.push({
            text: _('userimport.import_users_button_start')
            ,id: 'button-import-start'
            ,cls: 'primary-button'
            ,handler: this.startUserImport
            ,scope: this
        });
        buttons.push({
            text: '<i class="icon icon-check-circle icon-lg"></i>&nbsp;' + _('userimport.settings_save_button')
            ,id: 'button-settings-save'
            ,handler: this.updateSettings
            ,scope: this
        });
        buttons.push({
            text: '<i class="icon icon-question-circle"></i>'
            ,id: 'button-help'
            ,handler: function(){
                MODx.config.help_url = UserImport.config.helpUrl;
                MODx.loadHelpPane();
            }
            ,scope: this
        });
        return buttons;
    }
    ,getSettings: function(){
        this.getForm().load({
            url: UserImport.config.connectorUrl
            ,params: {
                action: 'Bitego\\UserImport\\Processors\\Settings\\Get'
            }
            ,waitMsg: _('userimport.msg_loading_defaults')
            ,success: function(data){
                //console.log(data);
            }
            ,failure: function(results,request){
                Ext.MessageBox.alert(_('userimport.msg_loading_defaults_failed'),results.responseText);
            }
            ,scope: this
        });
    }
    ,updateSettings: function(){
        this.getForm().submit({
            url: UserImport.config.connectorUrl
            ,params: {
                action: 'Bitego\\UserImport\\Processors\\Settings\\Update'
            }
            ,waitMsg: _('userimport.msg_saving_defaults')
            ,success: function(form,action){
                if(action.result.success){
                    // show success status message
                    MODx.msg.status({
                        title: _('save_successful')
                        ,message: _('userimport.msg_saving_defaults_successfull')
                        ,delay: 3
                    });
                }
            }
            ,failure: function(result,request) {
                Ext.MessageBox.alert(_('userimport.msg_saving_defaults_failed'),result.responseText);
            }
            ,scope: this
        });
    }
});
Ext.reg('userimport-panel-index',UserImport.IndexPanel);
