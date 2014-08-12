UserImport.panel.About = function(config) {
    config = config || {};

    /* Content of the About box */
    var about = [
        '<h3>'+UserImport.config.componentName+' '+UserImport.config.componentVersion+'-'+UserImport.config.componentRelease+'</h3>',
        '<p>',
        _('userimport.desc')+'<br />',
        '&copy; by '+UserImport.config.developerName+'<br />',
        '<a href="'+UserImport.config.developerUrl+'">'+UserImport.config.developerUrl+'</a>',
        '</p>',
       ].join('\n');

    /* Content of the Credits box */
    var credits = [
        '<h3>'+_('userimport.about_credits')+'</h3>',
        '<p>'+_('userimport.about_credits_modx_community')+'</p>',
        ].join('\n');
        
    Ext.applyIf(config,{
        id: 'userimport-panel-about'
        ,title: _('userimport.about_tab')   
        ,defaults: { 
            border: false 
        }
        ,items:[{
            layout: 'form'
            ,cls: 'main-wrapper'
            ,labelAlign: 'top'
            ,anchor: '100%'
            ,defaults: {
                msgTarget: 'under'
            }
            ,items: [{
                xtype: 'container'
                ,autoEl: 'div'
                ,cls: 'ui-about-box'
                ,html: about
            },{
                xtype: 'container'
                ,autoEl: 'div'
                ,cls: 'ui-about-box'
                ,html: credits
            }]
        }]
    });
    UserImport.panel.About.superclass.constructor.call(this,config);
};
Ext.extend(UserImport.panel.About,Ext.Panel);
Ext.reg('userimport-panel-about', UserImport.panel.About);
