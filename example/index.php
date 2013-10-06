<?php
// require the library
require_once '../require.php';  // path/to/WinkForm/require.php

// typically your own autoloader should be configured to look in your forms directory
require_once 'TestForm.php';

// create the form
$form = new TestForm();

// for sake of the example the html and css is inline here
?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Example</title>

    <!-- DateInput uses jquery ui date picker -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

    <!-- Twitter Bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
    body     { font-family:Comic Sans, Comic Sans MS, cursive;  }
    label    { float:left; width:200px;, padding:5px; }
    input    { float:left; padding:5px; margin:5px; }
    .clear   { clear:both; }
    div#testCheckbox-container label { width:auto; }
    div#container { margin:100px; padding:20px 20px 50px 20px; border:10px dotted pink; }
    div#checkboxes-container label,
    div#radio-container label { width:auto; margin-right:10px; margin-left:2px; }
    </style>
</head>
<body>
    <div id="container">
        <?php echo $form->render(); ?>
    </div>
</body>
</html>
