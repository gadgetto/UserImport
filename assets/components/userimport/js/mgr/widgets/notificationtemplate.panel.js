UserImport.panel.NotificationTemplate = function(config) {
    config = config || {};
        
    Ext.applyIf(config,{
        id: 'userimport-panel-notification-template'
        ,title: _('userimport.notification_template_tab')   
        ,defaults: { 
            border: false 
        }
        ,items:[{
            html: '<p>'+_('userimport.notification_template_tab_desc')+'</p>'
            ,border: false
            ,bodyCssClass: 'panel-desc'
        },{
            layout: 'form'
            ,cls: 'main-wrapper'
            ,labelAlign: 'top'
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'textfield'
                ,name: 'mailsubject'
                ,id: 'mailsubject'
                ,fieldLabel: _('userimport.notification_template_mail_subject')
                ,description: MODx.expandHelp ? '' : _('userimport.notification_template_mail_subject_desc')
                ,anchor: '100%'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'mailsubject'
                ,html: _('userimport.notification_template_mail_subject_desc')
                ,cls: 'desc-under'
            },{
                xtype: 'textarea'
                ,name: 'mailbody'
                ,id: 'mailbody'
                ,height: 300
                ,fieldLabel: _('userimport.notification_template_mail_body')
                ,description: MODx.expandHelp ? '' : _('userimport.notification_template_mail_body_desc')
                ,anchor: '100%'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden'
                ,forId: 'mailbody'
                ,html: _('userimport.notification_template_mail_body_desc')
                ,cls: 'desc-under'
            }]
        }]
    });
    UserImport.panel.NotificationTemplate.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.panel.NotificationTemplate,Ext.Panel);
Ext.reg('userimport-panel-notification-template',UserImport.panel.NotificationTemplate);
