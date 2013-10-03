<?php
require_once("../dompdf_config.inc.php");



    $_POST["html"] = stripslashes("salut");
  
  
  
  $dompdf = new DOMPDF();
  $dompdf->load_html($_POST["html"]);
  $dompdf->set_paper();
  $dompdf->render();

  $dompdf->stream("dompdf_out.pdf");

  exit(0);

