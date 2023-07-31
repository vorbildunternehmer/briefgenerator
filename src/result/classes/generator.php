<?php

namespace md2pdf;

use Symfony\Component\Yaml\Parser;


class PDF_GENERATOR {
  public function get($data) {
    
    require '/organisations/'.$data["organisation"].'/index.php';
    $class = 'md2pdf\\' . 'PAGE';
    $Parsedown = new \Parsedown();
    
    $meta = $data['meta'];

    $yml = new Parser();
    $meta = $yml->parse($meta);
    (isset($meta["from"]))? $meta["from"]: ($meta["from"] = $class::getFrom());

    $text = $Parsedown::instance()->setBreaksEnabled(true)->text($data["markdown"]);

    $address = $Parsedown::instance()->setBreaksEnabled(true)->text($this->getAddress($meta));
    $date = $Parsedown::instance()->setBreaksEnabled(true)->text($this->getDate($meta));
    $title = $this->getTitle($meta);
    $pages = $this->getPages();
    
    $this->setupMPDF($class);
    $this->pdf->defaultfooterline = 0;
    $this->setPDFmeta($meta);

    
    $this->pdf->WriteHTML($class::getStyle(),\Mpdf\HTMLParserMode::HEADER_CSS);
    
    $this->getFooter($class, $meta, $title, $date);
    
    if(isset($data["inhaltsverzeichnis"])) {
      $this->getTableOfContents($title, $address, $date, $pages);
    } else {
    
    $this->pdf->writeHTML('<div class="date">' . $date . '</div>', \Mpdf\HTMLParserMode::HTML_BODY);
    $this->pdf->writeHTML('<div class="address">' . $address . '</div>', \Mpdf\HTMLParserMode::HTML_BODY);
    $this->pdf->writeHTML('<div class="totalpages">' . $pages . '</div>', \Mpdf\HTMLParserMode::HTML_BODY);
    
    }
    
    $this->pdf->writeHTML('<div class="title">' . $title . '</div>', \Mpdf\HTMLParserMode::HTML_BODY);
    
    
    $this->pdf->writeHTML('<div class="page">' . $text . '</div>', \Mpdf\HTMLParserMode::HTML_BODY);  
    
    
    $this->pdf->Output($this->getDate($meta).'_'.$this->slugify(ucfirst($data["organisation"])).'_'.$this->slugify($title).'.pdf', 'I');
  }
  
  private function getTableOfContents($title, $address, $date, $pages) {
    $this->pdf->writeHTML('<div class="date"></div>', \Mpdf\HTMLParserMode::HTML_BODY);
    $this->pdf->h2toc = array('H1' => 0, 'H2' => 1, 'H3' => 1);
    $this->pdf->TOCpagebreakByArray([
      'toc_efvalue' => 1,
      'toc_ofvalue' => 1,
      'TOCusePaging' => true,
      'TOCuseLinking' => true,
      'toc_preHTML' => '<div class="date">'.$date.'</div><div class="address">'.$address.'</div><div class="totalpages">' . $pages . '</div><div class="title">Inhaltsverzeichnis</div>',
      'ofvalue' => false,
      'efvalue' => false,
      'toc_ofname' => 'tableOfContents',
      'toc_efname' => 'tableOfContents',
      'ofname' => 'tableOfContents',
      'efname' => 'tableOfContents',
      'resetpagenum' => false
    ]);
  }

  private function getFooter($class, $meta, $title) {
    $this->pdf->DefFooterByName('standard', $class::getFooter($meta, $title, $this->getDate($meta)));
  }

  private function setPDFmeta($meta) {
    $title = $this->getTitle($meta);

    $this->pdf->setTitle($title);
    $this->pdf->setSubject($title);
    $this->pdf->setAuthor($meta["from"]);
    $this->pdf->setCreator($meta["from"]);

    (isset($meta["tags"]))? $this->pdf->setKeywords($meta["tags"]): '';
  }

  private function getTitle($meta) {
    return (isset($meta["title"]))? ($meta["title"]): 'Untitled Document';
  }

  private function getPages() {
    return 'Seitenanzahl: {nb}';
  }

  private function getDate($meta) {
    return (isset($meta["date"]))? date('Y-m-d', $meta["date"]): date("Y-m-d");
  }

  private function getAddress($meta) {

    $address = '';
    $address .= '*'. $meta["from"] . '*';
    $address .= "\n";
    $address .= '**'. $meta["name"] . '**';
    $address .= "\n";
    $address .= (isset($meta["description"]))? $meta["description"]: $meta["straße"];
    $address .= "\n";
    $address .= (isset($meta["contact"]))? $meta["contact"]: $meta["ort"];

    return $address;

  }

  private function setupMPDF($class) {

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $fontDir = '/fonts/';

    $this->pdf = new \Mpdf\Mpdf([
      'mode' => 'utf-8',
      'format' => [210, 297],
      'orientation' => 'P',
      'fontDir' => array_merge($fontDirs, [$fontDir]),
      'fontdata' => $fontData + $class::getFonts(),
        'options' => [ 
          'defaultheaderline' => 0,  //for header
          'defaulfooterline' => 0  //for footer
        ]
      ]
    );
  }

  private function slugify($text, string $divider = '-')
  {
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    // $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }
}

?>