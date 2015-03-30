Ext.ns('BS.Customers');

BS.Customers.List = Ext.extend(Ext.grid.GridPanel, {
    
    loadLink: link('admin', 'customers', 'get-all'),

    addLink: link('admin', 'customers', 'add'),
    
    deleteLink: link('admin', 'customers', 'delete'),
    
    loadMask: true,
	
    autoScroll: true,
    
    monitorResize: true,
    
    autoLoadData: true,
    
    stripeRows: true,
    
//    viewConfig: {
//        getRowClass: function(record) {
//            return 'x-row-success';
//            return 'x-row-error';
//            return 'x-row-expired';
//        }
//    },
    
    initComponent: function() {
    	
        this.sm = new Ext.grid.RowSelectionModel({singleSelect:true});
        
        this.ds = new Ext.data.JsonStore({
            timeout: 300,
	        url: this.loadLink,
	        autoLoad: this.autoLoadData,
	        root: 'rows',
	        totalProperty: 'total',
	        remoteSort: true,
	        sortInfo: {field: 'id', direction: 'DESC'},
	        fields: [
	            {name: 'id'},
	            {name: 'login'},
	            {name: 'company_name'},
	            {name: 'name'},
	            {name: 'email'},
	            {name: 'phone'},
	            {name: 'site'},
	            {name: 'comments'},
	            {name: 'tariff'},
	            {name: 'status'},
	            {name: 'balance', type: 'int'},
	            {name: 'active'},
	            {name: 'expire', type: 'date', dateFormat: xlib.date.DATE_FORMAT_SERVER},
	            {name: 'created', type: 'date', dateFormat: xlib.date.DATE_FORMAT_SERVER}
	        ]
	    });
        
	    this.filtersPlugin = new Ext.grid.GridFilters({
	        filters: [
	            {type: 'string',  dataIndex: 'login'},
	            {type: 'string',  dataIndex: 'company_name'},
	            {type: 'string',  dataIndex: 'name'},
	            {type: 'string',  dataIndex: 'email'},
	            {type: 'string',  dataIndex: 'phone'},
	            {type: 'string',  dataIndex: 'site'},
	            {type: 'string',  dataIndex: 'comments'},
    	        {type: 'date',  dataIndex: 'expire', dateFormat: xlib.date.DATE_FORMAT_SERVER}
	    ]});
	    
	    var actionsPlugin = new xlib.grid.Actions({
	        autoWidth: true,
	        items: [{
                text: 'Редактировать',
                iconCls: 'edit',
                handler: this.onEdit,
                scope: this,
				hidden: !acl.isUpdate('customers')
        	}, {
                text: 'Сменить пароль',
                iconCls: 'edit',
                handler: this.onChangePassword,
                scope: this,
				hidden: !acl.isUpdate('customers')
        	}, {
                text: 'Удалить',
                iconCls: 'delete',
                handler: this.onDelete,
				hidden: !acl.isUpdate('customers')
            }]
	    });
	    /*
	    this.filteringMenu = new Ext.menu.Menu({
	    	defaults: {
	    		checked: false,
	    		group: 'status',
                handler: function(button) {
                    this.filteringMenuButton.toggle(button.value);
                    this.getStore().baseParams = {Xfilter: button.value};
                    this.getStore().load();
                },
                scope: this
	    	},
            items:[{
                text: 'Все заказы',
                checked: true,
                value: 0
            }, {
                text: 'Текущие',
                value: 1
            }, {
            	text: 'Выполненные',
                value: 2
            }, {
            	text: 'Просроченные',
                value: 3
            }],
            scope: this
        });
	    
        this.filteringMenuButton = new Ext.Toolbar.Button({ 
            text: 'Фильтр',
            iconCls: 'filter-icon',
            menu: this.filteringMenu
        });
        */
        this.plugins = [actionsPlugin, this.filtersPlugin];
        
        this.tbar = new Ext.Toolbar({
            items: [{
                text: 'Новый клиент',
                iconCls: 'add',
                handler: this.onAdd,
    			scope: this
            }, '-', this.filtersPlugin.getSearchField({width:200}), 
            ' '//, this.filteringMenuButton, ' '
            ],
//            plugins: [new xlib.Legend.Plugin({
//                items: [{
//                    color: '#99FF99',
//                    text: 'Выполненные'
//                }, {
//                    color: '#FFFF99',
//                    text: 'Просроченные'
//                }, {
//                    color: '#FF9999',
//                    text: 'Конфликт'
//                }]
//            })],
            scope: this
        });
        
        //this.tbar.add(this.filteringMenuButton);
        
        this.bbar = new xlib.PagingToolbar({
            plugins: [this.filtersPlugin],
            pageSize: 30,
            store: this.ds
        });
        
        this.autoExpandColumn = Ext.id();
        
		this.columns = [{
            header: '№', 
            dataIndex: 'id',
            width: 40,
            sortable: true
        }, {
            header: 'Логин', 
            width: 80,
            dataIndex: 'login',
            sortable: true
        }, {
            header: 'Организация', 
            width: 200,
            dataIndex: 'company_name',
            sortable: true
        }, {
            header: 'ФИО контактного лица', 
            id: this.autoExpandColumn,
            dataIndex: 'name',
            minWidth: 150,
            sortable: true
        }, {
            header: 'Email', 
            width: 150,
            dataIndex: 'email',
            hidden: true,
            sortable: true
        }, {
            header: 'Телефон', 
            width: 150,
            dataIndex: 'phone',
            hidden: true,
            sortable: true
        }, {
            header: 'Сайт', 
            width: 200,
            dataIndex: 'site',
            hidden: true,
            sortable: false
        }, {
            header: 'Тариф', 
            width: 100,
            dataIndex: 'tariff',
            sortable: true,
            renderer: function(v) {
                var index = Ext.each(BS.tariffPlans, function(item) {
                    return item[0] != v;
                });
                return Ext.isNumber(index) ? BS.tariffPlans[index][1] : v;
            }
        }, {
            header: 'Сатус', 
            width: 100,
            dataIndex: 'status',
            sortable: true,
            renderer: function(v, metaData) {
                var index = Ext.each(BS.statuses, function(item) {
                    return item[0] != v;
                });
                if (Ext.isNumber(index)) {
                    switch (BS.statuses[index][0]) {
                        case 'active':
                            metaData.css = 'x-row-success';
                            break;
                        case 'suspended':
                            metaData.css = 'x-row-expired';
                            break;
                        case 'inactive':
                            metaData.css = 'x-row-error';
                            break;
                    }
                    return BS.statuses[index][1]
                } else { 
                    return v;
                }
            }
        }, {
            header: 'Баланс', 
            width: 60,
            dataIndex: 'balance',
            sortable: false
        }, {
            header: 'Истекает',
            width: 70,
            sortable: true,
            renderer: xlib.dateRenderer(xlib.date.DATE_FORMAT),
            dataIndex: 'expire'
        }, {
            header: 'Создан',
            width: 70,
            sortable: true,
            renderer: xlib.dateRenderer(xlib.date.DATE_FORMAT),
            dataIndex: 'created'
        }];
        BS.Customers.List.superclass.initComponent.apply(this, arguments);
        
        this.on('rowdblclick', this.onEdit, this);
    },
	
    onAdd: function() {
        this.showForm(new BS.Customers.Add().getWindow());
    },
    
    onEdit: function(g, rowIndex) {
        var record = g.getStore().getAt(rowIndex);
        this.showForm(new BS.Customers.Edit({customerId: record.get('id')}).getWindow());
    },
    
    onChangePassword: function(g, rowIndex) {
        var record = g.getStore().getAt(rowIndex);
        this.showForm(new BS.Customers.Password({accountId: record.get('id')}).getWindow());
    },
    
    showForm: function(w) {
        w.on('close', function() {
            this.getStore().reload();
        }, this);
        w.show();
    },
    
    
    onDelete: function(g, rowIndex) {
        Ext.Msg.show({
            title: 'Подтверждение',
            msg: 'Вы уверены?',
            buttons: Ext.Msg.YESNO,
            fn: function(b) {
                if ('yes' == b) {
                    Ext.Ajax.request({
                        url: g.deleteLink,
                        success: function(res) {
                            var errors = Ext.decode(res.responseText).errors;
                            if (errors) {
                                xlib.Msg.error(errors[0].msg);
                                return;
                            }
                            g.getStore().reload();
                        },
                        failure: Ext.emptyFn(),
                        params: {id: g.getStore().getAt(rowIndex).get('id')}
                    });
                }
            },
            icon: Ext.MessageBox.QUESTION
        });
    }
});

Ext.reg('BS.Customers.List', BS.Customers.List);