<?php

/**
 * The plugin help to fill params as
 * "limit", "order", "sort", "filters"
 * into SQL query
 *
 * @category    OSDN
 * @package     OSDN_Db_Plugin
 * @version     $Id: Select.php 10013 2009-07-01 16:01:27Z yaroslav $
 */
class OSDN_Db_Plugin_Select
{
	const LIMIT    = 'limit';
	
	const START    = 'start';
	
	const SORT     = 'sort';
	
	const DIR      = 'dir';
	
	const FILTER   = 'filter';
	
    /**
     * Predefined table
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;
    
    /**
     * Detault adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * The tables cols
     *
     * @var array
     */
    protected $_fields = array();
    
    /**
     * Predefined select statement
     *
     * @var Zend_Db_Select
     */
    protected $_selectStatement;
    
    /**
     * array of param names
     *
     * @var array
     */
    protected $_paramNames = array(
        self::LIMIT     => self::LIMIT,
        self::START     => self::START,
        self::SORT      => self::SORT,
        self::DIR       => self::DIR,
        self::FILTER    => self::FILTER
    );
    
    /**
     * The input params
     * @var array
     */
    protected $_params;
    
    /**
     * Define allowed limit
     *
     * @var boolean
     */
    protected $_allowLimit = true;
    
    /**
     * Constructor
     *
     * @param Zend_Db_Table_Abstract $table  the working table
     * @param Zend_Db_Select $statement      the working statement
     * @param array $fields                  available fields
     */
    public function __construct(
        Zend_Db_Table_Abstract $table = null,
        Zend_Db_Select & $statement,
        array $fields = array()
    ) {
        $this->_table = $table;
        $this->_adapter = $statement->getAdapter();
        $this->_selectStatement = $statement;

        if (empty($fields) && !is_null($table)) {
            $fields = $table->info(OSDN_Db_Table_Abstract::COLS);
        }
        
        $this->_fields = $fields;
    }
    
