<?php
class ProductController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function listAction() {
        $productModel = new Application_Model_Product();
        $list = $productModel->getList();
        foreach ($list as $i=>$product) {
            $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($product->name))));
            $list[$i]->country = utf8_encode(ucwords(strtolower(stripslashes($product->country))));
            $list[$i]->region = utf8_encode(ucwords(strtolower(stripslashes($product->region))));
            $list[$i]->grape = utf8_encode(ucwords(strtolower(stripslashes($product->grape))));
            $list[$i]->productor = utf8_encode(ucwords(strtolower(stripslashes($product->productor))));
            $list[$i]->importer = utf8_encode(ucwords(strtolower(stripslashes($product->importer))));
            $list[$i]->tipicity = utf8_encode(ucwords(strtolower(stripslashes($product->tipicity))));
            $list[$i]->image_thumb = implode('/', array_map('urlencode', explode('/', $product->image_thumb)));
        }
        echo json_encode($list);
    }

    public function winehouselistAction() {
        $productModel = new Application_Model_Product();
        if ($this->_hasParam("id")) {
            $id = $this->getRequest()->getParam("id");
            $list = $productModel->getWinehouseList($id);
            foreach ($list as $i=>$product) {
                $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($product->name))));
            }
            echo json_encode($list);
        } elseif ($this->_hasParam("notid")) {
            $id = $this->getRequest()->getParam("notid");
            $list = $productModel->getWinehouseExcludedList($id);
            foreach ($list as $i=>$product) {
                $list[$i]->name = utf8_encode(ucwords(strtolower(stripslashes($product->name))));
                $list[$i]->country = utf8_encode(ucwords(strtolower(stripslashes($product->country))));
                $list[$i]->region = utf8_encode(ucwords(strtolower(stripslashes($product->region))));
                $list[$i]->grape = utf8_encode(ucwords(strtolower(stripslashes($product->grape))));
                $list[$i]->productor = utf8_encode(ucwords(strtolower(stripslashes($product->productor))));
                $list[$i]->importer = utf8_encode(ucwords(strtolower(stripslashes($product->importer))));
                $list[$i]->image_thumb = utf8_encode($product->image_thumb);
                $list[$i]->tipicity = utf8_encode(ucwords(strtolower(stripslashes($product->tipicity))));
            }
            echo json_encode($list);
        }
    }

    public function getAction() {
        $productModel = new Application_Model_Product();
        $id = $this->getRequest()->getParam("id");
        $product = $productModel->getById($id);
        $product->name = utf8_encode(ucwords(strtolower(stripslashes($product->name))));
        $product->country = utf8_encode(ucwords(strtolower(stripslashes($product->country))));
        $product->region = utf8_encode(ucwords(strtolower(stripslashes($product->region))));
        $product->grape = utf8_encode(ucwords(strtolower(stripslashes($product->grape))));
        $product->productor = utf8_encode(ucwords(strtolower(stripslashes($product->productor))));
        $product->importer = utf8_encode(ucwords(strtolower(stripslashes($product->importer))));
        $product->tipicity = utf8_encode(ucwords(strtolower(stripslashes($product->tipicity))));
        $product->winehouse_name = utf8_encode(ucwords(strtolower(stripslashes($product->winehouse_name))));
        echo json_encode($product);
    }

    public function deleteAction() {
        $productModel = new Application_Model_Product();
        $id = $this->getRequest()->getParam("id");
        echo $productModel->exclude($id);
    }

    public function createAction() {
        $productModel = new Application_Model_Product();
        $name = trim($this->getRequest()->getPost("name"));
        $graduation = trim($this->getRequest()->getPost("graduation"));
        $size = trim($this->getRequest()->getPost("size"));
        $id_country = trim($this->getRequest()->getPost("id_country"));
        if ($id_country == "new") {
            $id_country = Application_Model_Country::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_country"))))));
        }
        $id_region = trim($this->getRequest()->getPost("id_region"));
        if ($id_region == "new") {
            $id_region = Application_Model_Region::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_region"))))), $id_country);
        }
        $id_grape = trim($this->getRequest()->getPost("id_grape"));
        if ($id_grape == "new") {
            $id_grape = Application_Model_Grape::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_grape"))))));
        }
        $id_productor = trim($this->getRequest()->getPost("id_productor"));
        if ($id_productor == "new") {
            $id_productor = Application_Model_Productor::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_productor"))))));
        }
        $id_importer = trim($this->getRequest()->getPost("id_importer"));
        if ($id_importer == "new") {
            $id_importer = Application_Model_Importer::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_importer"))))));
        }
        $id_tipicity = trim($this->getRequest()->getPost("id_tipicity"));
        if ($id_tipicity == "new") {
            $id_tipicity = Application_Model_Tipicity::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_tipicity"))))));
        }
        $id_image_thumb = trim($this->getRequest()->getPost("id_image_thumb"));
        $id_winehouse_creation = $this->getRequest()->getPost("id_winehouse_creation");
        // $id_image_banner = trim($this->getRequest()->getPost("id_image_banner"));
        // $id_image_featured = trim($this->getRequest()->getPost("id_image_featured"));
        if ($name!="") {
            if (!is_numeric($id_country)) $id_country = null;
            if (!is_numeric($id_region)) $id_region = null;
            if (!is_numeric($graduation)) $graduation = null;
            if (!is_numeric($size)) $size = null;
            if (!is_numeric($id_country)) $id_country = null;
            if (!is_numeric($id_region)) $id_region = null;
            if (!is_numeric($id_grape)) $id_grape = null;
            if (!is_numeric($id_productor)) $id_productor = null;
            if (!is_numeric($id_importer)) $id_importer = null;
            if (!is_numeric($id_tipicity)) $id_tipicity = null;
            if (!is_numeric($id_image_thumb)) $id_image_thumb = null;
            if (!is_numeric($id_winehouse_creation)) $id_winehouse_creation = null;
            // if (!is_numeric($id_image_banner)) $id_image_banner = null;
            // if (!is_numeric($id_image_featured)) $id_image_featured = null;
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'graduation' => $graduation,
                'size' => $size,
                'id_country' => $id_country,
                'id_region' => $id_region,
                'id_grape' => $id_grape,
                'id_productor' => $id_productor,
                'id_importer' => $id_importer,
                'id_tipicity' => $id_tipicity,
                'id_image_thumb' => $id_image_thumb,
                'id_winehouse_creation' => $id_winehouse_creation
                // 'id_image_banner' => $id_image_banner,
                // 'id_image_featured' => $id_image_featured
            );
            echo $productModel->create($data);
        } else {
            echo true;
        }
    }

    public function saveAction() {
        $productModel = new Application_Model_Product();
        $id = $this->getRequest()->getParam("id");
        $name = trim($this->getRequest()->getPost("name"));
        $graduation = trim($this->getRequest()->getPost("graduation"));
        $size = trim($this->getRequest()->getPost("size"));
        $id_country = trim($this->getRequest()->getPost("id_country"));
        if ($id_country == "new") {
            $id_country = Application_Model_Country::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_country"))))));
        }
        $id_region = trim($this->getRequest()->getPost("id_region"));
        if ($id_region == "new") {
            $id_region = Application_Model_Region::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_region"))))), $id_country);
        }
        $id_grape = trim($this->getRequest()->getPost("id_grape"));
        if ($id_grape == "new") {
            $id_grape = Application_Model_Grape::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_grape"))))));
        }
        $id_productor = trim($this->getRequest()->getPost("id_productor"));
        if ($id_productor == "new") {
            $id_productor = Application_Model_Productor::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_productor"))))));
        }
        $id_importer = trim($this->getRequest()->getPost("id_importer"));
        if ($id_importer == "new") {
            $id_importer = Application_Model_Importer::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_importer"))))));
        }
        $id_tipicity = trim($this->getRequest()->getPost("id_tipicity"));
        if ($id_tipicity == "new") {
            $id_tipicity = Application_Model_Tipicity::fetchOrCreate(ucwords(strtolower(strip_tags(addslashes($this->getRequest()->getPost("new_tipicity"))))));
        }
        $id_image_thumb = trim($this->getRequest()->getPost("id_image_thumb"));
        // $id_image_banner = trim($this->getRequest()->getPost("id_image_banner"));
        // $id_image_featured = trim($this->getRequest()->getPost("id_image_featured"));
        if ($name!="") {
            if (!is_numeric($id_country)) $id_country = null;
            if (!is_numeric($id_region)) $id_region = null;
            if (!is_numeric($id_grape)) $id_grape = null;
            if (!is_numeric($id_productor)) $id_productor = null;
            if (!is_numeric($id_importer)) $id_importer = null;
            if (!is_numeric($id_tipicity)) $id_tipicity = null;
            if (!is_numeric($id_image_thumb)) $id_image_thumb = null;
            // if (!is_numeric($id_image_banner)) $id_image_banner = null;
            // if (!is_numeric($id_image_featured)) $id_image_featured = null;
            $data = array(
                'name' => utf8_decode(strip_tags(addslashes($name))),
                'graduation' => $graduation,
                'size' => $size,
                'id_country' => $id_country,
                'id_region' => $id_region,
                'id_grape' => $id_grape,
                'id_productor' => $id_productor,
                'id_importer' => $id_importer,
                'id_tipicity' => $id_tipicity,
                'id_image_thumb' => $id_image_thumb,
                // 'id_image_banner' => $id_image_banner,
                // 'id_image_featured' => $id_image_featured
            );
            echo $productModel->save($data, $id);
        } else {
            echo true;
        }
    }
}
