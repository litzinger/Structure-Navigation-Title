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
 
class Structure_navigation_title_ext {

    var $settings       = array();
    var $name           = SNT_NAME;
    var $version        = SNT_VERSION;
    var $description    = SNT_DESCRIPTION;
    var $docs_url       = SNT_DOCS;
    var $settings_exist = 'n';
    var $cache;
    
    /**
     * Constructor
     */
    function __construct($settings = '') 
    {
        $this->EE =& get_instance();
        
        // Create cache
        if (! isset($this->EE->session->cache['structure_navigation_title']))
        {
            $this->EE->session->cache['structure_navigation_title'] = array();
        }
        $this->cache =& $this->EE->session->cache['structure_navigation_title'];
    }
    
    /**
     * Install the extension
     */
    function activate_extension()
    {
        // Delete old hooks
        $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '". __CLASS__ ."'");
        
        // Add new hooks
        $ext_template = array(
            'class'    => __CLASS__,
            'settings' => '',
            'priority' => 5,
            'version'  => $this->version,
            'enabled'  => 'y'
        );
        
        $extensions = array(
            array('hook'=>'sessions_end', 'method'=>'sessions_end')
        );
        
        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }   
    }

    /**
     * @param string $current currently installed version
     */
    function update_extension($current = '') 
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }
    }

    /**
     * Uninstalls extension
     */
    function disable_extension() 
    {
        $this->EE->db->delete('extensions', array('class' => __CLASS__)); 
    }
    
    /**
     * Create our structure_page_titles global var
     */
    function sessions_end($session)
    {
        // Only do this if there is page data, and we're not within the control panel
        if(REQ == 'PAGE')
        {
            $fields = $this->_get_fields();
            
            $data = array();
            
            foreach($fields as $field)
            {
                $data[] = $field['channel_name'] .':'. $field['field_name'];
            }
            
            $this->EE->config->_global_vars['structure_navigation_title'] = implode('|', $data);
        }
        
        return $session;
    }
    
    private function _get_fields()
    {
        if(!isset($this->cache['channels']))
        {
            $channels = $this->EE->db->select('c.channel_name, f.*')
                                     ->from('channels AS c, channel_fields AS f, field_groups AS g, structure_channels AS sc')
                                     ->where('f.group_id = g.group_id')
                                     ->where('c.field_group = g.group_id')
                                     ->where('sc.type', 'page')
                                     ->where('f.field_type', 'structure_navigation_title')
                                     ->get()
                                     ->result_array();
                                     
            $this->cache['channels'] = array();
            
            foreach($channels as $channel)
            {
                $this->cache['channels'][] = $channel;
            }
        }
        
        return $this->cache['channels'];
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die;
    }
}