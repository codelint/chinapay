<?php

/**
 * helper function:
 * @date 14-3-29
 * @time 上午1:28
 * @author Ray.Zhang <gzhang@codelint.com>
 **/

if (!function_exists('array_add'))
{
    /**
     * Add an element to an array if it doesn't exist.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_add($array, $key, $value)
    {
        if (!isset($array[$key])) $array[$key] = $value;

        return $array;
    }
}

if (!function_exists('array_build'))
{
    /**
     * Build a new array using a callback.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    function array_build($array, Closure $callback)
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }
}

if (!function_exists('array_divide'))
{
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array $array
     * @return array
     */
    function array_divide($array)
    {
        return array(array_keys($array), array_values($array));
    }
}

if (!function_exists('array_dot'))
{
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array $array
     * @param  string $prepend
     * @return array
     **/
    function array_dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            } else
            {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('array_except'))
{
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array)$keys));
    }
}

if (!function_exists('array_fetch'))
{
    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    function array_fetch($array, $key)
    {
        foreach (explode('.', $key) as $segment)
        {
            $results = array();

            foreach ($array as $value)
            {
                $value = (array)$value;

                $results[] = $value[$segment];
            }

            $array = array_values($results);
        }

        return array_values($results);
    }
}

if (!function_exists('array_first'))
{
    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }

        return value($default);
    }
}

if (!function_exists('array_last'))
{
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array $array
     * @param  Closure $callback
     * @param  mixed $default
     * @return mixed
     */
    function array_last($array, $callback, $default = null)
    {
        return array_first(array_reverse($array), $callback, $default);
    }
}

if (!function_exists('array_flatten'))
{
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    function array_flatten($array, $key = '')
    {
        $return = array();

        array_walk_recursive($array, function ($v, $k, $ukey) use (&$return)
        {
            if (empty($ukey))
            {
                $return[] = $v;
            } else
            {
                if ($ukey == $k)
                {
                    $return[] = $v;
                }
            }
        }, $key);
        return $return;
    }
}

if (!function_exists('array_assoc'))
{
    function array_assoc($arr, $key)
    {
        $res = array();
        foreach ($arr as $k => $v)
        {
            $rkey = isset($v[$key]) ? $v[$key] : $k;
            $res[$rkey] = $v;
        }
        return $res;
    }
}

if (!function_exists('array_assoc_multi'))
{
    function array_assoc_multi($arr, $key)
    {
        $res = array();
        foreach ($arr as $k => $v)
        {
            $rkey = isset($v[$key]) ? $v[$key] : $k;
            if (!isset($res[$rkey]))
            {
                $res[$rkey] = array();
            }
            $res[$rkey][] = $v;
        }
        return $res;
    }
}

if (!function_exists('array_forget'))
{
    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @return void
     */
    function array_forget(&$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key]))
            {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }
}

if (!function_exists('array_get'))
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if (!is_array($array) || !array_key_exists($segment, $array))
            {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_only'))
{
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }
}

if (!function_exists('array_pluck'))
{
    /**
     * Pluck an array of values from an array.
     *
     * @param  array $array
     * @param  string $value
     * @param  string $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        $results = array();

        foreach ($array as $item)
        {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key))
            {
                $results[] = $itemValue;
            } else
            {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }
}

if (!function_exists('array_pull'))
{
    /**
     * Get a value from the array, and remove it.
     *
     * @param  array $array
     * @param  string $key
     * @return mixed
     */
    function array_pull(&$array, $key, $default = '')
    {
        $value = array_get($array, $key, $default);

        array_forget($array, $key);

        return $value;
    }
}

if (!function_exists('array_set'))
{
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key]))
            {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_where'))
{
    /**
     * Filter the array using the given Closure.
     *
     * @param  array $array
     * @param  \Closure $callback
     * @return array
     */
    function array_where($array, Closure $callback)
    {
        $filtered = array();

        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) $filtered[$key] = $value;
        }

        return $filtered;
    }
}

if (!function_exists('class_basename'))
{
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('data_get'))
{
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     * @throws InvalidArgumentException
     */
    function data_get($target, $key, $default = null)
    {
        if (is_array($target))
        {
            return array_get($target, $key, $default);
        } elseif (is_object($target))
        {
            return object_get($target, $key, $default);
        } else
        {
            throw new \InvalidArgumentException("Array or object must be passed to data_get.");
        }
    }
}

if (!function_exists('object_get'))
{
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object $object
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') return $object;

        foreach (explode('.', $key) as $segment)
        {
            if (!is_object($object) || !isset($object->{$segment}))
            {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('dd'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x)
        {
            var_dump($x);
        }, func_get_args());
        die;
    }
}

