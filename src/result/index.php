<?php

require_once __DIR__ . '/classes/pdf.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

md2pdf\PDF::deploy($_POST);


?>