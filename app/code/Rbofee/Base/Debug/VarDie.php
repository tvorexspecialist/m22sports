<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Debug;

class VarDie
{
    public static function execute()
    {
        if (VarDump::isAllowed()) {
            foreach (func_get_args() as $var) {
                System\Beautifier::getInstance()->beautify(VarDump::dump($var));
            }
            VarDump::rbofeeExit();
        }
    }

    public static function backtrace()
    {
        if (VarDump::isAllowed()) {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            foreach ($backtrace as $route) {
                System\Beautifier::getInstance()->beautify(
                    VarDump::dump(
                        [
                            'action' => $route['class'] . $route['type'] . $route['function'] . '()',
                            'object' => $route['object'],
                            'args' => $route['args'],
                            'file' => $route['file'] . ':' . $route['line']
                        ]
                    )
                );
            }
            VarDump::rbofeeExit();
        }
    }
}