if (!function_exists('e'))
{
    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('head'))
{
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (!function_exists('last'))
{
    /**
     * Get the last element from an array.
     *
     * @param  array $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('starts_with'))
{
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }

        return false;
    }
}

if (!function_exists('ends_with'))
{
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    function ends_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle)
        {
            if ($needle == substr($haystack, -strlen($needle))) return true;
        }

        return false;
    }
}

if (!function_exists('camel_case'))
{
    /**
     * Convert a value to camel case.
     *
     * @param  string $value
     * @return string
     */
    function camel_case($value)
    {
        return lcfirst(studly_case($value));
    }
}

if (!function_exists('studly_case'))
{
    /**
     * Convert a value to studly caps case.
     *
     * @param  string $value
     * @return string
     */
    function studly_case($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }
}

if (!function_exists('snake_case'))
{
    /**
     * Convert a string to snake case.
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        $replace = '$1' . $delimiter . '$2';

        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }
}

if (!function_exists('str_contains'))
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }
        return false;
    }
}

if (!function_exists('str_is'))
{
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string $pattern
     * @param  string $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        if ($pattern == $value) return true;

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern) . '\z';

        return (bool)preg_match('#^' . $pattern . '#', $value);
    }
}

if (!function_exists('str_limit'))
{
    /**
     * Limit the number of characters in a string.
     *
     * @param  string $value
     * @param  int $limit
     * @param  string $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) return $value;

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }
}

if (!function_exists('str_random'))
{
    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int $length
     * @return string
     * @throws RuntimeException
     */
    function str_random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false)
            {
                throw new \RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}

if (!function_exists('str_replace_array'))
{
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value)
        {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }
        return $subject;
    }
}

if (!function_exists('value'))
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('with'))
{
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

if (!function_exists('accept_type'))
{
    /**
     * 返回请求类型: html|xml|json|js|css|...
     * @return bool|int|string
     */
    function accept_type()
    {
        $type = array(
            'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
            'html' => 'text/html,application/xhtml+xml,*/*',
            'xml' => 'application/xml,text/xml,application/x-xml',
            'js' => 'text/javascript,application/javascript,application/x-javascript',
            'css' => 'text/css',
            'rss' => 'application/rss+xml',
            'yaml' => 'application/x-yaml,text/yaml',
            'atom' => 'application/atom+xml',
            'pdf' => 'application/pdf',
            'text' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv'
        );

        foreach ($type as $key => $val)
        {
            $array = explode(',', $val);
            foreach ($array as $k => $v)
            {
                if (stristr($_SERVER['HTTP_ACCEPT'], $v))
                {
                    return $key;
                }
            }
        }
        return false;
    }
}

// --------------------------------------------------------------------------------- //

if (!function_exists('url64encode'))
{
    /**
     * URL 64 encode
     */
    function url64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }
}

if (!function_exists('url64decode'))
{
    /**
     * URL 64 decode
     */
    function url64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4)
        {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}

if (!function_exists('array_key_map'))
{
    /**
     * Change key according by the $mapper
     *
     * @param  array $array
     * @param  array $keymap
     * @return array
     */
    function array_key_map($array, $keymap)
    {
        $keys = array_keys($keymap);
        $arr = array_only($array, $keys);
        $array = array_except($array, $keys);
        foreach ($arr as $k => $v)
        {
            $array[$keymap[$k]] = $v;
        }
        return $array;
    }
}

/**
 * Convert xml object to array
 */
if (!function_exists('xmlToArray'))
{
    function xmlToArray($xmlObj)
    {
        $output = array();
        foreach ((array)$xmlObj as $index => $node)
        {
            $output[$index] = (is_object($node)) ? xmlToArray($node) : $node;
        }
        return $output;
    }

}

if (!function_exists('array_sort_by'))
{
    function array_sort_by($arr, $field, $desc = true)
    {
        return array_sort($arr, function ($item) use ($field, $desc)
        {
            return $item[$field] * ($desc ? 1 : -1);
        });
    }
}

/**
 * @desc  数组排序
 * @param 排序的参照列的键
 * @param 需要排序的数组
 * @return array
 **/
if (!function_exists('array_sort_by_column'))
{
    function array_sort_by_column($key, $arr, $sort = 'desc')
    {

        $keys = array();

        foreach ($arr as $a)
        {
            $keys[] = $a[$key];
        }
        $sort = $sort == 'asc' ? SORT_ASC : SORT_DESC;

        array_multisort($keys, $sort, $arr);

        return $arr;

    }
}

if (!function_exists('array_column'))
{
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    function array_column($array, $key = '')
    {
        $return = array();

        array_walk_recursive($array, function ($v, $k, $ukey) use (&$return)
        {
            if (empty($ukey))
            {
                $return[] = $v;
            }
            else
            {
                if ($ukey == $k)
                {
                    $return[] = $v;
                }
            }
        }, $key);
        return $return;
    }
}

if (!function_exists('m_encrypt'))
{
    function m_encrypt($str, $key = 'lumbini')
    {
        $crypt = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $str, MCRYPT_MODE_CBC, md5(md5($key))));
        $crypt = base64_encode($crypt);
        $crypt = str_replace(array('+', '/', '='), array('-', '_', ''), $crypt);
        return trim($crypt);
    }
}

if (!function_exists('m_decrypt'))
{
    function m_decrypt($str, $key = 'lumbini')
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $str);
        $mod4 = strlen($data) % 4;
        if ($mod4)
        {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($data), MCRYPT_MODE_CBC, md5(md5($key))));
    }
}

if(!function_exists('http_post_data'))
{
    function http_post_data($url, $data_string) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }
}

if (!function_exists('array_weight'))
{
    function array_weight($array, $field)
    {
        $weight = 999999999;
        $w = array_get($array, $field, $weight);
        $weight = min($w, $weight);
        foreach ($array as $v)
        {
            if (is_array($v))
            {
                $w = array_weight($v, $field);
                $weight = min($w, $weight);
            }
        }
        return $weight;
    }
}



