<?php
/**
 * Helper.php, Creado el 10/12/2020 a las 23:52
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class Helper {

	/**
	 * @param $data : it comes in the format of $request->all()
	 * @param $rules : standard array of rules
	 * @param $messages : standard array of messages
	 *
	 * @return array: of strings of errors, one per each error
	 */
	public static function getValidationErrors( $data, $rules, $messages = [] ) {
		$validation = \Illuminate\Support\Facades\Validator::make( $data, $rules, $messages );
		$errors     = [];
		if ( $validation->fails() ) {
			foreach ( $validation->messages()->getMessages() as $fieldName => $messages ) {
				foreach ( $messages as $key => $value ) {
					$errors[] = $value;
				}
			}
		}

		return $errors;
	}

	public static function updateErrors( $data, $item ) {
		return Helper::getValidationErrors(
			$data, $item->updateRules(), $item->validationMessages()
		);
	}

	public static function storeErrors( $data, $item ) {
		return Helper::getValidationErrors(
			$data, $item->storeRules(), $item->validationMessages()
		);
	}

    /**
     * @param $file // $request->file('image')
     * @param $size // An integer representing the width
     * @param $directory // subidirectory to store the image as /img/muscles
     * @return string // the complete url of the file stored
     */
	public static function storeImage($file, $size, $directory){
        $image = Image::make($file)->encode('jpg', 75)->resize($size, null, function ($constraint) {
            $constraint->aspectRatio();
        })->stream();
        $filename = $file->getClientOriginalName();

        // get the first part of the name if it exists
        if (strpos($filename, ".")){
            $filename = explode(".", $filename)[0];
        }

        $filename = $directory . '/' . $filename . '-' . date('YmdH') . '.jpg';
        Storage::disk('midisco')->put($filename, $image->__toString());
        return $filename;
    }


  //entry must be a string formatted with 2 decimals, without separator of miles
  public function getFullLiteralMoney($ammount){
    $intAmmount = explode(".", $ammount)[0];
    $centsAmmount = explode(".", $ammount)[1];
    $cents =$centsAmmount.'/100 BOLIVIANOS';
    return $this->getLiteralMoney( $intAmmount ).' '.$cents;
  }

  public function getLiteralMoney($ammount) {
    return $this->conversions($ammount);
  }

  public function conversions($ammount) {
    // limit
    if ($ammount >= 10000000)
      return "";

    switch ($ammount) {
      case 0:
        return "CERO";
      case 1:
        return "UN"; //UNO
      case 2:
        return "DOS";
      case 3:
        return "TRES";
      case 4:
        return "CUATRO";
      case 5:
        return "CINCO";
      case 6:
        return "SEIS";
      case 7:
        return "SIETE";
      case 8:
        return "OCHO";
      case 9:
        return "NUEVE";
      case 10:
        return "DIEZ";
      case 11:
        return "ONCE";
      case 12:
        return "DOCE";
      case 13:
        return "TRECE";
      case 14:
        return "CATORCE";
      case 15:
        return "QUINCE";
      case 20:
        return "VEINTE";
      case 30:
        return "TREINTA";
      case 40:
        return "CUARENTA";
      case 50:
        return "CINCUENTA";
      case 60:
        return "SESENTA";
      case 70:
        return "SETENTA";
      case 80:
        return "OCHENTA";
      case 90:
        return "NOVENTA";
      case 100:
        return "CIEN";

      case 200:
        return "DOSCIENTOS";
      case 300:
        return "TRESCIENTOS";
      case 400:
        return "CUATROCIENTOS";
      case 500:
        return "QUINIENTOS";
      case 600:
        return "SEISCIENTOS";
      case 700:
        return "SETECIENTOS";
      case 800:
        return "OCHOCIENTOS";
      case 900:
        return "NOVECIENTOS";
      case 1000:
        return "MIL";
      case 1000000:
        return "UN MILLON";
      case 2000000:
        return "DOS MILLONES";
      case 3000000:
        return "TRES MILLONES";
      case 4000000:
        return "CUATRO MILLONES";
      case 5000000:
        return "CINCO MILLONES";
      case 6000000:
        return "SEIS MILLONES";
      case 7000000:
        return "SIETE MILLONES";
      case 8000000:
        return "OCHO MILLONES";
      case 9000000:
        return "NUEVE MILLONES";
    }
    if ($ammount < 20) {
      return "DIECI" . $this->conversions($ammount - 10);
    }
    if ($ammount < 30) {
      return "VEINTI" . $this->conversions($ammount - 20);
    }
    if ($ammount < 100) {
      return $this->conversions((int)($ammount / 10) * 10) . " Y " . $this->conversions($ammount % 10);
    }
    if ($ammount < 200) {
      return "CIENTO " . $this->conversions($ammount - 100);
    }
    if ($ammount < 1000) {
      return $this->conversions((int)($ammount / 100) * 100) . " " . $this->conversions($ammount % 100);
    }
    if ($ammount < 2000) {
      return "MIL " . $this->conversions($ammount % 1000);
    }
    if ($ammount < 1000000) {
      $temp = $this->conversions((int)($ammount / 1000)) . " MIL";
      if ($ammount % 1000 != 0) {

        $temp .= " " . $this->conversions($ammount % 1000);
      }
      return $temp;
    }
    if ($ammount < 2000000) {
      return "UN MILLON " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 3000000) {
      return "DOS MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 4000000) {
      return "TRES MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 5000000) {
      return "CUATRO MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 6000000) {
      return "CINCO MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 7000000) {
      return "SEIS MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 8000000) {
      return "SIETE MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 9000000) {
      return "OCHO MILLONES " . $this->conversions($ammount % 1000000);
    }
    if ($ammount < 10000000) {
      return "NUEVE MILLONES " . $this->conversions($ammount % 1000000);
    }
    return "";
  }

}
