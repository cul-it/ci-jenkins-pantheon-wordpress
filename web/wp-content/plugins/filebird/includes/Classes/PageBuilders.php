<?php
namespace FileBird\Classes;
use FileBird\Controller\Folder;

defined('ABSPATH') || exit;

class PageBuilders {

    protected static $instance = null;
    protected $folderController;

    public static function getInstance() {
        if (null == self::$instance) {
          self::$instance = new self;
        }
        
        return self::$instance;
    }

    public function __construct() {
        $this->folderController = Folder::getInstance();
        add_action('init', array($this, 'prepareRegister'));
    }

    public function prepareRegister(){
        // Compatible for Elementor
        if (defined('ELEMENTOR_VERSION')) {
            $this->registerForElementor();
        }
        // Compatible for WPBakery - Work normally

        // Compatible for Beaver Builder
        if (class_exists('FLBuilderLoader')) {
            $this->registerForBeaver();
        }

        // Brizy Builder
        if (class_exists('Brizy_Editor')) {
            $this->registerForBrizy();
        }
    }

    public function enqueueScripts(){
        $this->folderController->enqueueAdminScripts('pagebuilders');
    }

    public function registerForElementor(){
        add_action('elementor/editor/before_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function registerForBeaver(){
        add_action('fl_before_sortable_enqueue', array($this, 'enqueueScripts'));
    }

    public function registerForBrizy(){
        add_action('brizy_editor_enqueue_scripts', array($this, 'enqueueScripts'));
    }
}
