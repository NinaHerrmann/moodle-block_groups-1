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
 * The class contains a test script for the moodle block groups
 *
 * @package block_groups
 * @copyright 2016 N Herrmann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class blocks_groups_testcase extends advanced_testcase {

    protected function set_up() {
        // Recommended in Moodle docs to always include CFG.
        global $CFG;
        $this->resetAfterTest(true);
    }
    /**
     * Function to test the locallib functions.
     * @package block_groups
     */
    public function test_locallib() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/blocks/groups/locallib.php');
        $generator = $this->getDataGenerator()->get_plugin_generator('block_groups');
        $data = $generator->test_create_preparation();
        $this->test_deleting();
        // Test the function that changes the database.
        block_groups_db_transaction_change_visibility($data['group1']->id, $data['course2']->id);
        block_groups_db_transaction_change_visibility($data['group2']->id, $data['course2']->id);
        block_groups_db_transaction_change_visibility($data['group2']->id, $data['course2']->id);
        $functionresultshow = $DB->get_records('block_groups_hide', array('id' => $data['group1']->id));
        $functionresulthide = $DB->get_records('block_groups_hide', array('id' => $data['group2']->id));
        $booleanvisible = empty($functionresultshow);
        $booleandeleted = empty($functionresulthide);

        $this->assertEquals(false, $booleanvisible);
        $this->assertEquals(true, $booleandeleted);

        // Test the function that counts the grouping members.
        $functioncount = count_grouping_members ($data['grouping1']->id);
        $functioncount2 = count_grouping_members($data['grouping2']->id);
        $functioncount3 = count_grouping_members($data['grouping3']->id);

        $this->assertEquals(2, $functioncount);
        // Members are not counted multiple.
        $this->assertEquals(3, $functioncount2);
        // Test empty grouping.
        $this->assertEquals(0, $functioncount3);
    }
    /**
     * Methodes recommended by moodle to assure database and dataroot is reset.
     * @package block_groups
     */
    public function test_deleting() {
        global $DB;
        $this->resetAfterTest(true);
        $DB->delete_records('user');
        $DB->delete_records('block_groups_hide');
        $this->assertEmpty($DB->get_records('user'));
        $this->assertEmpty($DB->get_records('block_groups_hide'));
    }
    /**
     * Methodes recommended by moodle to assure database is reset.
     * @package block_groups
     */
    public function test_user_table_was_reset() {
        global $DB;
        $this->assertEquals(2, $DB->count_records('user', array()));
        $this->assertEquals(0, $DB->count_records('block_groups_hide', array()));
    }
}