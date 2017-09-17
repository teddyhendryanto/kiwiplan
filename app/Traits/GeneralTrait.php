<?php namespace App\Traits;

use Input;

trait GeneralTrait {

  public function emptyStringToNull($string){
    if(trim($string) === ''){
      $string = null;
    }
    return $string;
  }

  public function emptyStringToDash($string){
    if(trim($string) === ''){
      $string = '---';
    }
    return $string;
  }

  public function fixWidthWords($string, $size){
    $string = str_pad(trim($string), $size);
    return $string;
  }

}
