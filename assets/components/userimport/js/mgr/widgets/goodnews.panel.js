/**
 * Generates the GoodNews Groups/Categories Tree
 *
 * @class UserImport.tree.GoodNewsGroupsCategories
 * @extends MODx.tree.Tree
 * @param {Object} config An object of options.
 * @xtype goodnews-tree-groupscategories
 */
UserImport.tree.GoodNewsGroupsCategories = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        xtype: 'modx-tree'
        ,id: 'userimport-tree-gongroupscategories'
        ,url: UserImport.config.connectorUrl
        ,action: 'Bitego\\GoodNews\\Processors\\Group\\GroupCategoryGetNodes'
        ,autoHeight: false
        ,height: 380
        ,root: {
            text: _('userimport.import_goodnews_grpcat')
            ,id: 'n_gongrp_0'
            ,cls: 'tree-pseudoroot-node'
            ,iconCls: 'icon-tags'
            ,draggable: false
            ,nodeType: 'async'
        }
        ,rootVisible: false
        ,enableDD: false
        ,ddAppendOnly: true
        ,useDefaultToolbar: true
        ,stateful: false
        ,collapsed: false
        ,cls: 'ui-tree-gongroupscategories'
        ,listeners: {
            'checkchange': function(node,checked){
                // make dirty
                this.fireEvent('fieldChange');
                // check parent node (group) if child (category) is checked
                if(checked){
                    pn = node.parentNode;
                    pn.getUI().toggleCheck(checked);
                    node.expand();
                // uncheck all child (category) nodes if parent (group) is unchecked
                }else{
                    node.collapse();
                    node.eachChild(function(n) {
                        n.getUI().toggleCheck(checked);
                    });
                }
            }
            ,scope:this
        }
    });
    UserImport.tree.GoodNewsGroupsCategories.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.tree.GoodNewsGroupsCategories,MODx.tree.Tree,{});
Ext.reg('userimport-tree-gongroupscategories',UserImport.tree.GoodNewsGroupsCategories);

/**
 * The GoodNews groups/categories select panel.
 *
 * @class UserImport.panel.GoodNews
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype userimport-panel-goodnews
 */
UserImport.panel.GoodNews = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        id: 'userimport-panel-goodnews'
        ,title: _('userimport.import_goodnews_tab')
        ,layout: 'anchor'
        ,defaults: { 
            border: false 
        }
        ,items:[{
            html: '<p>'+_('userimport.import_goodnews_tab_desc')+'</p>'
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
                xtype: 'hidden'
                ,name: 'gongroupscategories'
            },{
                xtype: 'fieldset'
                ,title: _('userimport.import_goodnews_assign_grpcat')
                ,id: 'userimport-assign-gongrpcat'
                ,defaults: {
                    msgTarget: 'under'
                }
                ,items: [{
                    xtype: 'userimport-tree-gongroupscategories'
                }]
            }]
        }]
    });
    UserImport.panel.Import.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.panel.GoodNews,Ext.Panel);
Ext.reg('userimport-panel-goodnews', UserImport.panel.GoodNews);
