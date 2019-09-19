<?php
class StoreController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getproductsAction()
    {
        $productModel = new Application_Model_Product();
        $list = $productModel->getStoreList();
        foreach ($list as $i => $product) {
            $list[$i] = array(
                "id" => $product["id"],
                "winehouse_id" => $product['winehouse_id'],
                "url" => "#!/winehouse/{$product['winehouse_id']}/produto/{$product['id']}",
                "name" => utf8_encode(stripslashes($product['name'])),
                "size" => $product['size'],
                "image" => $product['image_thumb'],
                "featured" => false,
                "bestchoice" => true,
                "bestseller" => true,
                "isnew" => true,
                "price" => $product['price'],
                "tipicity" => array(
                    "value" => $product['tipicity_id'],
                    "name" => utf8_encode(stripslashes($product['tipicity_name'])),
                ),
                "winehouse" => array(
                    "value" => $product['winehouse_id'],
                    "name" => utf8_encode(stripslashes($product['winehouse_city'] . " - " . $product['winehouse_region'])),
                ),
                "region" => array(
                    "value" => $product['region_id'],
                    "name" => utf8_encode(stripslashes($product['region_name'])),
                ),
                "grape" => array(
                    "value" => $product['grape_id'],
                    "name" => utf8_encode(stripslashes($product['grape_name'])),
                ),
                "country" => array(
                    "value" => $product['country_id'],
                    "name" => utf8_encode(stripslashes($product['country_name'])),
                    "icon" => $product['country_icon'],
                    "shortname" => utf8_encode(stripslashes($product['country_shortname'])),
                ),
            );
        }
        echo json_encode($list);
    }

    public function searchAction()
    {
        $productModel = new Application_Model_Product();
        $term = strip_tags(addslashes(trim($this->getRequest()->getParam("term"))));
        $list = $productModel->getStoreList($term);
        foreach ($list as $i => $product) {
            $list[$i] = array(
                "id" => $product["id"],
                "winehouse_id" => $product['winehouse_id'],
                "url" => "#!/winehouse/{$product['winehouse_id']}/produto/{$product['id']}",
                "name" => utf8_encode(stripslashes($product['name'])),
                "size" => $product['size'],
                "image" => $product['image_thumb'],
                "featured" => false,
                "bestchoice" => true,
                "bestseller" => true,
                "isnew" => true,
                "price" => $product['price'],
                "tipicity" => array(
                    "value" => $product['tipicity_id'],
                    "name" => utf8_encode(stripslashes($product['tipicity_name'])),
                ),
                "winehouse" => array(
                    "value" => $product['winehouse_id'],
                    "name" => utf8_encode(stripslashes($product['winehouse_city'] . " - " . $product['winehouse_region'])),
                ),
                "region" => array(
                    "value" => $product['region_id'],
                    "name" => utf8_encode(stripslashes($product['region_name'])),
                ),
                "grape" => array(
                    "value" => $product['grape_id'],
                    "name" => utf8_encode(stripslashes($product['grape_name'])),
                ),
                "country" => array(
                    "value" => $product['country_id'],
                    "name" => utf8_encode(stripslashes($product['country_name'])),
                    "icon" => $product['country_icon'],
                    "shortname" => utf8_encode(stripslashes($product['country_shortname'])),
                ),
            );
        }
        echo json_encode($list);
    }

    public function getproductsbywinehouseAction()
    {
        if ($this->_hasParam("id")) {
            $productModel = new Application_Model_Product();
            $list = $productModel->getStoreListByWinehouse($this->getRequest()->getParam("id"));
            foreach ($list as $i => $product) {
                $list[$i] = array(
                    "id" => $product['id'],
                    "winehouse_id" => $product['winehouse_id'],
                    "url" => "#!/winehouse/{$product['winehouse_id']}",
                    "name" => utf8_encode(stripslashes($product['name'])),
                    "size" => $product['size'],
                    "image" => $product['image_thumb'],
                    "featured" => false,
                    "bestchoice" => true,
                    "bestseller" => true,
                    "quant" => 1,
                    "isnew" => true,
                    "price" => $product['price'],
                    "max_count" => $product['max_count'],
                    "tipicity" => array(
                        "value" => $product['tipicity_id'],
                        "name" => utf8_encode(stripslashes($product['tipicity_name'])),
                    ),
                    "winehouse" => array(
                        "value" => $product['winehouse_id'],
                        "name" => utf8_encode(stripslashes($product['winehouse_city'] . " - " . $product['winehouse_region'])),
                    ),
                    "region" => array(
                        "value" => $product['region_id'],
                        "name" => utf8_encode(stripslashes($product['region_name'])),
                    ),
                    "grape" => array(
                        "value" => $product['grape_id'],
                        "name" => utf8_encode(stripslashes($product['grape_name'])),
                    ),
                    "country" => array(
                        "value" => $product['country_id'],
                        "name" => utf8_encode(stripslashes($product['country_name'])),
                        "icon" => $product['country_icon'],
                        "shortname" => utf8_encode(stripslashes($product['country_shortname'])),
                    ),
                );
            }
            echo json_encode($list);
        }
    }
}
