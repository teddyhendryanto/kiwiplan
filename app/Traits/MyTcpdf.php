<?php namespace App\Traits;

use Input;
use PDF;

trait MyTcpdf {

  public function CreateTextBox($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y);
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::Cell($width, $height, $textval, 0, false, $align);
  }

  public function CreateTextBoxDetail($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'J') {
    PDF::SetXY($x, $y);
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 0, $align, 0, 0, '' ,'', true, 0, false, false, 0, 'T', false);
  }

  public function CreateTextBoxHistory($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::Cell($width, $height, $textval, 0, false, $align);
  }

  public function CreateTextBoxDetailHistory($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'J') {
    PDF::SetXY($x, $y);
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 0, $align, 0, 0, '' ,'', true, 0, false, false, 0, 'T', false);
  }

  public function CreateTextBoxCustom($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 1, $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxNoline($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 0, $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxTopBottom($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'TB', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxTopBottomLeftRight($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'TBLR', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxLeftRight($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'LR', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxTopLeftRight($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'TLR', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxBottomLeftRight($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'BLR', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function CreateTextBoxTop($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L') {
    PDF::SetXY($x, $y); // 20 = margin left
    PDF::SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
    PDF::MultiCell($width, $height, $textval, 'T', $align, 0, 0, '' ,'', true, 0, true, false, 0, 'C', true);
  }

  public function Footer() {
    PDF::SetY(-15);
    PDF::SetFont(PDF_FONT_NAME_MAIN, 'I', 7);
    PDF::Cell(0, 12, ' '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
  }

}
