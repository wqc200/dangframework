<?php


/*
 * 使用方法
$uploader = new \Dang\Upload\Uploader($imageFile);
$uploader->setExtensions(array("jpg", 'gif'));
$uploader->setSize(1000000);
$uploader->setDir("/tmp");
$uploader->setName("image_name");
$result = $uploader->move();
$filename = $uploader->getFilename();
 * 
 */

namespace Dang\Util;

class Uploader 
{
    private $_extensions = array();
    private $_extension;
    private $_size;//定义的文件大小
    private $_sys;//接收文件属性
    private $_dir;
    private $_name;//自定义的文件名
 
    function __construct($imageFile){
        $this->_sys = $_FILES[$imageFile];
    }
    
    public function setDir($dir)
    {
        $this->_dir = $dir;
        
        return $this;
    }
    
    public function getDir()
    {
        return $this->_dir;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    //上传完成之后调用,获取完整文件名
    public function getFilename()
    {
        return $this->_dir."/".$this->_name.".".$this->getExtension();
    }
    
    public function setSize($size)
    {
        $this->_size = $size;
        
        return $this;
    }
    
    public function getSize()
    {
        return $this->_size;
    }
    
    public function setExtensions(array $extensions)
    {
        $this->_extensions = $extensions;
        
        return $this;
    }
    
    /**
     * 获取文件扩展名
     *
     * @param string $filename 文件类型
     * @return string 文件类型，没有找到返回：other
     */
    public function getExtension()
    {
        if(isset($this->_extension)){
            return $this->_extension;
        }
        
        //gif,jpg等下面的方法可用
        $ok = "";
        switch($this->_sys['type'])
        {
            case "image/x-png": $ok = "png";
                break;
            case "image/png": $ok = "png";
                break;
            case "application/pdf": $ok = "pdf";
                break;
            case "image/pjpeg": $ok = "jpg";
                break;
            case "image/jpeg": $ok = "jpg";
                break;
            case "image/jpg": $ok = "jpg";
                break;
            case "image/gif": $ok = "gif";
                break;
            default: $ok = "";
                break;
        }
        if($ok != ""){
            $this->_extension = $ok;
            return $this->_extension;
        }
         
        //png等使用下面的方法获取扩展名
        $filename = $this->_sys['tmp_name'];
        
        $typelist = array(
            array("FFD8FFE1","jpg"),
            array("89504E47","png"),
            array("47494638","gif"),
            array("49492A00","tif"),
            array("424D","bmp"),
            array("41433130","dwg"),
            array("38425053","psd"),
            array("7B5C727466","rtf"),
            array("3C3F786D6C","xml"),
            array("68746D6C3E","html"),
            array("44656C69766572792D646174","eml"),
            array("CFAD12FEC5FD746F","dbx"),
            array("2142444E","pst"),
            array("D0CF11E0","xls/doc"),
            array("5374616E64617264204A","mdb"),
            array("FF575043","wpd"),
            array("252150532D41646F6265","eps/ps"),
            array("255044462D312E","pdf"),
            array("E3828596","pwl"),
            array("504B0304","zip"),
            array("52617221","rar"),
            array("57415645","wav"),
            array("41564920","avi"),
            array("2E7261FD","ram"),
            array("2E524D46","rm"),
            array("000001BA","mpg"),
            array("000001B3","mpg"),
            array("6D6F6F76","mov"),
            array("3026B2758E66CF11","asf"),
            array("4D546864","mid")
        );
        if(!file_exists($filename)) throw new \Exception("no found file '$filename '!");
        $file = @fopen($filename,"rb");
        if(!$file) throw new \Exception("Read file($filename) refuse!");
        $bin = fread($file, 15); //只读15字节 各个不同文件类型，头信息不一样。
        fclose($file);
        foreach($typelist as $v)
        {
            $blen=strlen(pack("H*", $v[0])); //得到文件头标记字节数
            $tbin=substr($bin, 0, intval($blen)); ///需要比较文件头长度
            if(strtolower($v[0]) == strtolower(array_shift(unpack("H*", $tbin)))){
                $this->_extension = $v[1];
                return $this->_extension;
            }
        }
        
        $file = $this->_sys['tmp_name'];
        $ext = @pathinfo($file, PATHINFO_EXTENSION);
        if($ext){
            $this->_extension = $ext;
            return $this->_extension;
        }
        
        $filename = $this->_sys['name'];
        $ext = substr(strrchr($filename, '.'), 1);
        if($ext){
            $this->_extension = $ext;
            return $this->_extension;
        }
        
        $this->_extension = "";
        return $this->_extension;
    }

    public function move()
    {
        //检查大小
        $size = $this->_sys['size'];
        if($this->_size > 0 && $size > $this->_size){
            $result = array();
            $result['errorCode'] = "1";
            $result['errorMsg'] = "大小为".round($size/1024)."k, 充许上传的大小为".round($this->_size/1024)."k, 请稍微压缩一下在上传吧!";
            return $result;
        }

        //检查扩展名
        $extension = $this->getExtension();
        if(count($this->_extensions) > 0 && !in_array($extension, $this->_extensions)){
            $result = array();
            $result['errorCode'] = "2";
            $result['errorMsg'] = "扩展名为$extension, 不充许上传哈!";
            $result['imageExt'] = $extension;
            return $result;
        }

        $filename = $this->getFilename();
        if( !move_uploaded_file($this->_sys['tmp_name'], $filename) ){
            $result = array();
            $result['errorCode'] = "3";
            $result['errorMsg'] = "Upload image failed!";
            return $result;
        }
         
        $result = array();
        $result['errorCode'] = "0";
        $result['errorMsg'] = "Successed!";
        return $result;
    }

}
