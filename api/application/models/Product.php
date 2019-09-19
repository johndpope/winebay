<?php
class Application_Model_Product extends Zend_Db_Table_Abstract
{

    protected $_name = "product";
    protected $_id = "id";

    public function getList()
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT pr.*, co.name AS country, re.name AS region, gr.name AS grape, pd.name AS productor, imp.name AS importer, ti.name AS tipicity, im1.path AS image_thumb
            FROM product pr
            LEFT JOIN country co ON pr.id_country=co.id
            LEFT JOIN region re ON pr.id_region=re.id
            LEFT JOIN grape gr ON pr.id_grape=gr.id
            LEFT JOIN productor pd ON pr.id_productor=pd.id
            LEFT JOIN importer imp ON pr.id_importer=imp.id
            LEFT JOIN tipicity ti ON pr.id_tipicity=ti.id
            LEFT JOIN image im1 ON pr.id_image_thumb=im1.id
            ORDER BY pr.name");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getWinehouseList($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT wp.*, pr.name, (CASE WHEN COUNT(sa.id) > 0 THEN TRUE ELSE FALSE END) AS sale
            FROM winehouse_product wp LEFT JOIN sale sa ON wp.id = sa.id_winehouse_product, product pr
            WHERE wp.id_winehouse=$id AND wp.id_product=pr.id GROUP BY wp.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addToWinehouse($data)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->insert("winehouse_product", $data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function saveOnWinehouse($data, $id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->update("winehouse_product", $data, "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getFromWinehouse($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow(" SELECT wp.*, pr.name FROM winehouse_product wp, product pr WHERE wp.id_product=pr.id AND wp.id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function excludeFromWinehouse($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->delete("winehouse_product", "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getWinehouseExcludedList($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchAll("SELECT pr.id, pr.name, pr.graduation, pr.size, co.name as country, re.name as region, gr.name as grape, prd.name as productor, imp.name as importer, ti.name as tipicity, im1.path AS image_thumb
            FROM product pr
            LEFT JOIN winehouse_product wp ON wp.id_product = pr.id AND wp.id_winehouse = $id
            LEFT JOIN country co ON pr.id_country = co.id
            LEFT JOIN region re ON pr.id_region = re.id
            LEFT JOIN grape gr ON pr.id_grape = gr.id
            LEFT JOIN productor prd ON pr.id_productor = prd.id
            LEFT JOIN importer imp ON pr.id_importer = imp.id
            LEFT JOIN tipicity ti ON pr.id_tipicity = ti.id
            LEFT JOIN image im1 ON pr.id_image_thumb=im1.id
            WHERE wp.id_product IS NULL");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getById($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            return $connection->fetchRow("SELECT pr.*, co.name AS country, re.name AS region, gr.name AS grape, pd.name AS productor, imp.name AS importer, ti.name AS tipicity, im1.path AS image_thumb, wi.name AS winehouse_name, wi.city as winehouse_city, wi.region as winehouse_region
            FROM product pr
            LEFT JOIN country co ON pr.id_country=co.id
            LEFT JOIN region re ON pr.id_region=re.id
            LEFT JOIN grape gr ON pr.id_grape=gr.id
            LEFT JOIN productor pd ON pr.id_productor=pd.id
            LEFT JOIN importer imp ON pr.id_importer=imp.id
            LEFT JOIN tipicity ti ON pr.id_tipicity=ti.id
            LEFT JOIN image im1 ON pr.id_image_thumb=im1.id
            LEFT JOIN winehouse wi ON pr.id_winehouse_creation = wi.id
            WHERE pr.id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function create($data)
    {
        try {
            return $this->insert($data);
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($data, $id)
    {
        try {
            return $this->update($data, "id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function exclude($id)
    {
        try {
            return $this->delete("id=$id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getStoreList($query = false)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $connection->setFetchMode(Zend_Db::FETCH_ASSOC);
            $filter = "";
            if ($query) {
                $filter = "
                AND (
                    (pr.name LIKE '%$query%') OR
                    (pr.graduation LIKE '%$query%') OR
                    (pr.size LIKE '%$query%') OR
                    ((re.name LIKE '%$query%')) OR
                    ((gr.name LIKE '%$query%')) OR
                    ((ti.name LIKE '%$query%')) OR
                    ((co.name LIKE '%$query%')) OR
                    ((wi.name LIKE '%$query%'))
                )
                ";
            }
           
            return $connection->fetchAll("SELECT wp.id, pr.name, pr.size, im1.path AS image_thumb, wp.price, ti.name as tipicity_name, ti.id as tipicity_id, wi.id as winehouse_id, wi.name as winehouse_name, wi.city as winehouse_city, wi.region as winehouse_region, co.id as country_id, co.name as country_name, coim.path as country_icon, co.shortname as country_shortname, re.id as region_id, re.name as region_name, gr.id as grape_id, gr.name as grape_name
            FROM winehouse_product wp 
            LEFT JOIN winehouse wi ON wp.id_winehouse = wi.id, product pr
            LEFT JOIN image im1 ON pr.id_image_thumb=im1.id
            LEFT JOIN region re ON pr.id_region=re.id
            LEFT JOIN grape gr ON pr.id_grape=gr.id
            LEFT JOIN tipicity ti ON pr.id_tipicity=ti.id
            LEFT JOIN country co ON pr.id_country = co.id
            LEFT JOIN image coim ON co.id_image = coim.id
            WHERE wp.id_product=pr.id $filter GROUP BY wp.id ORDER BY pr.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getStoreListByWinehouse($id)
    {
        try {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $connection->setFetchMode(Zend_Db::FETCH_ASSOC);
            return $connection->fetchAll("SELECT wp.id, wp.quantity AS max_count, pr.name, pr.size, im1.path AS image_thumb, wp.price, ti.name as tipicity_name, ti.id as tipicity_id, wi.id as winehouse_id, wi.name as winehouse_name, wi.city as winehouse_city, wi.region as winehouse_region, co.id as country_id, co.name as country_name, coim.path as country_icon, co.shortname as country_shortname, re.id as region_id, re.name as region_name, gr.id as grape_id, gr.name as grape_name
            FROM winehouse_product wp LEFT JOIN winehouse wi ON wp.id_winehouse = wi.id, product pr
            LEFT JOIN image im1 ON pr.id_image_thumb=im1.id
            LEFT JOIN region re ON pr.id_region=re.id
            LEFT JOIN grape gr ON pr.id_grape=gr.id
            LEFT JOIN tipicity ti ON pr.id_tipicity=ti.id
            LEFT JOIN country co ON pr.id_country = co.id
            LEFT JOIN image coim ON co.id_image = coim.id
            WHERE wp.id_product=pr.id AND wp.id_winehouse = $id GROUP BY wp.id ORDER BY pr.id");
        } catch (Zend_Db_Table_Exception $e) {
            echo $e->getMessage();
        }
    }
}
