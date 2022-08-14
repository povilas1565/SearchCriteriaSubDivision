<?php

class ControllerExtensionModuleOffersProduct extends Controller
{
    public function openCatalogProductBefore(&$route, &$data, &$output)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller openCatalogProductBefore');
            }

            $data = $this->loadLanguage__openCatalogProductBefore($data);
            $data = $this->loadData__openCatalogProductBefore($data);

        }
    }

    private function loadLanguage__openCatalogProductBefore($data)
    {
        $this->load->language('extension/module/offers_product');
        $data['label_select_offers_product'] = $this->language->get('label_select_offers_product');
        return $data;
    }

    private function loadData__openCatalogProductBefore($data)
    {
        $this->load->model('extension/module/offers_product');
        if (isset($data['product_id'])) {
            $master_product = $this->model_extension_module_offers_product->getProductMasterField($data['product_id']);
            $result = $this->model_extension_module_offers_product->getOffersProduct($data['product_id'], $master_product);
            $data['offers_product'] = [];

            foreach ($result as $product_id) {

                if ($product_id != $data['product_id']) {
                    $product_info = $this->model_catalog_product->getProduct($product_id);
                    if (isset($this->request->get['path'])) {
                        $path = $this->request->get['path'];
                    } else {
                        $path = '';
                    }
                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    $data['offers_product'][] = [
                        'short_name' => $product_info['meta_h1'],
                        'href'       => $this->url->link('product/product', 'path=' . $path  . '&product_id=' . $product_info['product_id']),
                        'price'      => $price,
                    ];
                }

            }
            $size = count($data['offers_product']);

            for ($i = 0; $i < $size; $i++){
                $min = $i;
                for ($j = $i + 1; $j < $size; $j++){
                    $p1 = $data['offers_product'][$j]['price'];
                    $pMin = $data['offers_product'][$min]['price'];
                    if ($p1 < $pMin){
                        $min = $j;
                    }
                }
                $temp = $data['offers_product'][$i];
                $data['offers_product'][$i] = $data['offers_product'][$min];
                $data['offers_product'][$min] = $temp;
            }

            foreach ($data['offers_product'] as $key=>$offers_product) {
                $data['offers_product'][$key]['price'] = $this->currency->format($offers_product['price'], $this->session->data['currency']);
            }


        }
        return $data;
    }
}
