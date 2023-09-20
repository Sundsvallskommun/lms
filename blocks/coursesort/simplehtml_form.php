<?php
require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform {
    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $options = array(
            'multiple' => true,
            'noselectionstring' => '',
            'class' => 'sortform'
        );


        $focusgrupp = array('Chef','Medarbetare','Fackligrepresentant','Skyddsombud');
        $mform->addElement('autocomplete', 'searchfocusgrupp', 'Målgrupp', $focusgrupp, $options);



        $category = array('Sjukvård','Skola');
        $mform->addElement('autocomplete', 'searchcategory', 'Kategori', $category, $options);



        $utbildningsform = array('Digitalt','lärarled');
        $mform->addElement('autocomplete', 'searchform', 'Utbildningsform',$utbildningsform, $options);


        $mform->addElement('submit', 'submitbutton', "Visa kurser",['class' => 'sortform']);

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
