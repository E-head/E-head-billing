Ext.ns('BS.Customers');

BS.Customers.Password = Ext.extend(xlib.form.FormPanel, {
    
    permissions: true,
    
    saveLink: link('admin', 'customers', 'change-password'),
    
    accountId: null,
    
    initComponent: function() {
	
        this.items = [{
        	xtype: 'textfield',
        	hideLabel: true,
        	minLength: 3,
            name: 'password'
        }];
        
        BS.Customers.Password.superclass.initComponent.apply(this, arguments);
        
        if (!Ext.isNumber(+this.accountId)) {
        	throw 'accountId must be specified!';
        }
    },
    
    getWindow: function() {
        var w = new Ext.Window({
            title: 'Пароль',
            resizable: false,
            modal: true,
            width: 300,
            items: [this],
            buttons: [{
                text: 'Сохранить',
                handler: function() {
            		if (this.getForm().isValid()) {
		            	Ext.Msg.show({
		                    title: 'Подтверждение',
		                    msg: 'Вы уверены?',
		                    buttons: Ext.Msg.YESNO,
		                    icon: Ext.MessageBox.QUESTION,
		                    fn: function(b) {
		                        if ('yes' == b) {
				                    this.getForm().submit({
				                        url: this.saveLink,
				                        waitMsg: 'Сохранение...',
				                        params: {
				                    		id: this.accountId
				                    	},
				                        success: function() {
				                            w.close();
				                        }, 
				                        failure: function() {
				                            xlib.Msg.error('Сохранение не удалось.');
				                        },
				                        scope: this
				                    });
		                        }
		            		},
		            		scope: this
		            	});
            		}
                },
                scope: this
            }, {
            	text: 'Отмена',
                handler: function() {
                    w.close();
                },
                scope: this
            }],
            scope: this
        });
        return w;
    }
});