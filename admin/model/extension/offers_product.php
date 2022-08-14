<?php

/**
 * @property $db
 * @property $load
 * @property $model_extension_event
 */
class ModelExtensionOffersProduct extends Model
{
    private $enable_log = false;

    public function install()
    {
        $this->enable_log = true;
        $this->writeLog('Install module offers product start...');
        $text = "CREATE TABLE `" . DB_PREFIX . "offers_product` ( `master_id` INT(11) NULL , `slave_id` INT(11) NULL ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'offers product'";
        $this->db->query($text);
        $this->writeLog('— ' . $text);
        $text = "ALTER TABLE `" . DB_PREFIX . "offers_product` ADD PRIMARY KEY( `master_id`, `slave_id`)
create table Product
(
    master_id int null,
    slave_id  int null
);

create table Product
(
    master_id int null,
    slave_id  int null
);

create table `%nameoffers_product`
(
    master_id int null,
    slave_id  int null
);

";
        $this->db->query($text);
        $this->writeLog('— ' . $text);
        $text = "ALTER TABLE `" . DB_PREFIX . "product` ADD `master_product` TINYINT(1) NOT NULL DEFAULT '0' ";
        $this->db->query($text);
        $this->writeLog('— ' . $text);

        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('ofp_1', 'admin/model/catalog/product/editProduct/before', 'extension/offers_product_edit_product/editProductBefore');
        $this->model_extension_event->addEvent('ofp_2', 'admin/view/catalog/product_form/after', 'extension/offers_product_edit_product/openProductAfter');
        $this->model_extension_event->addEvent('ofp_3', 'admin/view/catalog/product_form/before', 'extension/offers_product_edit_product/openProductBefore');
        $this->model_extension_event->addEvent('ofp_4', 'admin/model/catalog/product/addProduct/after', 'extension/offers_product_edit_product/addProductAfter');
        $this->model_extension_event->addEvent('ofp_5', 'admin/model/catalog/product/deleteProduct/after', 'extension/offers_product_edit_product/deleteProductAfter');

        $this->model_extension_event->addEvent('ofp_6', 'admin/view/catalog/product_list/after', 'extension/offers_product_edit_product/openProductListAfter');
        $this->model_extension_event->addEvent('ofp_7', 'admin/view/catalog/product_list/before', 'extension/offers_product_edit_product/openProductListBefore');

        $this->model_extension_event->addEvent('ofp_8', 'catalog/view/product/product/before', 'extension/module/offers_product/openCatalogProductBefore');


        for ($i = 1; $i <= 8; $i++) {
            $this->writeLog("— addEvent('offers product before edit product : ofp_" . $i . "')");
        }
        $this->writeLog('END');
    }

    /**
     *
     */
    public function uninstall()
    {
        $this->enable_log = true;
        $this->writeLog('Uninstall module offers product start...');
        $text = "DROP TABLE `" . DB_PREFIX . "offers_product`";
        $this->db->query($text);
        $this->writeLog('— ' . $text);
        $text = "ALTER TABLE `" . DB_PREFIX . "product` DROP `master_product`";
        $this->db->query($text);
        $this->writeLog('— ' . $text);

        $this->load->model('extension/event');
        for ($i = 1; $i <= 8; $i++) {
            $this->model_extension_event->deleteEvent('ofp_' . $i);
            $this->writeLog("— deleteEvent('offers product before edit product ofp_" . $i . "')");
        }

        $this->writeLog('END');
    }

    private function writeLog($text)
    {
        if ($this->enable_log)
            $this->log->write($text);
    }

    public function editProductBefore($data, $enable_log)
    {

        $this->enable_log = $enable_log;
        $this->writeLog('model editProductBefore');
        if (isset($data)) {
            $product_id = $data[0];
            if (isset($data[1]['master_product'])) {
                $master_product = (int)$data[1]['master_product'];
            } else {
                $master_product = 0;
            }
            $text = "UPDATE `" . DB_PREFIX . "product` SET `master_product`=" . $master_product . " WHERE product_id=" . $product_id;
            $this->db->query($text);
            $this->writeLog($text);
            if ($master_product) {
                if (isset($data[1]['slave_products'])) {
                    $this->updateMasterOffersProductTables($data[1]['slave_products'], $product_id);
                } else {
                    $this->cleanOffersProductTables($product_id);
                }
            } else {
                if (isset($data[1]['cur_master_product_id'])) {
                    $this->updateSlaveOffersProductTables($data[1]['cur_master_product_id'], $product_id);
                } else {
                    $this->cleanOffersProductTables($product_id);
                }
            }

        }
    }

    private function cleanOffersProductTables($product_id)
    {
        $text = "DELETE FROM `" . DB_PREFIX . "offers_product` WHERE `master_id` = " . $product_id . " OR `slave_id` = " . $product_id;
        $this->db->query($text);
        $this->writeLog($text);
    }

