<?php namespace App\Http\Requests;

class InputData extends Request {

    public function getOriginParam(){
        return $this->all();
    }

    /**
     * @param $key
     * @param int $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return int
     */
    public function getInteger($key,$defaultValue=0,$isThrowException=false,$errorCode=0){
        if(!$this->has($key)){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return (integer)$this->get($key);
    }

    /**
 * @param $key
 * @param int $defaultValue
 * @param bool $isThrowException
 * @param int $errorCode
 * @return int
 */
    public function getPositiveInteger($key,$defaultValue=0,$isThrowException=false,$errorCode=0){
        if(!$this->has($key)||($value=(integer)$this->get($key))<=0){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return $value;
    }

    /**
     * @param $key
     * @param bool $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return boolean
     */
    public function getBoolean($key,$defaultValue=false,$isThrowException=false,$errorCode=0){
        if(!$this->has($key)){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return $this->get($key) ? true : false;
    }

    /**
     * @param $key
     * @param float $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return float
     */
    public function getFloat($key,$defaultValue=0.0,$isThrowException=false,$errorCode=0){
        if(!$this->has($key)){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return (float)$this->get($key);
    }

    /**
     * @param $key
     * @param float $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return float
     */
    public function getPositiveFloat($key,$defaultValue=0.0,$isThrowException=false,$errorCode=0){
        if(!$this->has($key)||($value=(float)$this->get($key))<=0){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return $value;
    }

    /**
     * @param $key
     * @param string $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return float
     */
    public function getString($key,$defaultValue='',$isThrowException=false,$errorCode=0){
        if(!$this->has($key)){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return \HelperString::encode($this->get($key));
    }

    /**
     * @param $key
     * @param string $defaultValue
     * @param bool $isThrowException
     * @param int $errorCode
     * @return float
     */
    public function getTrimString($key,$defaultValue='',$isThrowException=false,$errorCode=0){
        if(!$this->has($key)||strlen($value=trim(\HelperString::encode($this->get($key))))<=0){
            $this->throwException($isThrowException,$errorCode);
            return $defaultValue;
        }
        return $value;
    }

    /**
     * 获取上传的图片文件
     * @param	string	$key	字段名
     * @param	string	$defaultPath	默认值
     * @param	boolean	$isThrowException	是否抛出异常
     * @param	int	$errorCode	错误码
     * @param	boolean	$isCompress	是否到达压缩规格后就自动压缩
     * @param	boolean	$isWriteWaterMark	是否需要添加水印
     * @param	int	$width	指定宽度
     * @param	int	$height	指定高度
     * @param	string	$appendImage	追加的图片地址
     * @param	string	$forceImageType	强制转换的图片类型
     * @return	array(
     * 				path :string
     * 				width: int
     * 				height: int
     * 				binary: byte[]
     * 				tmpPath: string
     * 			)
     */
    public function getUploadImage(
        $key ,
        $defaultPath = '' ,
        $isThrowException = false ,
        $errorCode = 0 ,
        $isCompress = false ,
        $isWriteWaterMark = false ,
        $width = 0 ,
        $height = 0 ,
        $appendImage = '' ,
        $forceImageType = 'jpg' )
    {
        if( !$this->hasFile($key) )
        {
            $this->throwException( $key , $isThrowException , $errorCode );

            return $this->_getDefaultImageParameters( $defaultPath );
        }

        return $this->_uploadImage( $key , $this->file($key) , $defaultPath ,
            $isThrowException , $errorCode ,
            $isCompress , $isWriteWaterMark , $width , $height ,
            $appendImage , $forceImageType );
    }


    /**
     * 获取默认图像参数
     * @param	string	$path	路径
     * @return	array(
     * 				path :string
     * 				width: int
     * 				height: int
     * 				binary: byte[]
     * 				tmpPath: string
     * 			)
     */
    private function _getDefaultImageParameters( $path )
    {
        if( empty( $path ) )
        {
            return array(
                'path' => $path ,
                'width' => 0 ,
                'height' => 0 ,
                'binary' => '' ,
                'tmpPath' => '' ,
            );
        }

        $binaryContent = file_get_contents( $path );
        $image = \Helper_Thumbnail::getInstance();
        $image->setSrcImg( $binaryContent , \Helper_Thumbnail::checkImgType( $binaryContent ) );

        return array(
            'path' => $path ,
            'width' => $image->src_w ,
            'height' => $image->src_h ,
            'binary' => $binaryContent ,
            'tmpPath' => $path ,
        );
    }

    /**
     * 获取上传的图片文件
     * @param	string	$key	字段名
     * @param	string	$tmpPath	临时文件目录
     * @param	string	$defaultPath	默认值
     * @param	boolean	$isThrowException	是否抛出异常
     * @param	int	$errorCode	错误码
     * @param	boolean	$isCompress	是否到达压缩规格后就自动压缩
     * @param	boolean	$isWriteWaterMark	是否需要添加水印
     * @param	int	$width	指定宽度
     * @param	int	$height	指定高度
     * @param	string	$appendImage	追加的图片地址
     * @param	string	$forceImageType	强制转换的图片类型
     * @return	array(
     * 				path :string
     * 				width: int
     * 				height: int
     * 				binary: byte[]
     * 				tmpPath: string
     * 			)
     */
    private function _uploadImage( $key , $tmpPath ,
        $defaultPath = '' ,
        $isThrowException = false ,
        $errorCode = 0 ,
        $isCompress = false ,
        $isWriteWaterMark = false ,
        $width = 0 ,
        $height = 0 ,
        $appendImage = '' ,
        $forceImageType = 'jpg' )
    {
        //上传图片
        $uploadResult = \Helper_Image::getInstance()
            ->setBinaryContent(
                $binaryContent = file_get_contents( $tmpPath )
            )
            ->setIsCompress( $isCompress )
            ->setIsWriteWaterMark( $isWriteWaterMark )
            ->setWidth( $width )
            ->setHeight( $height )
            ->setAppendImage( $appendImage )
            ->setForceType( $forceImageType )
            ->upload();

        //图片类型错误
        if( !defined( 'IN_TEST' ) && !$uploadResult['path'] )
        {
            $this->throwException( $key , $isThrowException , $errorCode );

            return $this->_getDefaultImageParameters( $defaultPath );
        }

        return array(
            'path' => $uploadResult['path'] ,
            'width' => $uploadResult['width'] ,
            'height' => $uploadResult['height'] ,
            'binary' => $binaryContent ,
            'tmpPath' => $tmpPath ,
        );
    }
}
