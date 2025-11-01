<?php
namespace mod_chimpaigo\output;

defined('MOODLE_INTERNAL') || die();

class view_page implements \renderable, \templatable {
    public function export_for_template(\renderer_base $output) {
        return [];
    }
}
