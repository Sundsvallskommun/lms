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
        if(is_null($badge_other["dateexpire"])){
            $expire_text_pdf = "";
        }else{
            $badge_expire_unix =  $badge_other["dateexpire"];
            $badge_expire = gmdate("Y-m-d", $badge_expire_unix);
            $expire_text_pdf="Giltig till: ". $badge_expire;
        }


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


        //kollar om pdf finns annars skapa den
        if (!file_exists($filepath . $filename)) {

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image($CFG->dirroot .'/local/badgeevent/logo/Sundsvalls.png',20,6,30);
            $pdf->Ln(5);

            $pdf->Cell(60);
            $pdf->SetFont('Arial','B',24);
            $pdf->Cell(20,1, iconv('UTF-8', 'windows-1252',"Kompetensplattformen"));
            $pdf->Ln(30);
            $pdf->Cell(60);
            $pdf->Cell(20,1, iconv('UTF-8', 'windows-1252',"Diplom: " .$badgename->name));
            $pdf->Ln(20);
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(60);
            $pdf->Cell(40,20, iconv('UTF-8', 'windows-1252', "Namn: " . $user->firstname . " ". $user->lastname));

            $pdf->Ln(20);
            $pdf->Cell(60);
            $pdf->Cell(40,20, iconv('UTF-8', 'windows-1252', $expire_text_pdf));
            $pdf->Ln(20);

            $pdf->Output('F', $filepath .$filename );


       }
	    
 
        // Skickar Email med pdf diplom
        // Från fil
        email_to_user($user, $contact, "Diplom: " . $badgename->name , '', "Diplom för genomförd kurs. Diplom bifogas som pdf i mailet", $filepath . $filename ,$filename,true);
        unlink($filepath .$filename);

    }


}



