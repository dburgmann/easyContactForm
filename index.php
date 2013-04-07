<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
error_reporting(E_ALL);
include("class/contactform.php");
$cf = new ContactForm();
$cf->process();
?> 
<html>
  <head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="contactForm.css">
  </head>
  <body>
    <?php echo $cf->form(); ?>
  </body>
</html>