    private function updateSlaveOffersProductTables($cur_master_product_id, $product_id)
    {
        $this->cleanOffersProductTables($product_id);

        $text = "INSERT INTO `" . DB_PREFIX . "offers_product`(master_id,slave_id) VALUES (" . (int)$cur_master_product_id . "," . (int)$product_id . ")";
        $this->writeLog($text);
        $this->db->query($text);
        $this->writeLog('OK');
    }

    private function updateMasterOffersProductTables($slave_products, $master_id)
    {
        $this->cleanOffersProductTables($master_id);
        $has_error = false;
        if (count($slave_products) > 0) {
            $text = "INSERT INTO `" . DB_PREFIX . "offers_product`(master_id,slave_id) VALUES ";

            foreach ($slave_products as $i => $slave_product_id) {
                if ($slave_product_id != $master_id) {
                    $text .= "(" . (int)$master_id . "," . (int)$slave_product_id . "),";
                    $this->writeLog($i);
                }else{
                    $has_error = true;
                }
            }
            if ($has_error){
                if (count($slave_products)-1>0) {
                    $text = ($text{strlen($text) - 1} == ',') ? $text = substr($text, 0, -1) : $text;
                    $this->writeLog($text);
                    $this->db->query($text);
                    $this->writeLog('OK');
                }
            }else{
                $text = ($text{strlen($text) - 1} == ',') ? $text = substr($text, 0, -1) : $text;
                $this->writeLog($text);
                $this->db->query($text);
                $this->writeLog('OK');
            }

        }
    }

    public function addProductAfter($data, $enable_log, $product_id)
    {
        $this->enable_log = $enable_log;
        $this->writeLog('model addProductAfter');
        if (isset($data)) {
            if (isset($data[0]['master_product'])) {
                $master_product = (int)$data[0]['master_product'];
            } else {
                $master_product = 0;
            }
            $text = "UPDATE `" . DB_PREFIX . "product` SET `master_product`=" . $master_product . " WHERE product_id=" . $product_id;
            $this->db->query($text);
            $this->writeLog($text);
            if ($master_product) {
                if (isset($data[0]['slave_products'])) {
                    $this->updateMasterOffersProductTables($data[0]['slave_products'], $product_id);
                } else {
                    $this->cleanOffersProductTables($product_id);
                }
            } else {
                if (isset($data[0]['cur_master_product_id'])) {
                    $this->updateSlaveOffersProductTables($data[0]['cur_master_product_id'], $product_id);
                } else {
                    $this->cleanOffersProductTables($product_id);
                }
            }
        }
    }

    public function deleteProductAfter($product_id, $enable_log)
    {
        $this->enable_log = $enable_log;
        $this->writeLog('model deleteProductAfter');
        $text = "DELETE FROM `" . DB_PREFIX . "offers_product` WHERE `master_id` = " . $product_id . " OR `slave_id` = " . $product_id;
        $this->db->query($text);
        $this->writeLog($text);
    }

    public function getCurMasterProduct($product_id)
    {
        $text = "SELECT * FROM " . DB_PREFIX . "offers_product WHERE slave_id = '" . (int)$product_id . "'";
        $master_id = "";

        $query = $this->db->query($text);

        foreach ($query->rows as $result) {
            $master_id = $result['master_id'];
        }

        return $master_id;
    }

    public function getSlaveProducts($product_id = 0)
    {
        $slave_products = [];

        if ($product_id != 0) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_product WHERE master_id = '" . (int)$product_id . "'");
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_product WHERE 1");
        }

        foreach ($query->rows as $result) {
            $slave_products[] = $result['slave_id'];
        }

        return $slave_products;
    }


//    Add filter master field

    public function getProducts($data = [])
    {

        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

        if (!empty($data['filter_category'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
        }

        $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        /*Auth: VarIzo Task: {offers product} Date:03.04.2017 !Start!*/
        /*Comment: filter for master_product field*/
        if (isset($data['master_product']) && !is_null($data['master_product'])) {

            if ((int)$data['master_product'] == 1) {
                $sql .= " AND p.master_product = '" . (int)$data['master_product'] . "'";
            } else {
                $all_slave_products = $this->getSlaveProducts();
                $sql .= " AND p.master_product = '" . (int)$data['master_product'] . "'";
                if (count($all_slave_products)>0){
                    $sql .= " AND p.product_id NOT IN (" . implode(",", $all_slave_products) . ")";
                }
                $this->log->write($sql);
            }
        }
        /*!End!*/

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
            if ($data['filter_image'] == 1) {
                $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
            } else {
                $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
            }
        }

        if (!empty($data['filter_category'])) {
            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category'] . "'";
        }
        $sql .= " GROUP BY p.product_id";

        $sort_data = [
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order',
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

}