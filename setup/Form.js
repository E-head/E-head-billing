Ext.ns('Setup');

Setup.Form = Ext.extend(Ext.form.FormPanel, {
	
	loadUrl: '/setup/?action=load',
	
    saveUrl: '/setup/?action=submit',
    
    bodyStyle: {
        padding: '10px'
    },

    autoScroll: true,
    
    labelWidth: 150,
    
    title: 'Config editor',
    
	initComponent: function() {
		
        this.items = [{
            xtype: 'fieldset',
            title: 'General settings',
            autoHeight: true,
            defaults: {
                xtype: 'textfield',
                anchor: '100%'
            },
            items: [{
                xtype: 'checkbox',
                fieldLabel: 'Debug',
                anchor: 0,
                name: 'debug',
                title: 'Hide all errors message then false. Disable on production server. Used only by developers (default: false)',
                checked: false
            },{
                xtype: 'textfield',
                fieldLabel: 'Company name',
                name: 'company[name]',
                title: 'Company name to identify this site'
            },{
                xtype: 'combo',
                fieldLabel: 'Timezone',
                name: 'ui[timezone]',
                hiddenName: 'ui[timezone]',
                title: 'Default timezone (default: Europe/London)',
                value: 'Europe/London',
                mode: 'local',
                triggerAction: 'all',
                allowBlank: false,
                editable: true,
                forceSelection: true,
                store: this.timeZones
            }]
        },{
			xtype: 'fieldset',
			title: 'Database settings',
			autoHeight: true,
            defaults: {
                xtype: 'textfield',
                anchor: '100%'
            },
			items: [{
    			fieldLabel: 'Hostname',
    			name: 'db[host]',
                allowBlank: false,
                title: 'Host (server) where database installed (default: localhost)',
    			value: 'localhost'
			},{
    			fieldLabel: 'Username',
    			name: 'db[username]',
                allowBlank: false,
                title: 'Username for connect to database'
			},{
    			fieldLabel: 'Password',
    			name: 'db[password]',
                title: 'Password for connect to database'
			},{
    			fieldLabel: 'DB name',
    			name: 'db[dbname]',
                allowBlank: false,
                title: 'Database name'
			},{
    			fieldLabel: 'Tables prefix',
    			name: 'db[prefix]',
                title: 'Default prefix for all tables in database'
			}]
		},{
			xtype: 'fieldset',
			title: 'Mail settings',
			autoHeight: true,
            defaults: {
                xtype: 'textfield',
                anchor: '100%'
            },
			items: [{
                fieldLabel: 'From address',
                name: 'mail[from][address]',
                title: 'Email address from system messages will be sent'
            },{
    			fieldLabel: 'From caption',
    			name: 'mail[from][caption]',
                value: 'FEEDBACK SUPPORT',
    			title: 'Caption for mail <from> field (default: FEEDBACK SUPPORT)'
			},{
                fieldLabel: 'SMTP server',
                name: 'mail[SMTP]',
                allowBlank: false,
                title: 'SMTP server name from which emails sent'
			},{
                fieldLabel: 'Authentification type',
                name: 'mail[authentificate][auth]',
                value: 'login',
                title: 'Authentification type for mail sending'
			},{
                fieldLabel: 'Authentification username',
                name: 'mail[authentificate][username]',
                allowBlank: false,
                title: 'Username for mail sending'
			},{
                fieldLabel: 'Authentification password',
                name: 'mail[authentificate][password]',
                allowBlank: false,
                title: 'Password for mail sending'
			}]
		}];
        
        this.buttons = [{
            text: 'Update',
            handler: this.update,
            scope: this
        }];
        
        // Init titles
        this.on('render', function(fp){
            fp.getForm().items.each(function(item){
                item.on('render', function(f) {f.getErrorCt().dom.title = f.title;});
            });
        });
        
        this.addEvents('load', 'update', 'failureUpdate');
        
		Setup.Form.superclass.initComponent.apply(this, arguments);
	},
        
    load: function() {
        this.disable();
        Ext.Ajax.request({
            url: this.loadUrl,
            success: function(response, options){
                var resp = Ext.decode(response.responseText);
                if (true !== resp.success) {
                    this.onFailure();
                    return;
                }
                if (resp.data) {
                    this.getForm().items.each(function(item) {
                        if (item.isFormField && item.name) {
                            var name = item.name.replace(/\]/g, "']").replace(/\[/g, "['");
                            try {
                                var value = eval('resp.data.' + name);
                                item.setValue(value);
                            } catch (e) {}
                        } 
                    });
                    this.enable();
                    this.fireEvent('load');
                }
                this.enable();
            },
            failure: this.onFailure.createDelegate(this),
            scope: this
        });
    },
        
    update: function() {
        if (this.getForm().isValid()) {
            var values = this.getForm().getValues();
            this.getForm().items.each(function(item) {
                if (item.isFormField && item.name && item.fieldType && item.fieldType == 'array') {
                    delete values[item.name];
                    values[item.name + '[]'] = item.getValue().split(/\s*,\s*/);
                } 
                if (item.isFormField && item.name && item.getXType() == 'checkbox') {
                    values[item.name] = item.checked ? 1 : 0;
                }
            });
            this.disable();
            Ext.Ajax.request({
                url: this.saveUrl,
                params: values,
                success: function(response, options){
                    if (true !== Ext.decode(response.responseText).success) {
                        this.onFailure();
                        return;
                    }
                    if (confirm('Settings saved successfully. Start application?')) {
                        parent.location='../';
                    }
                    this.enable();
                    this.fireEvent('update');
                },
                failure: this.onFailure.createDelegate(this),
                scope: this
            });
        } else {
            this.onFailure();
        }
    },
    
    onFailure: function() {
        this.enable();
        alert('Load/update settings failed.');
        this.fireEvent('requestFailure');
    }
});