Ext.ns('BS');

BS.Menu = function(username, rolename) {
	username = username || '';
	rolename = rolename || '';
	return [{
	    xtype: 'box',
	    autoEl: {
	        tag: 'div',
	        style: 'cursor: pointer;',
	        qtip: 'e-head.ru',
	        cls: 'e-head-logo'
	    },
	    listeners: {
	        render: function(box) {
	            box.el.on('click', function() {
	                window.open('http://e-head.ru');
	            })
	        }
	    }
	}, ' ', ' ', { xtype: 'label', text: 'BILLING', style: 'color:red'}, ' ', ' ', '-', {
        text: 'Клиенты',
        iconCls: 'customers-icon',
        hidden: !acl.isView('customers'),
        handler: function() {
            BS.System.Layout.getTabPanel().add({
                title: 'Клиенты',
                iconCls: 'customers-icon',
                entity: 'customers',
                xtype: 'BS.Customers.List',
                id: 'BS.Customers.List'
            });
        }
    }, {
    	text: 'Отчёт',
    	iconCls: 'customers-icon',
    	hidden: !acl.isView('customers'),
    	handler: function() {
    		window.open('/admin/report/customers');
    	}
    }, {
		text: 'Менеджер доступа',
		iconCls: 'accounts_manager-icon',
		hidden: !acl.isView('admin'),
		handler: function() {
			BS.System.Layout.getTabPanel().add({
				iconCls: 'accounts_manager-icon',
				xtype: 'xlib.acl.layout',
				id: 'xlib.acl.layout'
			});
		}
	}, '->', {
        text: 'Выход',
        iconCls: 'exit-icon',
        handler: function() {
            window.location.href = '/default/index/logout';
        }
    }];
}