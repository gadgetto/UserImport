UserImport.panel.NotificationTemplate = function(config) {
    config = config || {};
    
    Ext.applyIf(config,{
        id: 'userimport-panel-notification-template'
        ,title: _('userimport.notification_template_tab')
        ,layout: 'anchor'
        ,defaults: {
            border: false
        }
        ,items:[{
            html: '<p>'+_('userimport.notification_template_tab_desc')+'</p>'
            ,xtype: 'modx-description'
        },{
            layout: 'form'
            ,cls: 'main-wrapper'
            ,labelAlign: 'top'
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                layout: 'column'
                ,border: false
                ,defaults: {
                    layout: 'form'
                    ,border: false
                }
                ,items: [{
                    columnWidth: .75
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
                        ,growMin: 280
                        ,grow: true
                        ,fieldLabel: _('userimport.notification_template_mail_body')
                        ,description: MODx.expandHelp ? '' : _('userimport.notification_template_mail_body_desc')
                        ,anchor: '100%'
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'mailbody'
                        ,html: _('userimport.notification_template_mail_body_desc')
                        ,cls: 'desc-under'
                    }]
                },{
                    columnWidth: .25
                    ,items: [{
                        xtype: 'fieldset'
                        ,title: _('userimport.notification_mail_options')
                        ,id: 'mail-options'
                        ,defaults: {
                            msgTarget: 'under'
                        }
                        ,items: [{
                            xtype: 'modx-combo'
                            ,id: 'notification-mail-format'
                            ,fieldLabel: _('userimport.notification_mail_format')
                            ,description: _('userimport.notification_mail_format_desc')
                            ,name: 'mail_format'
                            ,hiddenName: 'mail_format'
                            ,store: [
                                [1,_('userimport.notification_mail_format_html')],
                                [0,_('userimport.notification_mail_format_plaintext')]
                            ]
                            ,value: 1
                            ,triggerAction: 'all'
                            ,editable: false
                            ,selectOnFocus: false
                            ,preventRender: true
                            ,forceSelection: true
                            ,enableKeyEvents: true
                            ,anchor: '100%'
                        }]
                    }]
                }]
            }]
        }]
    });
    UserImport.panel.NotificationTemplate.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.panel.NotificationTemplate,Ext.Panel);
Ext.reg('userimport-panel-notification-template',UserImport.panel.NotificationTemplate);
