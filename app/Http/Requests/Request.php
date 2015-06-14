<?php namespace App\Http\Requests;

use App\Exceptions\AppException;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest {

    /**
     * 抛异常
     * @param bool $isThrow
     * @param int $errorCode
     * @throws AppException
     */
    protected function throwException( $isThrow = false ,$errorCode = 0){
        if($isThrow){
            if( $errorCode == 0 ){
                $errorCode = AppException::ERROR_CODE_TYPE;
            }
            throw new AppException($errorCode);
        }
    }


}
