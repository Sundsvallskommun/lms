<?php

use block_rss_client\output\block;

require_once($CFG->dirroot . '/course/renderer.php');

//require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/enrol/renderer.php');



class theme_courseextension_core_course_renderer extends core_course_renderer
{

    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '')
    {
        global $PAGE;

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix ' . $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $coursecontext = context_course::instance($course->id);

        $students = get_role_users(5, $coursecontext);

        $enrolments = $this->enrol_get_instances_courseextension($course->id, true);

        //var_dump($enrolments);

        $enrolmentMethods = $enrolments;

        $rawStartDate = $course->startdate;

        $parsedStartDate = userdate($rawStartDate);

        $rawEndDate = $course->enddate;

        $parsedEndDate = userdate($rawEndDate);

        $configStartDate = get_config('theme_courseextension', 'showstartdate');
        $configEndDate = get_config('theme_courseextension', 'showenddate');
        $configEnrolment = get_config('theme_courseextension', 'showenrolments');

        $startOfCourse = '<b>' . get_string('startdate') . ' </b>' . $parsedStartDate;
        $endOfCourse = '<b>' . get_string('enddate') . ' </b>' . $parsedEndDate;

        $content .= html_writer::start_tag('div', array('class' => 'info'));
        $content .= $this->course_name($chelper, $course);
        $content .= $this->course_enrolment_icons($course);
        $content .= html_writer::end_tag('div');


        $content .= html_writer::start_tag('div', array('class' => 'content'));

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div');

        if ($configStartDate != null || $configEndDate != null) {
            $content .= \html_writer::start_tag('div', ['class' => 'd-flex']);
            $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
            $content .= \html_writer::start_tag('div', ['class' => 'customfields-container']);

            if ($configStartDate != null && $configStartDate == 'show') {
                $content .= html_writer::tag('p', $startOfCourse);
            }

            if ($configEndDate != null && $configEndDate == 'show') {
                $content .= html_writer::tag('p', $endOfCourse);
            }

            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
        }

        if ($configEnrolment != null && $configEnrolment == 'show') {
            $content .= \html_writer::start_tag('div', ['class' => 'd-flex']);
            $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
            $content .= \html_writer::start_tag('div', ['class' => 'customfields-container']);

            $content .= html_writer::tag('p', '<b>' . get_string('enrolmentmethods') . '</b>');
            $content .= html_writer::start_tag('ul', ['class' => 'teachers']);
            foreach ($enrolmentMethods as $enMethod) {
                $method = get_object_vars($enMethod);

                $courseFull = count($students) > 0 && count($students) == $method['customint3'];

                $enrolSpots = $method['customint3'] == null || $method['customint3'] == 0 ? get_string('nolimit', 'theme_courseextension') : (count($students) . ' / ' . $method['customint3'] . ' ' . get_string('spots', 'theme_courseextension'));

                if ($courseFull && $method['enrol' == 'waitlistext']) {
                    $content .= html_writer::tag('li', get_string($method['enrol'], 'theme_courseextension') . ' ' . $enrolSpots . ', ' . get_string('waitlist-in-use', 'theme_courseextension'));
                }
                else if($courseFull){
                    $content .= html_writer::tag('li', get_string($method['enrol'], 'theme_courseextension') . ' ' . $enrolSpots . ', ' . get_string('coursefull', 'theme_courseextension'));
                }
                 else {
                    $content .= html_writer::tag('li', get_string($method['enrol'], 'theme_courseextension') . ' ' . $enrolSpots);
                }
            }
            $content .= html_writer::end_tag('ul', ['class' => 'teachers']);

            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
        }

        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }

    function enrol_get_instances_courseextension($courseid, $enabled)
    {
        global $DB, $CFG;

        if (!$enabled) {

            return $DB->get_records('enrol', array('courseid' => $courseid), 'sortorder,id');
        }

        $result = $DB->get_records('enrol', array('courseid' => $courseid, 'status' => ENROL_INSTANCE_ENABLED), 'sortorder,id');

        $enabled = explode(',', $CFG->enrol_plugins_enabled);
        foreach ($result as $key => $instance) {
            if (!in_array($instance->enrol, $enabled)) {
                unset($result[$key]);
                continue;
            }
            if (!file_exists("$CFG->dirroot/enrol/$instance->enrol/lib.php")) {
                // broken plugin
                unset($result[$key]);
                continue;
            }
        }

        return $result;
    }

    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null)
    {
        global $DB, $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        $configSorting = get_config('theme_courseextension', 'sorting');

        if ($configSorting == 'dateSortingNewest') {
            usort($courses, array($this, "sortNewest"));
        } else if ($configSorting == 'dateSortingOldest') {
            usort($courses, array($this, "sortOldest"));
        }


        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar(
                    $totalcount,
                    $page,
                    $perpage,
                    $paginationurl->out(false, array('perpage' => $perpage))
                );
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link(
                        $paginationurl->out(false, array('perpage' => 'all')),
                        get_string('showall', '', $totalcount)
                    ), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag(
                    'div',
                    html_writer::link($viewmoreurl, $viewmoretext, ['class' => 'btn btn-secondary']),
                    ['class' => 'paging paging-morelink']
                );
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link(
                $paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)
            ), array('class' => 'paging paging-showperpage'));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        // fetch the adminsettingblock to get config from it
        $blockrecord = $DB->get_record('block_instances', array(
            'blockname' => 'adminsettingblock',
            'pagetypepattern' => 'site-index'
        ), '*', IGNORE_MISSING);
        // using our record to create an instance
        
        if($blockrecord != null && $blockrecord != false){
            $blockinstance = block_instance('adminsettingblock', $blockrecord);
            $configHiddenCourses = $blockinstance->config->hiddencourses;
        }
        else{
            $blockinstance = null;
        }

        //Todo Apend
        // Check if adminsettingblock exists in db, incase it crashes when null
        
        if (is_siteadmin() && $blockinstance != null && $configHiddenCourses == 'hide') {
            foreach ($courses as $course) {

                if ($course->visible == 1) {
                    $coursecount++;
                    $classes = ($coursecount % 2) ? 'odd' : 'even';
                    if ($coursecount == 1) {
                        $classes .= ' first';
                    }
                    if ($coursecount >= count($courses)) {
                        $classes .= ' last';
                    }
                    $content .= $this->coursecat_coursebox($chelper, $course, $classes);
                }
            }
        }
        else {
            foreach ($courses as $course) {
                $coursecount++;
                $classes = ($coursecount % 2) ? 'odd' : 'even';
                if ($coursecount == 1) {
                    $classes .= ' first';
                }
                if ($coursecount >= count($courses)) {
                    $classes .= ' last';
                }
                $content .= $this->coursecat_coursebox($chelper, $course, $classes);
            }
        }


        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }



        $content .= html_writer::end_tag('div'); // .courses
        return $content;
    }

   /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function frontpage_available_courses(array $coursetoreomve=null) {
        global $CFG, $DB;
$id=5;
$configdata = $DB->get_records_sql('SELECT configdata FROM {customfield_field} WHERE id = ?', [$id]);



        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_courses_display_options(array(
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));


       $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options());

