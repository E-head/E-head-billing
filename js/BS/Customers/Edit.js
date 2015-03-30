Ext.ns('BS.Customers');

BS.Customers.Edit = Ext.extend(xlib.form.FormPanel, {
    
    permissions: true,
    
    defaultType: 'textfield',
    
    saveLink: link('admin', 'customers', 'update'),
    
    loadLink: link('admin', 'customers', 'get'),
    
    customerId: null,
    
    labelWidth: 150,
    
    initComponent: function() {
        
        this.items = [{
            xtype: 'hidden',
            name: 'id'
        }, {
        	fieldLabel: 'Логин',
        	xtype: 'displayfield',
        	name: 'login'
        }, {
            fieldLabel: 'Имя',
            name: 'name'
        }, {
            fieldLabel: 'Email',
            name: 'email',
            vtype: 'email'
        }, {
            fieldLabel: 'Телефон',
            name: 'phone',
            allowBlank: true
        }, {
        	fieldLabel: 'Сайт',
        	name: 'site',
        	allowBlank: true
        }, {
        	fieldLabel: 'Пароль к сайту',
        	name: 'site_pwd',
        	allowBlank: true
        }, {
        	xtype: 'checkbox',
        	fieldLabel: 'Юр. лицо',
        	name: 'iscompany',
        	inputValue: 1
        }, {
        	fieldLabel: 'Название организации',
        	name: 'company_name',
        	allowBlank: true
        }, {
        	fieldLabel: 'Юридический адрес',
        	name: 'company_address',
        	allowBlank: true
        }, {
        	fieldLabel: 'Почтовый адрес',
        	name: 'company_postaddress',
        	allowBlank: true
        }, {
        	fieldLabel: 'ИНН',
        	name: 'company_inn',
        	allowBlank: true
        }, {
        	fieldLabel: 'КПП',
        	name: 'company_kpp',
        	allowBlank: true
        }, {
        	fieldLabel: 'р/с',
        	name: 'company_rs',
        	allowBlank: true
        }, {
        	fieldLabel: 'Банк',
        	name: 'company_bank',
        	allowBlank: true
        }, {
        	fieldLabel: 'к/с',
        	name: 'company_ks',
        	allowBlank: true
        }, {
        	fieldLabel: 'БИК',
        	name: 'company_bik',
        	allowBlank: true
        }, {
        	fieldLabel: 'ОГРН',
        	name: 'company_ogrn',
        	allowBlank: true
        }, {
        	fieldLabel: 'Генеральный директор',
        	name: 'company_director',
        	allowBlank: true
        }, {
        	xtype: 'xlib.form.combobox',
        	fieldLabel: 'Тариф',
        	name: 'tariff',
        	hiddenName: 'tariff',
            store: new Ext.data.ArrayStore({
                fields: ['name', 'title'],
                data: BS.tariffPlans
            }),
            valueField: 'name',
			displayField: 'title',
			typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            editable: false,
            lazyInit: false,
            allowBlankOption: true,
            allowBlank: true
        }, {
        	xtype: 'xlib.form.combobox',
        	fieldLabel: 'Статус',
        	name: 'status',
        	hiddenName: 'status',
        	store: new Ext.data.ArrayStore({
        		fields: ['name', 'title'],
        		data: BS.statuses
        	}),
        	valueField: 'name',
        	displayField: 'title',
        	typeAhead: true,
        	mode: 'local',
        	triggerAction: 'all',
        	editable: false,
        	lazyInit: false,
        	allowBlank: false
        }, {
        	xtype: 'numberfield',
        	fieldLabel: 'Баланс',
        	name: 'balance',
        	allowBlank: true
        }, {
        	xtype: 'xlib.form.DateField',
        	fieldLabel: 'Истекает',
        	format: xlib.date.DATE_FORMAT,
        	name: 'expire',
        	hiddenName: 'expire',
        	allowBlank: true
        }, {
        	xtype: 'textarea',
        	fieldLabel: 'Комментарии',
        	name: 'comments',
        	maxLength: 4096,
        	height: 150,
        	allowBlank: true
        }, {
        	xtype: 'checkbox',
            fieldLabel: 'Активнo',
            name: 'active',
            inputValue: 1
        }];

        BS.Customers.Edit.superclass.initComponent.apply(this, arguments);
        
        this.getForm().on('actioncomplete', function(f, action) {
            if ('load' == action.type) {
                var login = f.findField('login').getValue();
                var siteField = f.findField('site');
                if (Ext.isEmpty(siteField.getValue())) {
                    siteField.setValue('http://' + login + '.e-head.ru/');
                }
            }
        }, this);
    },
    
    getWindow: function() {
        var w = new Ext.Window({
            title: 'Редактирование клиента',
            resizable: false,
            modal: true,
            width: 600,
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
            }, {
                text: 'Уведомление',
                handler: this.showMailForm,
                scope: this
            }],
            scope: this
        });
        
        w.on('show', function() {
            this.getForm().load({
            	waitMsg: ' ',
            	params: {id: this.customerId},
            	url: this.loadLink
            });
        }, this);
        return w;
    },
    
    showMailForm: function() {
    	new BS.Customers.Mail({
            initialData: this.getForm().getValues()
        }).getWindow().show();
    }
});