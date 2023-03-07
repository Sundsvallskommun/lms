<?php

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/fpdf/fpdf.php');


class local_badgeevent_observer
{

    public static function badge_awarded(\core\event\badge_awarded $event)
    {
        global $DB, $CFG;

        //event_data  är data från badge_awarded
        $event_data = $event->get_data();

        //Hämta badgeID och id på vem som fick badge
        $badgeid = $event_data['objectid'];
        $user_issue = $event_data['relateduserid'];
       // I arrayen other från event_data hämta när märket går ut om den har slutdatum
        $badge_other = $event_data["other"];


        $user = \core_user::get_user($user_issue);

        /*
        mdl_enrol customint5 0/1 om det finns ett steg två = 1
        customint6 id för badgen
        */
        $ifsteptwo = $DB->get_records_sql('SELECT * FROM {enrol} WHERE customint5 = ? AND customint6 = ?', [1,$badgeid]);
        $badgename = $DB->get_record_sql('SELECT name FROM {badge} WHERE id = ?', [$badgeid]);

        //  'Påminelse om nästa kurs';
        if($ifsteptwo){

        $contact='';
        $subject = get_string('reminder','local_badgeevent');
        $message = get_string('message','local_badgeevent') . " " . $badgename->name;
        //'Det finns ett till steg på kursen '. $badgename->name;

            email_to_user($user, $contact, $subject, '', $message);

        }



        $filepath = $CFG->dirroot . '/tempfiles/';
	$filename = $user->username . "-" . $badgename->name.'.pdf';
        // skapa upp mappen om den inte finns
        if (!file_exists($filepath)) {
            mkdir($filepath, 0777, true);
        }


 	// bild på märket för pdf
           $badges = badges_get_badges(BADGE_TYPE_SITE);
           $badgeObj = array_column($badges, null, 'id')[$badgeid] ?? false;
           $badge_context = $badgeObj->get_context();

           $imageurlObject = moodle_url::make_pluginfile_url($badge_context->id, 'badges', 'badgeimage', $badgeid, '/', 'f2', FALSE);
           $imageurl = explode(" ", $imageurlObject);
           $imageurl = $imageurl[0];

        //kollar om pdf finns annars skapa den
        if (!file_exists($filepath . $filename)) {

            $pdf = new FPDF();
        $pdf->AddPage('P', 'A4');
        $pdf->Rect(5, 5, 200, 287, 'D');
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->Image($CFG->dirroot .'/local/badgeevent/logo/Sundsvalls.png',20,20,30);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTopMargin(10);
        $pdf->SetLeftMargin(10);
        $pdf->SetRightMargin(10);

        
        $pdf->SetXY(10, 60);
        $pdf->SetFont('', 'B', 20);
        $pdf->Cell(0, 7, 'Kompetensplattformen', 0, 1, 'C', false);
       
        $pdf->SetTextColor(100,100,100);
        $pdf->SetXY(10, 75);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 5, iconv('UTF-8', 'windows-1252',$badgename->name), 0, 1, 'C', false);
        $pdf->SetTextColor(0);
       
        $pdf->SetFontSize(20);
        $pdf->Text(80, 110, iconv('UTF-8', 'windows-1252', $user->firstname . " ". $user->lastname));


        if(is_null($badge_other["dateexpire"])){
            $expire_text_pdf = "";
            $badge_expire = "";
            
        }else{
            $expire_text_pdf = get_string('validuntil','local_badgeevent'); 
            $badge_expire_unix =  $badge_other["dateexpire"];
            $badge_expire = gmdate("Y-m-d", $badge_expire_unix);
            $pdf->Text(140, 253, iconv('UTF-8', 'windows-1252',$expire_text_pdf));
            $pdf->Line(141, 256, 200, 256);
            $pdf->Text(142, 262, iconv('UTF-8', 'windows-1252', $badge_expire));
            
        }
  
            $issued_text_pdf = get_string('issued','local_badgeevent');  
            $issued_expire_unix =  $event_data["timecreated"];
            $badge_issued = gmdate("Y-m-d", $issued_expire_unix);
            $pdf->Text(25, 250, iconv('UTF-8', 'windows-1252',$issued_text_pdf));
            $pdf->Line(26, 254, 75, 254);
            $pdf->Text(27, 260,  iconv('UTF-8', 'windows-1252', $badge_issued));
            


            $datatypelist  = array(
                IMAGETYPE_GIF => "GIF",
                IMAGETYPE_JPEG => "JPEG",
                IMAGETYPE_PNG => "PNG",
                IMAGETYPE_JPG => "JPG");
                


           $pdf->Output('F', $filepath .$filename );


       }
	    
        // Skickar Email med pdf diplom
        // Från fil
         email_to_user($user, $contact, "Diplom: " . $badgename->name , '', "Diplom för genomförd kurs. Diplom bifogas som pdf i mailet", $filepath . $filename ,$filename,true);
        unlink($filepath .$filename);

    }


}



