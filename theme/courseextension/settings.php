<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();                                                                                                
                                                                                                                                    
// This is used for performance, we don't need to know about these settings on every page in Moodle, only when                      
// we are looking at the admin settings pages.                                                                                      
if ($ADMIN->fulltree) {                                                                                                             
                                                                                                                                    
    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.                         
    $settings = new theme_boost_admin_settingspage_tabs('themesettingcourseextension', get_string('configtitle', 'theme_courseextension'));             
                                                                                                                                    
    // Each page is a tab - the first is the "General" tab.                                                                         
    $page = new admin_settingpage('theme_courseextension_general', get_string('generalsettings', 'theme_courseextension'));                             
                                                                                                                                    
    // dropdown for startdate                                                                                     
    $name = 'theme_courseextension/showstartdate';                                                                                                   
    $title = get_string('preset_show_startdate', 'theme_courseextension');                                                                                   
    $description = get_string('preset_desc_startdate', 'theme_courseextension');                                                                        
    $default = get_string('show');                                                                                                      
                                                                                                                                                                                                                                                              
    // show or not                                                                                   
    $choices1['show'] = get_string('show');
    $choices1['hide'] = get_string('hide');                                                                                     
                     
    // add page to settings
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices1);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // dropdown for enddate                                                                                     
    $name = 'theme_courseextension/showenddate';                                                                                                   
    $title = get_string('preset_show_enddate', 'theme_courseextension');                                                                                   
    $description = get_string('preset_desc_enddate', 'theme_courseextension');                                                                        
    $default = get_string('show');                                                                                                      
                                                                                                                                                                                                                                                               
    // show or not                                                                                   
    $choices2['show'] = get_string('show');
    $choices2['hide'] = get_string('hide');                                                                                         
                      
    // add page to settings
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices2);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);

    // dropdown for enrolments                                                                                     
    $name = 'theme_courseextension/showenrolments';                                                                                                   
    $title = get_string('preset_show_enrolments', 'theme_courseextension');                                                                                   
    $description = get_string('preset_desc_enrolments', 'theme_courseextension');                                                                        
    $default = get_string('show');                                                                                                      
                                                                                                                                                                                                                                                              
    // show or not                                                                                   
    $choices3['show'] = get_string('show');
    $choices3['hide'] = get_string('hide');                                                                                      
                     
    // add page to settings
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices3);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);

    $name = 'theme_courseextension/sorting';                                                                                                   
    $title = get_string('preset_sorting', 'theme_courseextension');                                                                                   
    $description = get_string('preset_desc_sorting', 'theme_courseextension');                                                                        
    $default = get_string('moodle_sorting' , 'theme_courseextension');                                                                                                      
                                                                                                                                                                                                                                                              
    // show or not                                                                                   
    $choices4['moodleSorting'] = get_string('moodle_sorting', 'theme_courseextension');
    $choices4['dateSortingNewest'] = get_string('datesorting_newest', 'theme_courseextension');
    $choices4['dateSortingOldest'] = get_string('datesorting_oldest', 'theme_courseextension');                                                                                           
                     
    // add page to settings
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices4);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);

    $name = 'theme_courseextension/hiddencourses';                                                                                                   
    $title = get_string('hiddencourses', 'theme_courseextension');                                                                                   
    $description = get_string('hiddencourses_desc', 'theme_courseextension');                                                                        
    $default = get_string('show');                                                                                                      
                                                                                                                                                                                                                                                              
    // show or not                                                                                   
    $choices5['show'] = get_string('show');
    $choices5['hide'] = get_string('hide');                                                                                       
                     
    // add page to settings
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices5);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);
                                                              
    $settings->add($page);




    // Advanced settings.
    $page = new admin_settingpage('theme_courseextension_advanced', get_string('advancedsettings', 'theme_courseextension'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_courseextension/scsspre',
        get_string('rawscsspre', 'theme_courseextension'), get_string('rawscsspre_desc', 'theme_courseextension'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_courseextension/scss', get_string('rawscss', 'theme_courseextension'),
        get_string('rawscss_desc', 'theme_courseextension'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

}
