<?php
/**
 * @version     $Id: cincopa.php $
 * @copyright   Copyright (C) 2010 Oren Shmulevich. All rights reserved.
 * @license     GNU/GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class com_CincopaInstallerScript
{
    function __construct()
    {
    }

    function install($parent)
    {
    }

    function uninstall($parent)
    {
        $installer = $parent->getParent();
        isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();

        $this->app = JFactory::getApplication();
        $this->manifest = $this->parentInstaller->getManifest();

        $this->path = $this->parentInstaller->getPath('extension_administrator');
        $this->name = substr(basename($this->path), 4);

        $this->parseDependency($this->parentInstaller);

        foreach ($this->dependencies AS $extension) {
            $this->removeExtension($extension);
        }
    }

    function update($parent)
    {
    }

    function preflight($type, $parent)
    {
        $installer = $parent->getParent();
        $this->parentInstaller = $parent->getParent();

        $this->app = JFactory::getApplication();
        $this->manifest = $this->parentInstaller->getManifest();

        $this->parseDependency($this->parentInstaller);
    }

    function postflight($type, $parent)
    {
        foreach ($this->dependencies AS $extension) {
            if (isset($extension->source)) {
                $this->installExtension($extension);
            }
        }
    }

    public function installExtension($extension)
    {
        $installer = new JInstaller;
        if (!$installer->install($extension->source)) {
            $this->app->enqueueMessage(sprintf('Error installing "%s" %s', $extension->name, $extension->type), 'error');
        } else {
            $this->app->enqueueMessage(sprintf('Install "%s" %s was successfull', $extension->name, $extension->type));
            $this->updateExtension($extension);
        }
    }

    protected function removeExtension($extension)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $q->select('extension_id, manifest_cache, custom_data, params');
        $q->from('#__extensions');
        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);

        if ($status = $db->loadObject()) {
            $id = $status->extension_id;

            $this->disableExtension($extension);
            $this->unlockExtension($extension);

            $installer = new JInstaller;

            if ($installer->uninstall($extension->type, $id)) {
                $this->app->enqueueMessage(sprintf('"%s" %s has been uninstalled', $extension->name, $extension->type));
            } else {
                $this->app->enqueueMessage(sprintf('Cannot uninstall "%s" %s', $extension->name, $extension->type));
            }
        }
    }

    protected function parseDependency($installer)
    {
        if (!isset($this->dependencies) OR !is_array($this->dependencies)) {
            $this->dependencies = array();

            if (isset($this->manifest->subinstall) AND $this->manifest->subinstall instanceOf SimpleXMLElement) {
                foreach ($this->manifest->subinstall->children() AS $node) {
                    if (strcasecmp($node->getName(), 'extension') != 0) {
                        continue;
                    }

                    $extension = (array)$node;
                    $extension = (object)$extension['@attributes'];

                    $extension->title = trim((string)$node != '' ? (string)$node : ($node['title'] ? (string)$node['title'] : (string)$node['name']));

                    if (!isset($extension->name) OR !isset($extension->type) OR !in_array($extension->type, array('template', 'plugin', 'module', 'component')) OR ($extension->type == 'plugin' AND !isset($extension->folder))) {
                        continue;
                    }

                    if (isset($extension->dir) AND is_dir($source = $installer->getPath('source') . '/' . $extension->dir)) {
                        $extension->source = $source;
                    }

                    $this->dependencies[] = $extension;
                }
            }
        }

        return $this;
    }

    protected function disableExtension($extension)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $q->update('#__extensions');
        $q->set('enabled = 0');
        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);
        $db->execute();

        return $this;
    }

    protected function unlockExtension($extension)
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $q->update('#__extensions');
        $q->set('protected = 0');
        $q->where("element = '{$extension->name}'");
        $q->where("type = '{$extension->type}'");
        $extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

        $db->setQuery($q);
        $db->execute();

        return $this;
    }

    protected function updateExtension($extension)
    {
        $table = JTable::getInstance('Extension');

        $condition = array(
            'type' => $extension->type,
            'element' => $extension->name
        );

        if ($extension->type == 'plugin') {
            $condition['folder'] = $extension->folder;
        }

        $table->load($condition);

        $table->enabled = (isset($extension->publish)    AND (int)$extension->publish > 0) ? 1 : 0;
        $table->protected = (isset($extension->lock)        AND (int)$extension->lock > 0) ? 1 : 0;
        $table->client_id = (isset($extension->client)    AND $extension->client == 'site') ? 0 : 1;

        $table->store();

        if ($extension->type == 'module') {
            $module = JTable::getInstance('module');

            $module->load(array('module' => $extension->name));

            $module->title = $extension->title;
            $module->ordering = isset($extension->ordering) ? $extension->ordering : 0;
            $module->published = (isset($extension->publish) AND (int)$extension->publish > 0) ? 1 : 0;

            if ($hasPosition = (isset($extension->position) AND (string)$extension->position != '')) {
                $module->position = (string)$extension->position;
            }

            $module->store();

            if ($hasPosition AND (int)$module->id > 0) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true);

                try {
                    $q->delete('#__modules_menu');
                    $q->where("moduleid = {$module->id}");

                    $db->setQuery($q);
                    $db->execute();

                    $q->insert('#__modules_menu');
                    $q->columns('moduleid, menuid');
                    $q->values("{$module->id}, 0");

                    $db->setQuery($q);
                    $db->execute();
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }

        return $this;
    }
}