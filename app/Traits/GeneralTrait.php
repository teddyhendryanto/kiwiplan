<?php namespace App\Traits;

use Input;

trait GeneralTrait {

  public function emptyStringToNull($string){
    if(trim($string) === ''){
      $string = null;
    }
    return $string;
  }

}
