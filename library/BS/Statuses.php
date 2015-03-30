<?php

/**
 * Define statuses
 *
 * @version $Id $
 * @package BS
 */
class BS_Statuses
{
    const active    = 'active';
    const suspended = 'suspended';
    const inactive  = 'inactive';

    /**
     * Return collection
     * @return array
     */
    public static function getAll()
    {
    	return array(
    	    self::active    => 'Активно',
    	    self::suspended => 'Приостановлено',
    	    self::inactive  => 'Не активно'
    	);
    }

    /**
     * Get title by key
     * @return array
     */
    public static function get($key)
    {
        $a = self::getAll();
        return isset($a[$key]) ? $a[$key] : '';
    }
    
    /**
     * Check if type is present
     *
     * @param int
     * @return bool
     */
    public static function hasType($type)
    {
    	$types = array_keys(self::getTypes());
        return false !== in_array($type, $types);
    }
}