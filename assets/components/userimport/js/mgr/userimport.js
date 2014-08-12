/**
 * Instantiate the UserImport class
 * 
 * @class UserImport
 * @extends Ext.Component
 * @param {Object} config An object of config properties
 */
var UserImport = function(config) {
    config = config || {};
    UserImport.superclass.constructor.call(this,config);
};
Ext.extend(UserImport,Ext.Component,{
    window:{},grid:{},panel:{},tabs:{},page:{},combo:{},config:{},msg:{},util:{},form:{},toolbar:{},tree:{}
});
Ext.reg('userimport',UserImport);

UserImport = new UserImport();
