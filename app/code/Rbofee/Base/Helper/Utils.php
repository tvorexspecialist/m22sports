<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Utils extends AbstractHelper
{
    public function _exit($code = 0)
    {
        $exit = create_function('$a', 'exit($a);');
        $exit($code);
    }

    public function _echo($a)
    {
        $echo = create_function('$a', 'echo $a;');
        $echo($a);
    }
}
