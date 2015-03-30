Ext.onReady(function(){

    Ext.BLANK_IMAGE_URL = '/js/Library/extjs/framework/resources/images/default/s.gif';
    Ext.enableListenerCollection = true;
    Ext.QuickTips.init();
    
    Ext.form.DateField.prototype.format = 'd-m-Y';
    Ext.DatePicker.prototype.startDay = 1;
    
    Ext.menu.RangeMenu.prototype.icons = {
        gt: '/js/Library/extjs/plugins/grid/GridFiltering/img/greater_then.png', 
        lt: '/js/Library/extjs/plugins/grid/GridFiltering/img/less_then.png',
        eq: '/js/Library/extjs/plugins/grid/GridFiltering/img/equals.png',
        neq: '/js/Library/extjs/plugins/grid/GridFiltering/img/not_equals.png'
    };
    Ext.grid.filter.StringFilter.prototype.icon = '/js/Library/extjs/plugins/grid/GridFiltering/img/find.png';
    
    // disable stateful
    Ext.Window.prototype.stateful = false;
    Ext.form.Field.prototype.stateful = false;
    Ext.form.FieldSet.prototype.stateful = false;
    
    Ext.namespace('xlib.date');
    xlib.date.TIME_FORMAT = 'H:i';
    xlib.date.DATE_FORMAT = 'd-m-Y';
    xlib.date.DATE_WEEK_FORMAT = 'd-m-Y, l';
    xlib.date.DATE_TIME_WITHOUT_SECONDS_FORMAT = 'd-m-Y H:i';
    xlib.date.DATE_TIME_FORMAT = 'd-m-Y H:i:s';
    xlib.date.DATE_TIME_FORMAT_SERVER = 'Y-m-d H:i:s';
    xlib.date.DATE_FORMAT_SERVER = 'Y-m-d';
   
    Ext.Ajax.on({
        requestexception: function(conn, response, options) {
            switch (response.status) {
                case 401:
                    alert("Авторизация истекла! \n Вы будете перенаправлены на страницу входа.");
                    window.location.reload();
                    break;
                    
                case 404:
                    xlib.Msg.error('Страница не найдена');
                    break;
                    
                case 405:
                
                    var t = ['<b>Нет прав!</b>'];
                    
                    var result = xlib.decode(response.responseText);
                    if (Ext.isArray(result.trace) && result.trace.length > 0) {

                        t = t.concat(['<table style="padding-top: 20px">', '<tr>', '<td colspan="2"><b>Запрашиваемый ресурс:</b></td></tr>']);
                        
                        Ext.each(result.trace, function(i) {
                            t.push('<tr><td><b>Ресурс:</b> <i>' + i.resource.join('->') + '</i></td></tr>');
                            t.push('<tr><td><b>Привилегия:</b> <i>' + i.privilege + '</i></td></tr>');
                        }, this);
                        
                        t.push('</table>');
                    }
                        
                    xlib.Msg.error(t.join(''));
                    break;
                    
                case 500:
                	xlib.Msg.error('Ошибка сервера');
                    break;
            }
        }  
    });
    
    xlib.LoadingMask.hide();
    
    Ext.ns('BS');
    BS.tariffPlans = [
        ['start', 'Старт'], 
        ['mini', 'Мини'], 
        ['econom', 'Эконом'], 
        ['business', 'Бизнес'],
        ['expert', 'Эксперт'],
        ['tycoon', 'Олигарх']
    ];
    BS.statuses = [
        ['active', 'Активно'], 
        ['suspended', 'Приостановлено'], 
        ['inactive', 'Не активно']
    ];
    
    xlib.applicationInitialization(function() {
    	
        Ext.ns('BS.System');
        
        BS.System.Layout = new xlib.Layout.Workspace({
    		mainMenu: BS.Menu()
        });
        
        BS.System.Layout.getTabPanel().add({
            title: 'Клиенты',
            iconCls: 'customers-icon',
            entity: 'customers',
            xtype: 'BS.Customers.List',
            id: 'BS.Customers.List'
        });
       
        BS.System.Layout.doLayout();
    });
    
});