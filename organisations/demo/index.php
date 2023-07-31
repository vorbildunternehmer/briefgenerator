<?php

namespace md2pdf;

class PAGE {

  public static function getStyle () {
    return file_get_contents(dirname(__FILE__) . '/style.css');
  }

  public static function getFrom () {
    return 'Demo';
  }

  public static function getFooter ($meta, $title, $date) {
    return array (
      'L' => array (
          'content' => $date,
          'font-size' => 10,
          'font-style' => 'R',
          'font-family' => 'opensans',
          'color'=>'#000000'
      ),
      'C' => array (
          'content' => '{PAGENO}/{nb}',
          'font-size' => 10,
          'font-style' => 'R',
          'font-family' => 'opensans',
          'color'=>'#000000'
      ),
      'R' => array (
          'content' => $title,
          'font-size' => 10,
          'font-style' => 'R',
          'font-family' => 'opensans',
          'color'=>'#000000'
      ),
      'line' => 0,
    );
  }
  public static function getFonts () {
    
    // lowercase letters only in font key
    return [ 
          "opensans" => array(
          
            'R' => 'OpenSans/OpenSans-Light.ttf',
            'B' => 'OpenSans/OpenSans-ExtraBold.ttf',
            'I' => 'OpenSans/OpenSans-LightItalic.ttf',
            'BI' => 'OpenSans/OpenSans-ExtraBoldItalic.ttf',
          )
        ];
  }
}

?>