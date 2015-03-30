Ext.ns('xlib.grid');

xlib.grid.Actions = function(config){
    Ext.apply(this.rowConfig, config || {});
    xlib.grid.Actions.superclass.constructor.call(this);
};

Ext.extend(xlib.grid.Actions, Ext.util.Observable, {
    
    rowConfig: {
        actionsType: 'context'
    },
    
    init : function(grid) {

        this.grid = grid;
        if (!this.rowConfig.items) {
            return;
        }
        
        var item = this.rowConfig.items;
        this.grid.rowActionsConfig = this.rowConfig.items;
        this.grid.on({
            rowcontextmenu: this.onRowContextMenu,
            scope: this
        });
        
        if (this.rowConfig.actionsType == 'context' && !Ext.isOpera) {
            return;
        }
        
        var actions = [];
        var handlers = {};

        if (!Ext.isArray(this.rowConfig.items)) {
            this.rowConfig.items = [this.rowConfig.items];
        }
        
        Ext.each(this.rowConfig.items, function(item) {
            if ('function' == Ext.type(item) || item.hidden || item.disabled) {
                return;
            }
            
            var isSeparator = item instanceof Ext.menu.Separator || item == '-';
            if (item && !isSeparator) {
                actions.push({iconCls: item.iconCls, qtip: item.text});
                handlers[item.iconCls] = item.handler || Ext.emptyFn;
            }
        }, this);
        
        if (0 == actions.length) {
            return;
        }
        
        var rowAction = new Ext.ux.grid.RowActions({
            header: this.rowConfig.header || '&nbsp;',
            autoWidth: this.rowConfig.autoWidth,
            actions: actions,
            listeners: {
                'action': function(grid, record, action, row, col) {
                    if (handlers[action]) {
                        handlers[action](grid, row);
                    }
                }
            }
        });
        
        var cm = grid.getColumnModel().config;
        cm = cm.concat(rowAction);
        grid.getColumnModel().setConfig(cm);
        
        rowAction.init(grid);
    },

    onRowContextMenu: function (g, rowIndex, e) {
        
        e.stopEvent();
        
        var record = g.getStore().getAt(rowIndex);
        if (!g.getSelectionModel().isIdSelected(record.get('id'))) {
        	g.getSelectionModel().selectRow(rowIndex);
        }
        
        var menu = new Ext.menu.Menu();
        var count = 0;
        
        Ext.each(g.rowActionsConfig, function(i) {
            
            var item = null;
            if ('function' === typeof i) {
                item = i(g, rowIndex, e);
            } else {
                item = i;
            }
            
            if (false !== item) {
                var isSeparator = item instanceof Ext.menu.Separator || item == '-';
                if (!isSeparator && !(item.hidden || item.disabled)) {
                    item = new Ext.menu.Item(item);
                    
                    if (item.handler) {
                        var h = item.handler;
                        item.setHandler(function(){
                            h.createDelegate(this, [g, rowIndex, e])();
                        }, item.scope || window);
                    }
                    count++;
                }
                
                menu.add(item);
            }
                                    
        }, this);
        
        if (count > 0) {
            menu.showAt(e.getXY());
        }
    }
});