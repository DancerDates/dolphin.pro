<?
function rotateImage($sourceFile,$destImageName,$degreeOfRotation)
{
  //function to rotate an image in PHP
  //developed by Roshan Bhattara (http://roshanbh.com.np)

  //get the detail of the image
  $imageinfo=getimagesize($sourceFile);
  switch($imageinfo['mime'])
  {
   //create the image according to the content type
   case "image/jpg":
   case "image/jpeg":
   case "image/pjpeg": //for IE
        $src_img=imagecreatefromjpeg("$sourceFile");
                break;
    case "image/gif":
        $src_img = imagecreatefromgif("$sourceFile");
                break;
    case "image/png":
        case "image/x-png": //for IE
        $src_img = imagecreatefrompng("$sourceFile");
                break;
  }
  //rotate the image according to the spcified degree
  $src_img = imagerotate($src_img, $degreeOfRotation, 0);
  //output the image to a file
  imagejpeg ($src_img,$destImageName);
}
?>