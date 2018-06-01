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
 * @package   block_customslider
 * @copyright  2018 David Herney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Specialised restore task for the customslider block
 * (requires encode_content_links in some configdata attrs)
 *
 * TODO: Finish phpdocs
 */
class restore_customslider_block_task extends restore_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
    }

    public function get_fileareas() {
        return array('slides');
    }

    public function get_configdata_encoded_attributes() {
        return array();
    }

    static public function define_decode_contents() {

        $contents = array();

        $contents[] = new restore_customslider_block_decode_content('block_instances', 'configdata', 'block_instance');

        return $contents;
    }

    static public function define_decode_rules() {
        return array();
    }
}

/**
 * Specialised restore_decode_content provider that unserializes the configdata
 * field, to serve the configdata->text content to the restore_decode_processor
 * packaging it back to its serialized form after process
 */
class restore_customslider_block_decode_content extends restore_decode_content {

    protected $configdata; // Temp storage for unserialized configdata

    protected function get_iterator() {
        global $DB;

        // Build the SQL dynamically here
        $fieldslist = 't.' . implode(', t.', $this->fields);
        $sql = "SELECT t.id, $fieldslist
                  FROM {" . $this->tablename . "} t
                  JOIN {backup_ids_temp} b ON b.newitemid = t.id
                 WHERE b.backupid = ?
                   AND b.itemname = ?
                   AND t.blockname = 'customslider'";
        $params = array($this->restoreid, $this->mapping);
        return ($DB->get_recordset_sql($sql, $params));
    }

    /*public function process($processor) {
        if (!$processor instanceof restore_decode_processor) { // No correct processor, throw exception
            throw new restore_decode_content_exception('incorrect_restore_decode_processor', get_class($processor));
        }
        if (!$this->restoreid) { // Check restoreid is set
            throw new restore_decode_rule_exception('decode_content_restoreid_not_set');
        }
        // Get the iterator of contents
        $it = $this->get_iterator();
        foreach ($it as $itrow) {               // Iterate over rows
            $itrowarr   = (array)$itrow;        // Array-ize for clean access
            $rowchanged = false;                // To track changes in the row
            foreach ($this->fields as $field) { // Iterate for each field
                $this->preprocess_field($itrowarr[$field]);     // Apply potential pre-transformations
                $after = $this->postprocess_field(null); // Apply potential post-transformations
                $rowchanged = $itrowarr[$field] != $after ? true : $rowchanged;
                $itrowarr[$field] = $after;
            }

            if ($rowchanged) { // Change detected, perform update in the row.
                $this->update_iterator_row($itrowarr);
            }
        }
        $it->close(); // Always close the iterator at the end.
    }

    protected function preprocess_field($field) {
        $this->configdata = unserialize(base64_decode($field));

        if (!empty($this->configdata) && is_object($this->configdata)) {
            $this->configdata->slidesnumber = is_numeric($this->configdata->slidesnumber) ? (int)$this->configdata->slidesnumber : 0;
        } else {
            $this->configdata = new stdClass();
            $this->configdata->slidesnumber = 0;
        }

        // Really, used only to mark the field as modified.
        return $field;
    }

    protected function postprocess_field($content) {

        $newconfigdata = new stdClass();
        $newconfigdata->slidesnumber = $this->configdata->slidesnumber;
        $newconfigdata->title = isset($this->configdata->title) ? $this->configdata->title : '';

        for($i = 1; $i <= $this->configdata->slidesnumber; $i++) {
            $sliderimage = 'file_slide' . $i;
            $sliderurl = 'url_slide' . $i;
            $slidercaption = 'title_slide' . $i;

            $newconfigdata->$sliderimage = $i;
            $newconfigdata->$sliderurl = isset($this->configdata->$sliderurl) ? $this->configdata->$sliderurl : '';
            $newconfigdata->$slidercaption = isset($this->configdata->$slidercaption) ? $this->configdata->$slidercaption : '';
        }

        $this->configdata = $newconfigdata;

        return base64_encode(serialize($this->configdata));
    }*/
}
