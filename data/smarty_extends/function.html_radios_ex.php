<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_radios} function plugin
 *
 * File:       function.html_radios.php<br>
 * Type:       function<br>
 * Name:       html_radios<br>
 * Date:       24.Feb.2003<br>
 * Purpose:    Prints out a list of radio input types<br>
 * Input:<br>
 *           - name       (optional) - string default "radio"
 *           - values     (required) - array
 *           - options    (optional) - associative array
 *           - checked    (optional) - array default not set
 *           - separator  (optional) - ie <br> or &nbsp;
 *           - output     (optional) - the output next to each radio button
 *           - assign     (optional) - assign the output as an array to this variable
 * Examples:
 * <pre>
 * {html_radios values=$ids output=$names}
 * {html_radios values=$ids name='box' separator='<br>' output=$names}
 * {html_radios values=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * @link http://smarty.php.net/manual/en/language.function.html.radios.php {html_radios}
 *      (Smarty online manual)
 * @author     Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author credits to Monte Ohrt <monte at ohrt dot com>
 * @version    1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_radios_ex($params, &$smarty)
{
    if (!is_callable('smarty_function_escape_special_chars')) {
        require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');
    }

    $name = 'radio';
    $values = null;
    $options = null;
    $selected = null;
    $separator = '';
    $labels = true;
    $label_ids = true;
    $output = null;
    $extra = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'tags':
                $$_key = explode("|", $_val);
                break;
            case 'name':
            case 'separator':
                $$_key = (string)$_val;
                break;

            case 'checked':
            case 'selected':
                if (is_array($_val)) {
                    $smarty->trigger_error('html_radios: the "' . $_key . '" attribute cannot be an array', E_USER_WARNING);
                } else {
                    $selected = (string)$_val;
                }
                break;

            case 'labels':
            case 'label_ids':
                $$_key = (bool)$_val;
                break;

            case 'options':
                $$_key = (array)$_val;
                break;
            case 'values':
            case 'output':
                $$_key = array_values((array)$_val);
                break;

            case 'radios':
                $smarty->trigger_error('html_radios: the use of the "radios" attribute is deprecated, use "options" instead', E_USER_WARNING);
                $options = (array)$_val;
                break;

            case 'assign':
                break;

            default:
                if (!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_radios: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (!isset($options) && !isset($values))
        return ''; /* raise error here? */

    $_html_result = array();

    if (isset($options)) {
        foreach ($options as $_key=>$_val)
            $_html_result[] = smarty_function_html_radios_output_ex($name, $_key, $_val, $selected, $extra, $separator, $labels, $label_ids, $tags);
    } else {
        foreach ($values as $_i=>$_key) {
            $_val = isset($output[$_i]) ? $output[$_i] : '';
            $_html_result[] = smarty_function_html_radios_output_ex($name, $_key, $_val, $selected, $extra, $separator, $labels, $label_ids, $tags);
        }

    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $_html_result);
    } else {
        return implode("\n",$_html_result);
    }

    return '';
}

/**
 * @param string $name
 * @param null|string $selected
 * @param string $extra
 * @param string $separator
 * @param boolean $labels
 * @param boolean $label_ids
 */
function smarty_function_html_radios_output_ex($name, $value, $output, $selected, $extra, $separator, $labels, $label_ids, $tags)
{
    $_output = '';

    $_output .= '<input type="radio" name="'
        . smarty_function_escape_special_chars($name) . '" value="'
        . smarty_function_escape_special_chars($value) . '"';

    if ($labels && $label_ids) {
        $_id = smarty_function_escape_special_chars(preg_replace('![^\w\-\.]!', '_', $name . '_' . $value));
        $_output .= ' id="' . $_id . '"';
    }
    if ((string)$value == $selected) {
        $_output .= ' checked="checked"';
    }

    $_output .= $extra . ' />';

    $_output .= $tags[0];

    if ($labels) {
        if ($label_ids) {
        $_id = smarty_function_escape_special_chars(preg_replace('![^\w\-\.]!', '_', $name . '_' . $value));
            $_output .= '<label for="' . $_id . '">';
        } else {
            $_output .= '<label>';
        }
    }

    // 値を挿入
    $_output .= $output;

    $_output .= $tags[1];

    if ($labels) $_output .= '</label>';
    $_output .=  $separator;

    return $_output;
}
