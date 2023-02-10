<?php

/**
 * This file is part of the UserImport package.
 *
 * @copyright bitego (Martin Gartner)
 * @license GNU General Public License v2.0 (and later)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitego\UserImport\Processors\Usergroups;

use MODX\Revolution\modX;
use MODX\Revolution\modUserGroup;
use MODX\Revolution\Processors\Processor;

/**
 * Get MODX user groups in tree node format
 * (This processor is called once for each MODX user group node!)
 *
 * @param string $id The parent ID
 *
 * @package userimport
 * @subpackage processors
 */

class GetNodes extends Processor
{
    /** @var string $id */
    public $id;

    /** @var modUserGroup $userGroup */
    public $userGroup;

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('usergroup_view');
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['user'];
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function initialize()
    {
        $this->setDefaultProperties([
            'id' => 0,
            'sort' => 'name',
            'dir' => 'ASC',
            'showAnonymous' => false,
        ]);
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {
        $this->id = $this->parseId($this->getProperty('id'));
        $this->getUserGroup();

        $groups = $this->getGroups();

        $list = [];
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
    protected function parseId($id)
    {
        return str_replace('n_ug_', '', $id);
    }

    /**
     * Get the selected user group
     *
     * @return modUserGroup|null
     */
    public function getUserGroup()
    {
        if (!empty($this->id)) {
            $this->userGroup = $this->modx->getObject(modUserGroup::class, $this->id);
        }
        return $this->userGroup;
    }

    /**
     * Get the User Groups within the filter
     *
     * @return array
     */
    public function getGroups()
    {
        $data = [];
        $c = $this->modx->newQuery(modUserGroup::class);
        $c->where([
            'parent' => $this->id,
        ]);
        $data['total'] = $this->modx->getCount(modUserGroup::class, $c);
        $c->sortby($this->getProperty('sort'), $this->getProperty('dir'));
        $data['results'] = $this->modx->getCollection(modUserGroup::class, $c);
        return $data;
    }

    /**
     * Add the Anonymous group to the list
     *
     * @param array $list
     * @return array
     */
    public function addAnonymous(array $list)
    {
        if ($this->getProperty('showAnonymous') && empty($this->id)) {
            $cls = 'pupdate';
            $list[] = [
                'text' => '(' . $this->modx->lexicon('anonymous') . ')',
                'id' => 'n_ug_0',
                'leaf' => true,
                'type' => 'usergroup',
                'cls' => $cls,
                'checked' => false,
                'iconCls' => 'icon-group',
            ];
        }
        return $list;
    }

    /**
     * Prepare a User Group for listing
     *
     * @param modUserGroup $group
     * @return array
     */
    public function prepareGroup(modUserGroup $group)
    {
        $cls = 'padduser pcreate pupdate';
        if ($group->get('id') != 1) {
            $cls .= ' premove';
        }
        return [
            'text' => $group->get('name') . ' (' . $group->get('id') . ')',
            'id' => 'n_ug_' . $group->get('id'),
            'leaf' => false,
            'type' => 'usergroup',
            'qtip' => $group->get('description'),
            'cls' => $cls,
            'checked' => false,
            'iconCls' => 'icon-group',
        ];
    }
}
