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
        ,unstyled: true
        ,fileUpload: true
        ,items: [{
            html: '<h2>'+_('userimport.cmp_title')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,itemId: 'tabs'
			,cls: 'structure-tabs'
            ,items: [{
                xtype: 'userimport-panel-import'
            },{
                xtype: 'userimport-panel-about'
            }]
        }]
    });
    Ext.Ajax.timeout = 0;
    UserImport.IndexPanel.superclass.constructor.call(this,config);
    this.init();
};
Ext.extend(UserImport.IndexPanel,MODx.FormPanel,{
    init: function(){
        this.actionToolbar = new Ext.Toolbar({
            renderTo: 'modAB'
            ,id: 'modx-action-buttons'
            ,defaults: { scope: this }
            ,items: this.getButtons()
        });                                
        this.actionToolbar.doLayout();
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
        if (this.console == null || this.console == undefined) {
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
        } else {
            this.console.setRegister(register,topic);
        }
        this.console.show(Ext.getBody());

        // get selected user groups from tree
        var nodeIDs = '';
        var selNodes;
        var tree = Ext.getCmp('userimport-tree-usergroups');
        selNodes = tree.getChecked();
        Ext.each(selNodes, function(node){
            if (nodeIDs!='') {
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
                action: 'mgr/users/import'
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
        },'-')
        buttons.push({
            text: _('help_ex')
            ,id: 'button-help'
            ,handler: function(){
                MODx.config.help_url = UserImport.config.helpUrl;
                MODx.loadHelpPane();
            }
            ,scope: this
        })
        return buttons;
    }
});
Ext.reg('userimport-panel-index',UserImport.IndexPanel);
