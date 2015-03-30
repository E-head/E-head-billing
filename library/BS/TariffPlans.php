<?php

/**
 * Define tariff plans
 *
 * @version $Id $
 * @package BS
 */
class BS_TariffPlans
{
	const start    = 'start';
	const mini     = 'mini';
	const econom   = 'econom';
	const business = 'business';
    const expert   = 'expert';
    const tycoon   = 'tycoon';

    /**
     * Return collection
     * @return array
     */
    public static function getAll()
    {
    	return array(
    	    self::start    => 'Старт',
    	    self::mini     => 'Мини',
    	    self::econom   => 'Эконом',
    	    self::business => 'Бизнес',
    	    self::expert   => 'Эксперт',
    	    self::tycoon   => 'Олигарх'
    	);
    }

    /**
     * Return collection
     * @return array
     */
    public static function getAllCosts()
    {
    	return array(
    	    self::start    => 0,
    	    self::mini     => 470,
    	    self::econom   => 970,
    	    self::business => 1470,
    	    self::expert   => 2470,
    	    self::tycoon   => 4970
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
     * Get cost by key
     * @return array
     */
    public static function getCost($key)
    {
    	$a = self::getAllCosts();
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