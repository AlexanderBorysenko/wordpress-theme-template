<?php
namespace DevTools\Tools;

abstract class Tool
{
    protected static $instance;

    public function getToolName()
    {
        return isset($this->toolName) ? $this->toolName : null;
    }
    public function getAjaxAction()
    {
        return isset($this->ajaxAction) ? $this->ajaxAction : null;
    }

    public function initWpInterfaces()
    {
        $this->addAdminMenu();
        $this->addAjaxAction();
    }

    public function addAdminMenu()
    {
        add_action('admin_menu', function () {
            $toolName = $this->getToolName();
            if (!$toolName)
                return;
            add_submenu_page(
                \DevTools\DevTools::ADMIN_MENU_SLUG,
                $toolName,
                $toolName,
                'manage_options',
                strtolower(str_replace(' ', '-', $toolName)),
                array($this, 'view')
            );
        });
    }
    public function addAjaxAction()
    {
        $ajaxAction = $this->getAjaxAction();
        if (!$ajaxAction)
            return;
        add_action('wp_ajax_' . $ajaxAction, array($this, 'ajax'));
        add_action('wp_ajax_nopriv_' . $ajaxAction, array($this, 'ajax'));
    }

    public function view()
    {
    }

    public function ajax()
    {
    }

}
