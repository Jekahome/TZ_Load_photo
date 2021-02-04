<?php
namespace App\Model;

class Model{
    private $files;

    function __construct($files)
    {
        $this->files = json_decode($files,1);

        $this->load($this->files);
    }


    static function clear($pictures=null){


           if(is_null($pictures)){
               $pictures = array_flip(array_diff(scandir(DIR_PICTURE), array('..', '.')));
           }

           foreach ($pictures as $f=>$v){
               @unlink(DIR_PICTURE.$f);
           }
    }


   private function load($files=[]) {
        try{

            $pictures = array_flip(array_diff(scandir(DIR_PICTURE), array('..', '.')));

            $arr=[];

            $files = array_unique($files);//убрать дубли значений

            foreach ($files as $url ){

                $hash = hash("md5",$url).".jpg";

                if( !isset($pictures[$hash]) ){

                    $file = file_get_contents($url);
                    if($file){

                        $img = DIR_PICTURE.$hash;
                        file_put_contents($img, $file);
                        //chmod($img,0777);

                        if( $this->watermark($img) && $this->resize($img ,false,200) ){
                            $arr[$url]='/images/'.$hash;
                        }

                    }
                }

            }

            echo json_encode($arr) ;
            exit;

        }catch (Throwable $e){
            echo json_encode([]) ; exit;
        }
    }


    private function resize($image) {
        try{


            list($w_i, $h_i, $type) = getimagesize($image);
            $types = array("", "gif", "jpeg", "png");
            $ext = $types[$type];
            if ($ext) {
                $func = 'imagecreatefrom'.$ext;
                $img_i = $func($image);
            } else {
                echo 'Некорректное изображение';
                return false;
            }

            $w_o = HEIGHT_PICTURE / ($h_i / $w_i);

            $img_o = imagecreatetruecolor($w_o, HEIGHT_PICTURE);

            imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, HEIGHT_PICTURE, $w_i, $h_i);
            $func = 'image'.$ext;
            $func($img_o, $image);
            return true;
        }
        catch (\Throwable $e){
            return false;
        }
        catch (\Error $e){
            return false;
        }

    }

    private function watermark($path){
        try{
            $stamp = imagecreatefrompng(PATH_WATERMARK);
            $im = imagecreatefromjpeg($path);

            $marge_right = 3;
            $marge_bottom = 5;

            $watermark_width = imagesx($stamp);
            $watermark_height = imagesy($stamp);

            imagecopy($im, $stamp,
                imagesx($im) - $watermark_width - $marge_right,
                imagesy($im) - $watermark_height - $marge_bottom,
                0, 0,
                $watermark_width, $watermark_height);
            imagejpeg($im,$path,75);

            imagedestroy($im);
            return true;
        }
        catch (\Throwable $e){
            return false;
        }
        catch (\Error $e){
            return false;
        }
    }



}

