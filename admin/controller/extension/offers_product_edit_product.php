<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * @property $config
 * @property $log
 * @property $load
 * @property $model_extension_offers_product
 * @property $registry
 * @property $session
 * @property $model_catalog_product
 * @property $url
 * @property $language
 */
class ControllerExtensionOffersProductEditProduct extends Controller
{
//    Event action

    public function deleteProductAfter(&$route, &$data)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller deleteProductAfter');
            }
            $this->load->model('extension/offers_product');
            $this->model_extension_offers_product->deleteProductAfter($data[0], $this->config->get('offers_product_en_log'));
        }

    }

    public function addProductAfter(&$route, &$data, &$product_id)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller addProductAfter');
            }
            $this->load->model('extension/offers_product');
            $this->model_extension_offers_product->addProductAfter($data, $this->config->get('offers_product_en_log'), $product_id);
        }
    }

    public function editProductBefore(&$route, &$data)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller editProductBefore');
            }
            $this->load->model('extension/offers_product');
            $this->model_extension_offers_product->editProductBefore($data, $this->config->get('offers_product_en_log'));
        }
    }

    public function openProductBefore(&$route, &$data)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller openProductBefore');
            }
            $data = $this->loadLanguageForOpenProduct($data);
            $data = $this->loadMainDataForOpenProduct($data);
        }

    }

    public function openProductAfter(&$route, &$data, &$output)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller openProductAfter');
            }
            $template = new Template($this->registry->get('config')->get('template_type'));
            foreach ($data as $key => $value) {
                $template->set($key, $value);
            }

            $searchText = "id=\"tab-data\">";
            $outputAdd = $template->render('extension/module/offers_product_for_product_form_first.tpl');
            $output = str_replace($searchText, $searchText . $outputAdd, $output);


            $searchText = "<li><a href=\"#tab-links\"";
            $outputAdd = $template->render('extension/module/offers_product_for_product_form_nav.tpl');
            $output = str_replace($searchText, $outputAdd . $searchText, $output);

            $outputAdd = $template->render('extension/module/offers_product_for_product_form_slave_list.tpl');
            $searchText = "<div class=\"tab-pane\" id=\"tab-links\">";
            $output = str_replace($searchText, $outputAdd . $searchText, $output);
        }
    }

    public function openProductListBefore(&$route, &$data)
    {
        if ($this->config->get('offers_product_status')) {
            if ($this->config->get('offers_product_en_log')) {
                $this->log->write('controller openProductListBefore');
            }

            $data = $this->loadLanguageForOpenProductList($data);
            $data = $this->loadMainDataForOpenProductList($data);
        }
    }

    public function openProductListAfter(&$route, &$data, &$output)
    {

    }


//-------------------------------------------------------------------
    //Product Form
    private function loadMainDataForOpenProduct($data)
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/offers_product');
        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
            $data['product_id'] = $this->request->get['product_id'];
        }
        if (isset($this->request->post['master_product'])) {
            $data['master_product'] = $this->request->post['master_product'];
        } elseif (!empty($product_info)) {
            $data['master_product'] = $product_info['master_product'];
        } else {
            $data['master_product'] = 0;
        }
        if (isset($this->request->post['$cur_master_product_id'])) {
            $cur_master_product_id = $this->request->post['$cur_master_product_id'];
        } elseif (isset($this->request->get['product_id'])) {
            $cur_master_product_id = $this->model_extension_offers_product->getCurMasterProduct($this->request->get['product_id']);
        } else {
            $cur_master_product_id = '';
        }
        if (isset($cur_master_product_id) AND $cur_master_product_id != "") {
            $master_product_info = $this->model_catalog_product->getProduct($cur_master_product_id);

            if ($master_product_info) {
                $data['cur_master_product'] = [
                    'id'   => $master_product_info['product_id'],
                    'name' => $master_product_info['name'],
                ];
            }
        }
//        slve product start
        if (isset($this->request->post['slave_products'])) {
            $slave_products = $this->request->post['slave_products'];
        } elseif (isset($this->request->get['product_id'])) {
            $slave_products = $this->model_extension_offers_product->getSlaveProducts($this->request->get['product_id']);
        } else {
            $slave_products = [];
        }

        $data['slave_products'] = [];

        foreach ($slave_products as $slave_product_id) {
            $sec_product_info = $this->model_catalog_product->getProduct($slave_product_id);

            if ($sec_product_info) {
                $data['slave_products'][] = [
                    'product_id' => $sec_product_info['product_id'],
                    'name'       => $sec_product_info['name'],
                    'edit'       => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $sec_product_info['product_id'], true),
                ];
            }
        }
//        slve product end

        return $data;
    }

    private function loadLanguageForOpenProduct($data)
    {
        $this->load->language('extension/module/offers_product');
        $data['select_master_product_id_helper'] = $this->language->get('select_master_product_id_helper');
        $data['entry_master_product_helper'] = $this->language->get('entry_master_product_helper');
        $data['entry_master_product_filter'] = $this->language->get('entry_master_product_filter');
        $data['select_master_product_id'] = $this->language->get('select_master_product_id');
        $data['entry_master_product'] = $this->language->get('entry_master_product');
        $data['tab_slave_list'] = $this->language->get('tab_slave_list');
        $data['entry_product'] = $this->language->get('entry_product');
        return $data;
    }

    //Product List
    private function loadMainDataForOpenProductList($data)
    {
        if (isset($this->request->get['master_product_filter'])) {
            $master_product_filter = $this->request->get['master_product_filter'];
        } else {
            $master_product_filter = null;
        }

        return $data;
    }

    private function loadLanguageForOpenProductList($data)
    {
        $this->load->language('extension/module/offers_product');
        $data['entry_master_product_filter'] = $this->language->get('entry_master_product_filter');

        return $data;
    }

}
