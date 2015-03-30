<?php

/**
 * @todo make view helper
 * @see Zend_View_Helper_Translate
 *
 * @param string $phrase
 * @return string
 */
function lang($phrase) 
{
	return $phrase;
	
    $args = func_get_args();
    
    if (!empty($phrase)) {
        $internal = Zend_Registry::get('config')->ui->language->internal;
        $translationObject = OSDN_Translation::getInstance();
        $translation = $translationObject->translate($phrase, OSDN_Language::getDefaultLocale(), $internal);
    } else {
        $translation = $phrase;
    }
    
    $len = count($args);
    if (1 >= $len) {
        return $translation;
    }

    for($i = 1; $i < $len; $i++) {
        $translation = str_replace('{' . ($i - 1). '}', $args[$i], $translation);
    }
    return $translation;
}