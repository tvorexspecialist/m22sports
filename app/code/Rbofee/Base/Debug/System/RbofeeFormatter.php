<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Debug\System;

class RbofeeFormatter extends \Monolog\Formatter\LineFormatter
{
    /**
     * @param array $record
     *
     * @return string
     */
    public function format(array $record)
    {
        $output = $this->format;
        $output = str_replace('%datetime%', date('H:i d/m/Y'), $output);
        $output = str_replace('%message%', $record['message'], $output);
        return $output;
    }
}