//include simplehtml_form.php
   require_once('blocks/coursesort/simplehtml_form.php');

  //Instantiate simplehtml_form
  $mform = new simplehtml_form();

    $sortvalue='';

    //$output .=  $mform->render();

    // Form processing and displaying is done here.
    if ($mform->is_cancelled()) {
        // If there is a cancel element on the form, and it was pressed,
        // then the `is_cancelled()` function will return true.
        // You can handle the cancel operation here.
    } else if ($fromform = $mform->get_data()) {
        // When the form is submitted, and the data is successfully validated,
        // the `get_data()` function will return the data posted in the form.
            $sortvalue =$mform->get_data();

    } else {
        // This branch is executed if the form is submitted but the data doesn't
        // validate and the form should be redisplayed or on the first display of the form.
        // Set anydefault data (if any).
    //    $mform->set_data($toform);
        // Display the form.
        $mform->display();


    }

    $searchfocusgrupp = [];
    $searchcategory = [];
    $searchutbform = [];

    if (is_object($sortvalue)) {

    $searchfocusgrupp = array_flip($sortvalue->searchfocusgrupp);
    $searchcategory = array_flip($sortvalue->searchcategory);
    $searchutbform = array_flip($sortvalue->searchform);

    $fieldMappings = [
         'searchfocusgrupp' => [
            'fieldid' => 9,
            'intvalues' => [1, 2, 3, 4],
            'courseidwithcustomfield' => &$courseidwithcustomfieldfocusgrupp
         ],
         'searchcategory' => [
             'fieldid' => 10,
             'intvalues' => [1, 2, 3, 4, 5, 6],
             'courseidwithcustomfield' => &$courseidwithcustomfieldcategory
         ],
         'searchutbform' => [
            'fieldid' => 11,
            'intvalues' => [1, 2],
            'courseidwithcustomfield' => &$courseidwithcustomfieldutbform
         ]
    ];

    foreach ($fieldMappings as $fieldKey => $fieldData) {
        $fieldid = $fieldData['fieldid'];
        $intvalues = $fieldData['intvalues'];
        $courseidwithcustomfield = &$fieldData['courseidwithcustomfield'];

        foreach ($$fieldKey as $key => $value) {
            if (array_key_exists($key, $$fieldKey)) {
                $intvalue = $intvalues[$key];
                $courseidwithcustomfield[$key] = $DB->get_records_sql(
                    'SELECT instanceid FROM {customfield_data} WHERE fieldid = ? AND intvalue = ?',
                        [$fieldid, $intvalue]
                );
            }
        }

        $courseidwithcustomfield = array_merge(...$courseidwithcustomfield);
    }

    $resultsort = array_merge($courseidwithcustomfieldfocusgrupp, $courseidwithcustomfieldcategory, $courseidwithcustomfieldutbform);

    $idofcoursestoshow = array_column($resultsort, 'instanceid');
    $courses = array_intersect_key($courses, array_flip($idofcoursestoshow));

  }



        $totalcount = core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }

        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }


    public function sortNewest($a, $b)
    {
        return strcmp($a->startdate, $b->startdate);
    }
    public function sortOldest($a, $b)
    {
        return strcmp($b->startdate, $a->startdate);
    }
}