    /**
     * Set param names
     *
     * @param array $data            param names
     * @return void
     */
    public function setParamNames($data)
    {
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $this->_paramNames)) {
                $this->_paramNames[$k] = $v;
            }
        }
    }
    
    /**
     * Get param names
     *
     * @param array $name name of parameter
     * @return mixed
     */
    public function getParamNames($name = null)
    {
        if (array_key_exists($name, $this->_paramNames)) {
            return $this->_paramNames[$name];
        }
        
        return $this->_paramNames;
    }
    
    /**
     * Parse order, limit and filter params and add this to statement
     *
     * @param array $params            params
     * @return Zend_Db_Select          the modified statement
     */
    public function parse(array $params)
    {
        $this->_params = $params;
        $this->parseFilters();
        $this->parseOrders();
        $this->parseLimits();
        return $this->_selectStatement;
    }

    /**
     * Parse order params and add this to statement
     *
     * @param array $params            params
     * @return Zend_Db_Select          the modified statement
     */
    public function parseOrders($params = null)
    {
        if (!isset($params)) {
            $params = $this->_params;
        }
        
        if (!isset($params[self::SORT]) || !in_array($params[self::SORT], $this->_fields)) {
            return $this->_selectStatement;
        }
        
        if (!isset($params[self::DIR]) || !in_array(strtolower($params[self::DIR]), array('asc', 'desc'))) {
            $params[self::DIR] = 'asc';
        }
        
        $orderClause = $this->getAlias($params[self::SORT], false) . " " . strtoupper($params[self::DIR]);
        return $this->_selectStatement->order($orderClause);
    }
    
    /**
     * Parse limit params and add this to statement
     *
     * @param array $params            params
     * @return Zend_Db_Select          the modified statement
     */
    public function parseLimits($params = null)
    {
        if (true !== $this->_allowLimit) {
            return $this->_selectStatement;
        }
        
        if (!isset($params)) {
            $params = $this->_params;
        }

        if (empty($params[self::LIMIT]) || !isset($params[self::START])) {
            return $this->_selectStatement;
        }
        
        return $this->_selectStatement->limit($params[self::LIMIT], $params[self::START]);
    }
    
    /**
     * Parse filter params and add this to statement
     *
     * @param array $params            params
     * @return Zend_Db_Select          the modified statement
     */
    public function parseFilters(array $params = array())
    {
        if (empty($params)) {
            $params = $this->_params;
        }

        if (!array_key_exists(self::FILTER, $params) || !is_array($params[self::FILTER])) {
            return $this->_selectStatement;
        }
        
        foreach ($params[self::FILTER] as $filter) {
            
            if (empty($filter['data'])) {
                continue;
            }
            $data = $filter['data'];
            
            if (empty($filter['field']) || empty($data['type']) || !isset($data['value'])) {
                continue;
            }
            
            $field = $filter['field'];
            $value = $data['value'];
            
            if ('search' != $data['type']) {
                if (!in_array($field, $this->_fields)) {
                    continue;
                }
                $field = $this->getAlias($field);
            }
            
            switch($data['type']) {
                case 'string':
                    $this->_selectStatement->where($field
                        . $this->_adapter->quoteInto(' LIKE ?', '%' . $value. '%'));
                    break;
                
                case 'stringstrict':
                    $this->_selectStatement->where($field . ' = ?', $value);
                    break;
                    
                case 'search':
                    $fields = explode(',', $field);
                    if (empty($fields)) {
                        continue;
                    }
                    
                    $collection = array();
                    foreach ($fields as $field) {
                        $field = trim($field);
                        if (!in_array($field, $this->_fields)) {
                            continue;
                        }
                        
                        $field = $this->getAlias($field);
                        $collection[] = $this->_adapter->quoteInto($field . ' LIKE ?', '%' . $value . '%');
                    }
                    
                    if (!empty($collection)) {
                        $this->_selectStatement->where(join(' OR ', $collection));
                    }
                    break;
                    
                case 'list' :
                    if (false !== strpos($value, ',')) {
                        $value = explode(',', $value);
                    } else {
                        $value = (array) $value;
                    }
                    
                    $this->_selectStatement->where($field
                        . ' IN (' . $this->_adapter->quote($value) . ')');
                    break;
                    
                case 'boolean':
                    $this->_selectStatement->where($filter . ' = ?', $value);
                    break;

                case 'numeric' :
                    if (empty($data['comparison'])) {
                        continue;
                    }
                    
                    switch ($data['comparison']) {
                        case 'eq':
                            $this->_selectStatement->where($field . ' = ?', $value);
                            break;
                            
                        case 'lt':
                            $this->_selectStatement->where($field . ' < ?', $value);
                            break;
                        
                        case 'gt':
                            $this->_selectStatement->where($field . ' > ?', $value);
                            break;
                        
                        case 'neq':
                            $this->_selectStatement->where($field . ' != ?', $value);
                            break;
                    }
                    break;
                    
                case 'date':
                    if (empty($data['comparison'])) {
                        continue;
                    }
                    
                    switch ($data['comparison']) {
                        case 'eq':
                            $this->_selectStatement->where($field . ' = ?', date('Y-m-d', strtotime($value)));
                            break;
                            
                        case 'lt':
                            $this->_selectStatement->where($field . ' < ?', date('Y-m-d', strtotime($value)));
                            break;
                            
                        case 'gt':
                            $this->_selectStatement->where($field . ' > ?', date('Y-m-d', strtotime($value)));
                            break;
                    }
                    break;
                        
                default:
                    break;
                
            }
        }

        return $this->_selectStatement;
    }
    
    /**
     * Get column alias
     *
     * @param string $field     The field name
     * @param bool $escape      Escape flag
     *      If true then escape via adapter quote indentifier
     * @return string           The field name
     */
    public function getAlias($field, $escape = true)
    {
        $index = array_search($field, $this->_fields);
        if (!is_int($index)) {
            $field = $index;
        }
        
        if (true === $escape) {
            $field = $this->_adapter->quoteIdentifier($field);
        }
        
        return $field;
    }
    
    /**
     * Set limit avialable
     *
     * @param bool $flag
     * @return OSDN_Db_Plugin_Select
     */
    public function allowLimit($flag)
    {
        $this->_allowLimit = (boolean) $flag;
        return $this;
    }

    /**
     * Calculate total count
     *
     * @return int|false
     */
    public function getTotalCount()
    {
        $s = clone $this->_selectStatement;
        $s->reset(Zend_Db_Select::COLUMNS);
        $s->reset(Zend_Db_Select::LIMIT_COUNT);
        $s->reset(Zend_Db_Select::LIMIT_COUNT);
        $s->reset(Zend_Db_Select::ORDER);
        
        $s->columns(array('c' => new Zend_Db_Expr('COUNT(*)')));
        
        try {
            $count = $s->query()->fetchColumn(0);
            return $count;
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
            
            return false;
        }
    }
}