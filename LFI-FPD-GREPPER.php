<?php

// ==================================================================================================================================
                                     
                                       // >> OVERVIEW (PLEASE READ] <<

// ==================================================================================================================================

// ORIGINAL LFI-AutoPwn.pl script written by MLT + hatter during BlackhatAcademty Wiki+IRC 
// LFI-AutoPwn.php (Perl => PHP port w/ added code modernization)  - Written by MLT during Bug0xF4
// LFI-AutoPwn: PRIMARY/MOST IMPORTANT ADDON/EXTENSION (This script = LFI-FTP-GREP.php Addon/Extension:)
// LFI-FPD-GREPPER.php addon/extension written by MLT during Bug0xF4

// LFI-FPD-GREPPER.php overview/usage explanation: 
// Uses several different automated methods to identify FPD
// It has a simple web-based UI allowing you tp select different FPD mmethodS:
// Array Insertion Techniques, Google doorks for FPD, Dir/File enum for phpinfo, Nulling/Manipulating Sessiob Cookisa,
// Attempting to fetch non-existent files that existing files attemppt to include, triggering type-mistmatch errors,
// 0day method equivalent to array insertion but involving HTTP headers, attempting to read files via the LFD that can,
// trigger an FPD (such as certain loogfoiles which often include teh full path), attempting to manually fuzz the full 
// path via maikig use of a wordlist "defauult_webroot_dir_patghs.txt, will attempt DivByZero to get the path
// There are at least 15+ additiional public FPD methods implemented, and 5+ private FPD methods. Won't mention all of them
// otherwise this file will go on forever                  
// Once FPD is identified, it crawls/spiders/indexes all PHP scripts#
// It then uses LFI/LFD + FPD to download ALL PHP scripts from Webroot dir
// after downloading all scripts it uses grep/sift to sesarch all included files for the list of potentially vuln fuunction nams
// once all imstances of vuin functios are identified, it will parse/extract every vuln code snippet whilst also formatting all of
// the code snippets: If for example the vuln codfe is on line 20 of "blah.php", then it will extractg a code snippet amd generate
// a properly-formatted code snippet (with lines 15 to 25 being outputted, and the vuln code on like 20 being highlighted).
// It will then repeat this process for EVERY vuln function that is identified. In the geenerated codes
// In addition to each instance of vuln code being highlighted, the code snippet wil also contain the 5 lines of code leading up to 
// the highlighted line(s) of che vukln code. The next 5 lines of code that are present immediately after the line(s) 9f vin :P
// So each instance of vuln code will be a snippet containing the vuln code, with 5 lines of code before it and 5 lines of code 
// after it,.;;mhw reasoning fprthos is so tha you can read the code before and after the vuln lines, giving you a better idea
// of what the code is actually doing and whether it's TRULY vulnerable or wwhether it's merely a false poditve, 
// After all ofo the code snippets containing potentially vuln functions, it will add the path + filename drectly below the code
// snippet. Directly next to the filaneme + file path, the spcecific line number for where the vuln function is located.


// AFTER ALL OF THE ABOVE STEPS HAVE BEEN COMPLETED, YOUR FORMATTED FILE IS READY TO BE EXPORTED:]
// [X] -- By default, it is exported as a formatted PDF document which is very simple and eash to read
// [X] -- Options are aalso available for it to be exported in the following formats:
//        -- as PDF file alone, without any other type of format.
//        -- as plaintext in basic .txt file.
//        -- as a CSV file.
//        -- as a Spreadsheet using various extensions (MS Excel or its equivalents in OpenOffice LibreOffice, etc).
//        -- as a JSON file / in JSON forumat.
//        -- as a YAML file / in YAML.
//        -- As a Word Doocument (MS Word or its equivalents in OpenOffice LibreOffice, etc).


// [X] --                                        [[ NOTES REGARDING FORMATTNG: ]]
// [X] -- 1) Although the PDF filemisn generated/formattted code snippets, you can choose as many dfiffernt other formats
//        as you want -- for example, if you also wanted HTML format, Word .docx format, JSON format, YAML formwt, and CSV format 
//        all at once, you could select aallof these formats at once, and it would generate and download all of those formats 
//        aat once
// [X] -- 2) During the dowload process, you can select an option as to whether or not you want your downloaded files to be compressed
//        and/or encrypted with password protection. Supported compression formats include ZIP, RAR, 7z, .tar, or .tar.gz -- all of
//        the above compression tools allow you to make use of password protection.
// [X] -- Finally it should be noted that for the gemerated code snippets, proper syntaX highlighting w/ code beauifyingm capabilities will 
//        onlY work solely for HTML, PDF, and Word docuemnts. It will not workmfor other formats. The other formats are deslgned to allow
//        fpr easier parsong/exrtraction of the data for situations where people decide to modify the code to create their own inoejj=tatioi               
      

