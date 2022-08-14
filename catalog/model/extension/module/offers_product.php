<?php

class ModelExtensionModuleOffersProduct extends Model
{
    public function getOffersProduct($product_id, $master)
    {
        if ($master) {
            return $this->getSlaveProducts($product_id);
        } else {
            $text = "SELECT DISTINCT * FROM " . DB_PREFIX . "offers_product WHERE slave_id = '" . (int)$product_id . "'";
            $query = $this->db->query($text);

            $master_id = 0;
            foreach ($query->rows as $result) {
                $master_id = $result['master_id'];
            }
            if ($master_id != 0) {
                $products_id = $this->getSlaveProducts($master_id);
                $products_id[] = $master_id;
                return $products_id;
            } else {
                return [];
            }
        }
    }

    public function getSlaveProducts($master_id)
    {
        $text = "SELECT DISTINCT * FROM " . DB_PREFIX . "offers_product WHERE master_id = '" . (int)$master_id . "'";
        $query = $this->db->query($text);
        $products_id = [];
        foreach ($query->rows as $result) {
            $products_id[] = $result['slave_id'];
        }
        return $products_id;
    }

    public function getProductMasterField($product_id)
    {
        $text = "SELECT DISTINCT master_product FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'";
        $query = $this->db->query($text);

        return $query->row['master_product'];

    }
}
