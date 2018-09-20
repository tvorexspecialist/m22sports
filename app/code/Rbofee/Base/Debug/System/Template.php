<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Debug\System;

class Template
{
    public static $varWrapper = '<div class="rbofee-base-debug-wrapper"><code>%s</code></div>';

    public static $string = '"<span class="rbofee-base-string">%s</span>"';

    public static $var = '<span class="rbofee-base-var">%s</span>';

    public static $arrowsOpened =  '<span class="rbofee-base-arrow" data-opened="true">&#x25BC;</span>
        <div class="rbofee-base-array">';

    public static $arrowsClosed = '<span class="rbofee-base-arrow" data-opened="false">&#x25C0;</span>
        <div class="rbofee-base-array rbofee-base-hidden">';

    public static $arrayHeader = '<span class="rbofee-base-info">array:%s</span> [';

    public static $array = '<div class="rbofee-base-array-line" style="padding-left:%s0px">
            %s  => %s
        </div>';

    public static $arrayFooter = '</div>]';

    public static $arrayKeyString = '"<span class="rbofee-base-array-key">%s</span>"';

    public static $arrayKey = '<span class="rbofee-base-array-key">%s</span>';

    public static $arraySimpleVar = '<span class="rbofee-base-array-value">%s</span>';

    public static $arraySimpleString = '"<span class="rbofee-base-array-string-value">%s</span>"';

    public static $objectHeader = '<span class="rbofee-base-info" title="%s">Object: %s</span> {';

    public static $objectMethod = '<div class="rbofee-base-object-method-line" style="padding-left:%s0px">
            #%s
        </div>';

    public static $objectMethodHeader = '<span style="margin-left:%s0px">Methods: </span>
        <span class="rbofee-base-arrow" data-opened="false">◀</span>
        <div class="rbofee-base-array  rbofee-base-hidden">';

    public static $objectMethodFooter = '</div>';

    public static $objectFooter = '</div> }';

    public static $debugJsCss = '<script>
            var rbofeeToggle = function() {
                if (this.dataset.opened == "true") {
                    this.innerHTML = "&#x25C0";
                    this.dataset.opened = "false";
                    this.nextElementSibling.className = "rbofee-base-array rbofee-base-hidden";
                } else {
                    this.innerHTML = "&#x25BC;";
                    this.dataset.opened = "true";
                    this.nextElementSibling.className = "rbofee-base-array";
                }
            };
            document.addEventListener("DOMContentLoaded", function() {
                arrows = document.getElementsByClassName("rbofee-base-arrow");
                for (i = 0; i < arrows.length; i++) {
                    arrows[i].addEventListener("click", rbofeeToggle,false);
                }
            });
        </script>
        <style>
            .rbofee-base-debug-wrapper {
                background-color: #263238;
                color: #ff9416;
                font-size: 13px;
                padding: 10px;
                border-radius: 3px;
                z-index: 1000000;
                margin: 20px 0;
            }
            .rbofee-base-debug-wrapper code {
                background: transparent !important;
                color: inherit !important;
                padding: 0;
                font-size: inherit;
                white-space: inherit;
            }
            .rbofee-base-info {
                color: #82AAFF;
            }
            .rbofee-base-var, .rbofee-base-array-key {
                color: #fff;
            }
            .rbofee-base-array-value {
                color: #C792EA;
                font-weight: bold;
            }
            .rbofee-base-arrow {
                cursor: pointer;
                color: #82aaff;
            }
            .rbofee-base-hidden {
                display:none;
            }
            .rbofee-base-string, .rbofee-base-array-string-value {
                font-weight: bold;
                color: #c3e88d;
            }
            .rbofee-base-object-method-line {
                color: #fff;
            }
        </style>';
}
