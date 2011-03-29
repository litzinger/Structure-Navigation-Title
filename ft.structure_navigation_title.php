<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (! defined('SNT_VERSION'))
{
    // get the version from config.php
    require PATH_THIRD.'structure_navigation_title/config.php';
    define('SNT_VERSION', $config['version']);
    define('SNT_NAME', $config['name']);
    define('SNT_DESCRIPTION', $config['description']);
    define('SNT_DOCS', $config['docs_url']);
}

/**
 * ExpressionEngine Structure Navigation Title Fieldtype Class
 *
 * @package     ExpressionEngine
 * @subpackage  Fieldtypes
 * @category    Structure Navigation Title
 * @author      Brian Litzinger
 * @copyright   Copyright (c) 2011 Brian Litzinger
 * @link        http://boldminded.com
 */
 
class structure_navigation_title_ft extends EE_Fieldtype {
    
    var $info = array(
        'name'      => SNT_NAME,
        'version'   => SNT_VERSION
    );
    var $has_array_data = FALSE;
    var $settings = array();
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct()
    {
        parent::EE_Fieldtype();
    }
    
    function validate($data)
    {
        return TRUE;
    }
    
    function display_field($data)
    {
        return '<input type="text" name="'. $this->field_name .'" value="'. $data .'" />';
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die;
    }
}