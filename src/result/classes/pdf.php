<?php
namespace md2pdf;

require __DIR__ . '/generator.php';

class PDF {
  public static function deploy($data) {
    $gen = new PDF_GENERATOR();
    return $gen->get($data);
  }
}

?>