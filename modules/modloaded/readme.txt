1. create modloaded directory inside modules directory if does not exists

2. upload photo_rotator folder from the zip archive to the modloaded directory

3. Go to admin->tools->modules then look for the photo rotator then hit install

4. on modules/boonex/photos/classes/BxPhotosSearch.php

//after


 function getImgUrl ($sHash, $sImgType = 'browse')
    {
        return BX_DOL_URL_ROOT . $this->oModule->_oConfig->getBaseUri() . 'get_image/' . $sImgType .'/' . $sHash . '.jpg';
    }

//insert
	function serviceGetImgUrl($sHash,$sImgType){
        return $this->getImgUrl($sHash,$sImgType);
    }

DONE!