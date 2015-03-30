Ext.ns('BS.Customers');

BS.Customers.Mail = Ext.extend(xlib.form.FormPanel, {
    
    permissions: true,
    
    defaultType: 'textfield',
    
    sendLink: link('admin', 'customers', 'send-mail'),
    
    labelWidth: 120,
    
    initialData: null,
    
    messageBody: null,
    
    initComponent: function() {
    
		this.messageBody = new Ext.form.TextArea({
	    	fieldLabel: 'Сообщение',
	    	name: 'body',
	    	height: 250
		});
	
        this.items = [{
            xtype: 'hidden',
            name: 'id'
        }, {
            fieldLabel: 'Имя',
            name: 'name',
            readOnly: true
        }, {
            fieldLabel: 'Email',
            name: 'email',
            vtype: 'email',
            readOnly: true
        }, this.messageBody];

        BS.Customers.Mail.superclass.initComponent.apply(this, arguments);
    },
    
    getWindow: function() {
        var w = new Ext.Window({
            title: 'Уведомление клиента',
            resizable: false,
            modal: true,
            width: 500,
            items: [this],
            buttons: [{
                text: 'Отправить',
                handler: function() {
                    this.getForm().submit({
                        url: this.sendLink,
                        waitMsg: 'Отправление...',
                        success: function() {
                            w.close();
                        }, 
                        failure: function() {
                            xlib.Msg.error('Отправление не удалось.');
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
        
        if (Ext.isObject(this.initialData)) {
            w.on('show', function() {
                this.getForm().setValues(this.initialData);
                this.setBody();
                //xlib.Msg.info('Реализовать посылку мыла!!!');
            }, this);
        }
        return w;
    },
    
    setBody: function() {
    	this.messageBody.setValue('Здравствуйте, ' + this.initialData.name + '.\n\n' 
            + 'Система e-head активирована. Вы можете получить к ней доступ по адресу ' 
            + this.initialData.site + '\n\n' + 'Логин: admin\n'
            + 'Пароль: ' + this.initialData.site_pwd + '\n\n'
            + 'Если у вас возникнут какие-либо вопросы о работе системы, ' 
            + 'вы можете задать их службе поддержки по телефону, указанному на сайте '
            + 'http://e-head.ru/ или письменно через форму обратной связи на странице '
            + 'http://e-head.ru/index.php/home/podderzhka\n\n'
            + 'Приятной вам работы.'
        );
    }
});