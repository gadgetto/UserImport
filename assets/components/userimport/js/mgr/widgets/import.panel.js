UserImport.panel.Import = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        id: 'userimport-panel-import'
        ,title: _('userimport.import_users_tab')   
        ,defaults: { 
            border: false 
        }
        ,items:[{
            html: '<p>'+_('userimport.import_users_tab_desc')+'</p>'
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
                layout: 'column'
                ,border: false
                ,defaults: {
                    layout: 'form'
                    ,border: false
                }
                ,items: [{
                    columnWidth: .5
                    ,items: [{
                        xtype: 'fileuploadfield'
                        ,id: 'file'
                        ,name: 'file'
                        ,fieldLabel: _('userimport.import_users_file')
                        ,buttonText: _('userimport.import_users_file_button')
                        ,anchor: '100%'
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'file'
                        ,html: _('userimport.import_users_file_desc')
                        ,cls: 'desc-under'
                    },{
                        xtype: 'xcheckbox'
                        ,id: 'userimport-has-header'
                        ,name: 'hasheader'
                        ,hideLabel: true
                        ,boxLabel: _('userimport.import_users_first_row_headers')
                        ,description: MODx.expandHelp ? '' : _('userimport.import_users_first_row_headers_desc')
                        ,inputValue: 1
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'userimport-has-header'
                        ,html: _('userimport.import_users_first_row_headers_desc')
                        ,cls: 'desc-under'
                    },{
                        xtype: 'numberfield'
                        ,name: 'batchsize'
                        ,id: 'batchsize'
                        ,allowDecimals: false
                        ,allowNegative: false
                        ,autoStripChars: true
                        ,minValue: 0
                        ,value: 0
                        ,fieldLabel: _('userimport.import_users_batchsize')
                        ,description: MODx.expandHelp ? '' : _('userimport.import_users_batchsize_desc')
                        ,anchor: '100%'
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'batchsize'
                        ,html: _('userimport.import_users_batchsize_desc')
                        ,cls: 'desc-under'
                    },{
                        xtype: 'textfield'
                        ,name: 'delimiter'
                        ,id: 'delimiter'
                        ,value: ','
                        ,maxLength: 1
                        ,fieldLabel: _('userimport.import_users_delimiter')
                        ,description: MODx.expandHelp ? '' : _('userimport.import_users_delimiter_desc')
                        ,anchor: '100%'
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'delimiter'
                        ,html: _('userimport.import_users_delimiter_desc')
                        ,cls: 'desc-under'
                    },{
                        xtype: 'textfield'
                        ,name: 'enclosure'
                        ,id: 'enclosure'
                        ,value: '"'
                        ,maxLength: 1
                        ,fieldLabel: _('userimport.import_users_enclosure')
                        ,description: MODx.expandHelp ? '' : _('userimport.import_users_enclosure_desc')
                        ,anchor: '100%'
                    },{
                        xtype: MODx.expandHelp ? 'label' : 'hidden'
                        ,forId: 'enclosure'
                        ,html: _('userimport.import_users_enclosure_desc')
                        ,cls: 'desc-under'
                    }]
                },{
                    columnWidth: .5
                    ,items: [{
                        xtype: 'hidden'
                        ,name: 'usergroups'
                    },{
                        xtype: 'fieldset'
                        ,title: _('userimport.import_users_options')
                        ,id: 'userimport-options'
                        ,defaults: {
                            msgTarget: 'under'
                        }
                        ,items: [{
                            xtype: 'xcheckbox'
                            ,id: 'userimport-username-is-email'
                            ,name: 'autousername'
                            ,hideLabel: true
                            ,boxLabel: _('userimport.import_users_email_as_username')
                            ,description: _('userimport.import_users_email_as_username_desc')
                            ,inputValue: 1
                        },{
                            xtype: 'xcheckbox'
                            ,id: 'userimport-set-importmarker'
                            ,name: 'setimportmarker'
                            ,hideLabel: true
                            ,boxLabel: _('userimport.import_users_set_importmarker')
                            ,description: _('userimport.import_users_set_importmarker_desc')
                            ,checked: true
                            ,inputValue: 1
                        }]
                    },{
                        xtype: 'fieldset'
                        ,title: _('userimport.import_users_assign_groups_roles')
                        ,id: 'userimport-assign-groups-roles'
                        ,defaults: {
                            msgTarget: 'under'
                        }
                        ,items: [{
                            xtype: 'modx-tree'
                            ,id: 'userimport-tree-usergroups'
                            ,url: UserImport.config.connectorUrl
                            ,action: 'mgr/usergroups/getUserGroupNodes'
                            ,autoHeight: false
                            ,height: 160
                            ,root_id: 'n_ug_0'
                            ,root_name: _('userimport.import_users_groups')
                            ,rootVisible: false
                            ,enableDD: false
                            ,ddAppendOnly: true
                            ,useDefaultToolbar: true
                            ,stateful: false
                            ,collapsed: false
                            ,listeners: {
                                'checkchange': function(node,checked){
                                    // make dirty
                                    this.fireEvent('fieldChange');
                                }
                                ,scope:this
                            }
                        },{
                            xtype: 'modx-combo-role'
                            ,id: 'userimport-combo-role'
                            ,name: 'role'
                            ,value: '0'
                            ,hiddenName: 'role'
                            ,allowBlank: false
                            ,fieldLabel: _('userimport.import_users_roles')
                            ,description: MODx.expandHelp ? '' : _('user_group_user_add_role_desc')
                            ,anchor: '100%'
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'userimport-combo-role'
                            ,html: _('user_group_user_add_role_desc')
                            ,cls: 'desc-under'
                        }]
                    }]
                }]
            }]
        }]
    });
    UserImport.panel.Import.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.panel.Import,Ext.Panel);
Ext.reg('userimport-panel-import', UserImport.panel.Import);
