<?php
class ImageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function uploadAction()
    {
        if (isset($_FILES['file'])) {
            $tiposPermitidos = array("image/jpg", "image/jpeg", "image/png", "image/JPEG", "image/PNG");
            if (in_array($_FILES['file']['type'], $tiposPermitidos)) {
                $random_prefix = date('YdHis') . floor(rand() % 1000);
                $random_suffix = floor(rand() % 1000) . date('siHdY');
                $fileExtension = explode('.', $_FILES['file']['name']);
                $fileExtension = array_pop($fileExtension);
                $image_path = IMAGE_UPLOAD_PATH . "{$_FILES['file']['size']}{$random_prefix}_{$random_suffix}.{$fileExtension}";
                $arquivo = fopen($_FILES['file']['tmp_name'], 'rb');
                if (!file_exists(IMAGE_UPLOAD_PATH)) {
                    mkdir(IMAGE_UPLOAD_PATH, 0774, true);
                }
                $nocrop = $this->getRequest()->getParam("nocrop");
                $img = new Imagick($_FILES['file']['tmp_name']);
                if ($nocrop) {
                    $replaced_path = $image_path;
                } else {
                    $replaced_path = str_replace(".jpg", ".png", $image_path);

                    $img->paintTransparentImage($img->getImageBackgroundColor(), 0, 1500);
                    $img->trimImage(20000);
                    $img->setImageFormat('png');
                }
                $img->writeImage($replaced_path);
                $return_data = Application_Model_Image::create('/' . $replaced_path);
                if ($this->getRequest()->getParam("returnpath")) {
                    $return_data = '/' . $replaced_path;
                }
                echo $return_data;
            }
        }
    }
}

