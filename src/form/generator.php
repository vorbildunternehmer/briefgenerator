<?php

namespace form;

class GENERATOR {

  static function get(){

    $form = file_get_contents(dirname(__FILE__) . '/index.html');

    $options = [];
    foreach(scandir('/organisations') as $folder) {
      if(!file_exists($folder)) {
        $options[] = sprintf('<option value="%s">%s</option>', $folder, ucFirst($folder));
      }
    }

    return sprintf($form, implode('', $options));
  }
}

?>