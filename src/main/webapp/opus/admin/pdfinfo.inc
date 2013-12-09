<?php
if ( strtolower($ext) == "pdf" ) {
	fwrite($ind, "<br>\n<font size=\"-2\">");                    
	$exe = $pdfinfopath." ".escapeshellarg($datei);
	unset($output);
	exec($exe, $output, $return_var);
	if ($return_var == 0) {
		fwrite($ind, "<br><b>pdfinfo</b> (".$return_var."):\n<!--\n".$exe."\n-->\n");
		for ($p=0; $p <= count($output); $p++ ) {
			fwrite($ind, "<br>\n".$output[$p]);
		}
	} 
	else {
		fwrite($ind, "<b>".$exe."</b> (Result: ".$return_var." !!!)");
	}
	fwrite($ind, "</font>\n");                      
}
?>
