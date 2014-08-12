<?php
/**
 * UserImport
 *
 * Copyright 2014 by bitego <office@bitego.com>
 *
 * UserImport is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * UserImport is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this software; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * Get MODX user groups in tree node format
 * (This processor is called once for each MODX user group node!)
 *
 * @param string $id The parent ID
 *
 * @package userimport
 * @subpackage processors
 */

class UserGroupsGetNodesProcessor extends modProcessor {
    /** @var string $id */
    public $id;
    
    /** @var modUserGroup $userGroup */
    public $userGroup;
    
    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function checkPermissions() {
        return $this->modx->hasPermission('usergroup_view');
    }
    
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getLanguageTopics() {
        return array('user');
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function initialize() {
        $this->setDefaultProperties(array(
            'id' => 0,
            'sort' => 'name',
            'dir' => 'ASC',
            'showAnonymous' => false,
        ));
        return true;
    }

    /**
     * {@inheritDoc}
     * 
     * @return mixed
     */
    public function process() {
        $this->id = $this->parseId($this->getProperty('id'));
        $this->getUserGroup();

        $groups = $this->getGroups();

        $list = array();
        $list = $this->addAnonymous($list);
        
        /** @var modUserGroup $group */
        foreach ($groups['results'] as $group) {
            $groupArray = $this->prepareGroup($group);
            if (!empty($groupArray)) {
                $list[] = $groupArray;
            }
        }

        return $this->toJSON($list);
    }

    /**
     * Parse the ID to get the parent group
     *
     * @param string $id
     * @return mixed
     */
    protected function parseId($id) {
        return str_replace('n_ug_','',$id);
    }

    /**
     * Get the selected user group
     *
     * @return modUserGroup|null
     */
    public function getUserGroup() {
        if (!empty($this->id)) {
            $this->userGroup = $this->modx->getObject('modUserGroup', $this->id);
        }
        return $this->userGroup;
    }

    /**
     * Get the User Groups within the filter
     *
     * @return array
     */
    public function getGroups() {
        $data = array();
        $c = $this->modx->newQuery('modUserGroup');
        $c->where(array(
            'parent' => $this->id,
        ));
        $data['total'] = $this->modx->getCount('modUserGroup',$c);
        $c->sortby($this->getProperty('sort'),$this->getProperty('dir'));
        $data['results'] = $this->modx->getCollection('modUserGroup',$c);
        return $data;
    }

    /**
     * Add the Anonymous group to the list
     * 
     * @param array $list
     * @return array
     */
    public function addAnonymous(array $list) {
        if ($this->getProperty('showAnonymous') && empty($this->id)) {
            $cls = 'pupdate';
            $list[] = array(
                'text' => '('.$this->modx->lexicon('anonymous').')',
                'id' => 'n_ug_0',
                'leaf' => true,
                'type' => 'usergroup',
                'cls' => $cls,
                'checked' => false,
                'iconCls' => 'icon-group',
            );
        }
        return $list;
    }

    /**
     * Prepare a User Group for listing
     * 
     * @param modUserGroup $group
     * @return array
     */
    public function prepareGroup(modUserGroup $group) {
        $cls = 'padduser pcreate pupdate';
        if ($group->get('id') != 1) {
            $cls .= ' premove';
        }
        return array(
            'text' => $group->get('name').' ('.$group->get('id').')',
            'id' => 'n_ug_'.$group->get('id'),
            'leaf' => false,
            'type' => 'usergroup',
            'qtip' => $group->get('description'),
            'cls' => $cls,
            'checked' => false,
            'iconCls' => 'icon-group',
        );
    }
}
return 'UserGroupsGetNodesProcessor';
