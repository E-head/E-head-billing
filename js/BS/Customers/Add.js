Ext.ns('BS.Customers');

BS.Customers.Add = Ext.extend(xlib.form.FormPanel, {
    
    permissions: true,
    
    defaultType: 'textfield',
    
    saveLink: link('admin', 'customers', 'add'),
    
    labelWidth: 80,
    
    initComponent: function() {
        
        this.items = [{
        	fieldLabel: 'Логин',
        	name: 'login'
        }, {
            fieldLabel: 'Пароль',
            name: 'password'
        }];
        
        BS.Customers.Add.superclass.initComponent.apply(this, arguments);
    },
    
    getWindow: function() {
        var w = new Ext.Window({
            title: 'Редактирование клиента',
            resizable: false,
            modal: true,
            width: 300,
            items: [this],
            buttons: [{
                text: 'Сохранить',
                handler: function() {
                    this.getForm().submit({
                        url: this.saveLink,
                        waitMsg: 'Сохранение...',
                        success: function() {
                            w.close();
                        }, 
                        failure: function() {
                            xlib.Msg.error('Сохранение не удалось.');
                        },
                        scope: this
                    });
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