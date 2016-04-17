<?php

class URL
{

    public static function get_language_url($language, $url_id, $arg1=null)
    {
        $url = new URL($url_id);
        $url->set_language($language);

        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        foreach($args as $arg)
        {
            if(is_array($arg) && ArrayHelper::is_numeric($arg))
            {
                $url->add_arg($arg);
            }
            else if(is_array($arg))
            {
                $url->update_params($arg);
            }
            else
            {
                $url->add_arg($arg);
            }
        }

        return $url->to_string();
    }

    public static function get_escaped_language_url($language, $url_id, $arg1=null)
    {
        $args = func_get_args();
        $url = call_user_func_array(array(self, 'get_language_url'), $args);
        return HTMLHelper::escape($url);
    }

    public static function get_url($url_id, $arg1=null)
    {
        $args = func_get_args();
        array_unshift($args, LanguageHelper::get_current_language());
        $url = call_user_func_array(array(self, 'get_language_url'), $args);
        return $url;
    }

    public static function get_escaped_url($url_id, $arg1=null)
    {
        $args = func_get_args();
        $url = call_user_func_array(array(self, 'get_url'), $args);
        return HTMLHelper::escape($url);
    }

    public static function get_actual_url($language, $arg1=null)
    {
        $args = func_get_args();
        array_shift($args);
        array_unshift($args, null);
        array_unshift($args, $language);
        $url = call_user_func_array(array(self, 'get_language_url'), $args);
        return $url;
    }

    public static function get_escaped_actual_url($language, $arg1=null)
    {
        $args = func_get_args();
        $url = call_user_func_array(array(self, 'get_actual_url'), $args);
        return HTMLHelper::escape($url);
    }

    /*------------------------------------------------------------------------------------------*/

    protected $_id;
    protected $_use_language = true;
    protected $_language = null;
    protected $_params = array();
    protected $_args = array();

    public function __construct($id)
    {
        $this->_id = $id;

        if(is_null($this->_id))
        {
            $this->_id = ZPHP::get_actual_uri();
        }
    }

    public function __set($name, $value)
    {
        return $this->set_param($name, $value);
    }

    public function __get($name)
    {
        return $this->get_param($name);
    }

    public function __toString()
    {
        return $this->to_string();
    }

    public function set_use_language($use)
    {
        $this->_use_language = $use;
        return $this;
    }

    public function get_use_language()
    {
        return $this->_use_language;
    }

    public function set_language($language)
    {
        $this->_language = $language;
        return $this;
    }

    public function get_language()
    {
        return $this->_language;
    }

    public function update_params(array $params = array())
    {
        $this->_params = array_merge($this->_params, $params);
        return $this;
    }

    public function set_param($name, $value=null)
    {
        if(is_null($value))
        {
            unset($this->_params[$name]);
        }
        else
        {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    public function get_param($name)
    {
        return $this->_params[$name];
    }

    public function clear_param($name)
    {
        $args = func_get_args();
        foreach($args as $arg)
        {
            $this->set_param($arg, null);
        }
        return $this;
    }

    public function clear_params()
    {
        $this->_params = array();
        return $this;
    }

    public function add_arg($arg)
    {
        $args = func_get_args();
        foreach($args as $arg)
        {
            if(is_array($arg))
            {
                foreach($arg as $a)
                {
                    $this->add_arg($a);
                }

            }
            else
            {
                $this->_args[] = $arg;
            }
        }
        return $this;
    }

    public function get_arg($index)
    {
        return $this->_args[$index];
    }

    public function clear_args()
    {
        $this->_args = array();
        return $this;
    }

    public function to_string()
    {
        $url = URLPattern::reverse($this->_id, $this->_args);

        if(is_null($url))
        {
            $url = $this->_id;
        }

        if($this->_use_language && LanguageHelper::is_enabled())
        {
            $url = LanguageHelper::get_url($url, $this->_language);
        }

        if(!empty($this->_params))
        {
            $params_str = http_build_query($this->_params);

            if(strpos($url, '?') !== false)
            {
                $url.= '&'.$params_str;
            }
            else
            {
                $url.= '?'.$params_str;
            }
        }

        return $url;
    }

}