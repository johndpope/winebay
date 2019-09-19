<?php
class MaintenanceController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function uploadAction() {
        if (isset($_FILES['file'])) {
            $tiposPermitidos = array("application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            if (in_array($_FILES['file']['type'], $tiposPermitidos)) {
                $inputFileType = 'Excel2007';
                $fileMeta = explode('.', $_FILES['file']['name']);
                if (array_pop($fileMeta)=="xls") $inputFileType = 'Excel5';
                $inputFileName = $_FILES['file']['tmp_name'];
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $spreadsheet = $objReader->load($inputFileName);
                $worksheet = $spreadsheet->setActiveSheetIndex(0);
                $highestRow = $worksheet->getHighestDataRow();
                $highestCol = $worksheet->getHighestDataColumn();
                $array = $worksheet->rangeToArray("A1:$highestCol$highestRow", null, true, false, false);
                $headings = array();
                foreach (array_shift($array) as $head) {
                    if($head!=NULL) {
                        switch (trim($head)) {
                            case 'IMPORTADORA':
                            $headings[] = 'importer';
                            break;
                            case 'VINHO':
                            $headings[] = 'name';
                            break;
                            case 'PRODUTOR':
                            $headings[] = 'productor';
                            break;
                            case 'PAIS':
                            $headings[] = 'country';
                            break;
                            case 'UVA':
                            $headings[] = 'grape';
                            break;
                            case 'TIPO':
                            $headings[] = 'type';
                            break;
                            case 'QUALIFICAÇÃO':
                            $headings[] = 'qualification';
                            break;
                            case 'REGIÕES':
                            $headings[] = 'region';
                            break;
                            case 'SAFRA':
                            $headings[] = 'crop';
                            break;
                            case 'GRADUAÇÃO':
                            $headings[] = 'graduation';
                            break;
                            case 'ML':
                            $headings[] = 'size';
                            break;
                            default:
                            $headings[] = $head;
                            break;
                        }
                    }
                }
                $productList = array();
                foreach($array as $i=>$line) {
                    $product = array();
                    foreach($headings as $h=>$head) {
                        $product[$head] = $line[$h];
                    }
                    $product['id'] = $i;
                    $productList[] = $product;
                }
                echo json_encode($productList);
            }
        }
    }

    public function importAction() {
        $maintenanceModel = new Application_Model_Maintenance();
        $list = $this->getRequest()->getPost("list");
        foreach ($list as $i=>$prod) {
            $newProduct = array(
               'name' => trim($prod['name']),
               'graduation' => floatval(str_replace(',','.',trim($prod['graduation']))),
               'size' => floatval(str_replace(',','.',trim($prod['size']))),
               'id_country' => NULL,
               'id_region' => NULL,
               'id_grape' => NULL,
               'id_productor' => NULL,
               'id_importer' => NULL,
               'id_tipicity' => NULL,
            );
            if (trim($prod['country'])!="") {
                $newProduct['id_country'] = $maintenanceModel->fetchOrCreate('country', $prod['country']);
            }
            if (trim($prod['region'])!="") {
                $newProduct['id_region'] = $maintenanceModel->fetchOrCreate('region', $prod['region']);
            }
            if (trim($prod['grape'])!="") {
                $newProduct['id_grape'] = $maintenanceModel->fetchOrCreate('grape', $prod['grape']);
            }
            if (trim($prod['productor'])!="") {
                $newProduct['id_productor'] = $maintenanceModel->fetchOrCreate('productor', $prod['productor']);
            }
            if (trim($prod['importer'])!="") {
                $newProduct['id_importer'] = $maintenanceModel->fetchOrCreate('importer', $prod['importer']);
            }
            if (trim($prod['type'])!="") {
                $newProduct['id_tipicity'] = $maintenanceModel->fetchOrCreate('tipicity', $prod['type']);
            }
            if (trim($prod['id_image_thumb'])!="") {
                $newProduct['id_image_thumb'] = $prod['id_image_thumb'];
            }
            $list[$i] = $newProduct;
        }
        $maintenanceModel->addProduct($list);
    }
}
