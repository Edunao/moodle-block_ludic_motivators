<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Extra installation code
 * @package    block_ludic_motivators
 * @copyright  2018 Edunao SAS (contact@edunao.com)
 * @author     Sadge <daniel@edunao.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
s */


function xmldb_block_ludic_motivators_install() {
    global $DB;

    // make sure the user info category exists
    $userInfoCategoryName = 'block_ludic_motivators';
    if ( ! $userInfoCategoryId = $DB->get_field( 'user_info_category', 'id', array('name' => $userInfoCategoryName ) ) ) {
        $userInfoCategoryId = $DB->insert_record( 'user_info_category', array(
            'name'      => $userInfoCategoryName,
            'sortorder' => 1
        ));
    }

    // make sure the user info field exists
    $userInfoFieldName = 'motivator';
    if ( ! $userInfoFieldId = $DB->get_field( 'user_info_field', 'id', array('shortname' => $userInfoFieldName ) ) ) {
        $userInfoFieldId = $DB->insert_record( 'user_info_field', array(
            'shortname'     => $userInfoFieldName,
            'name'          => 'Internal data belonging to block_ludi_motivators',
            'categoryid'    => $userInfoCategoryId,
            'datatype'      => 'text',
            'signup'        => 0,
            'locked'        => 1,
            'visible'       => 0,
            'required'      => 0,
            'sortorder'     => 1
        ));
    }
}