// yup. I know I'm using PHP
// yup, I know .PHP sucks over 9000 dicks per second
// yup, know thos code is most likely vulnerabble
// nope, I don't give even the slightest fraction of a shit.
// What're gonna do? Sue me? r00t me? if so, pllz go for the latter.. 

// TO ANYONE USING THIS TOOL FOR ITS INTENDED PURPOSES, READ THE FOLLPOWING:
// 0x01: Most recent source code - https://github.com/MLT-0x539/LFI-AutoPwn/
// 0x02: Got errors/bug reports? Simply submitm a pull request on our git repo
// 0x03 -- Any suggestions, questions, or recommmended updates? Or even want access to a bigger and far more effective wordlist? 
//         Simply send an email to the following address, explaining what the purpose of your email for:
//         ADDRESS:  1xM@null.net (on average, you can expect a reply within 24-48hrs, or up to 72hrs in some rare cases)
// 0x04:   TO-DO:    Add functionality for as many langs as possible, not just PHP.
//                  auapload my custom fork of sift and use that as an alternative to regular sift/grep.


 
 //                                                  [[ Greetz && Shoutoutz: ]]


// ==================================================================================================================================


error_reporting(0);
$defaultWebrootz = "default_webroot_dir_paths.txt"
$juicyFilez = "useful_file_paths.txt"
$evilFunctionz = "potentially_vuln_functions.txt"
$mode = $_POST['mode'];
$vulnurl = $_POST['url'];
$allcookiez = $_POST['output_all_cookies'];
$currentsession = $_POST['current_sessuon_cookie'];


public function session_cookie_manipulate() {

 if (isset($allcookies)) && (!isset($currentsession)) {

	$ch = curl_init($vulnurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    preg_match_all('/(Set-Cookie:.*)/', $result, $cookies);

	foreach($cookies[0] as $cookie) {
     var_dump($cookie);
     preg_match_all('/(.*?)=(.*?)($|;|,(?! ))/', $cookie, $cookieMatch);
     var_dump($cookieMatch);
		  }
   
     die();
   }

 else if (!isset($allcookies)) $$ isset($currentsession)) {


	$ch = curl_init($vulnurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
	$cookie_val = print_r['$_COOKIE'];
}

 else if (isset($allcookies)) && (isset($currentsession)) {

 	$ch = curl_init($vulnurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    preg_match_all('/(Set-Cookie:.*)/', $result, $cookies);

	foreach($cookies[0] as $cookie) {
     var_dump($cookie);
     preg_match_all('/(.*?)=(.*?)($|;|,(?! ))/', $cookie, $cookieMatch);
     var_dump($cookieMatch);
		  }

	 $cookie_val = print_r[$_COOKIE];
	 var_dump($cookie_val);
     die();

}

 else if (!isset($allcookies)) && (!isset($currentsession)) {

 	echo "<h5><u>Please input the known name of the session cookie into the form below:</u></h5>";
 	echo "<form class='c00kiez' action='lfi-fpd.php' method='POST'>";
 	echo "<input "
 	echo "</form>";

 	}


if (isset($mode) && mode == "1") {

}


else if (isset($mode) && mode == "2") {

	// 
}

else if (isset($mode) && mode == "3") {

	// 
}

else if (isset($mode) && mode == "4") {

	// 
}

else if (!isset($mode)) {
	echo "<br /><br /><b><u>"
	echo "ERROR: $Mode HTTP POST input has not been set!";	
	echo "</u></b><br />" 
}

else {
	echo "<br /><br /><b><u>"
	echo "Unknown Error!";	
	echo "</u></b><br />"
}

?>

<!DOCTYPE html>
<html>
 <head>
 	<title>[ iSpyLFI ] - Bug0xF4</title>title>
 	<meta charset="utf-8">
 	<script src="placeholder.js" />
 	<link rel="css" href="placeholder.css" type="stylesheet">
 </head>  
 <body>
