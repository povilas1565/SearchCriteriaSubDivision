<?php /** @noinspection ALL */

/** @noinspection PhpUndefinedClassInspection */

class ControllerExtensionModuleOffersProduct extends Controller
{
private $error = [];

public function index()
{
$this->load->language('extension/module/offers_product');

$this->document->setTitle($this->language->get('heading_title'));

$this->load->model('setting/setting');

if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
if (isset($this->request->post['offers_product_prefix_attr'])) {
$this->request->post['offers_product_prefix_attr'] = json_encode($this->request->post['offers_product_prefix_attr']);
}
$this->model_setting_setting->editSetting('offers_product', $this->request->post);

$this->session->data['success'] = $this->language->get('text_success');

if (isset($this->request->post['apply']) and $this->request->post['apply']) {
$this->response->redirect($this->url->link('extension/module/offers_product', 'token=' . $this->session->data['token'], true));
} else {
$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
}
}

$data = $this->loadLanguage();
$data = $this->loadError($data);
$data = $this->loadBreadcrumbs($data);
$data = $this->loadMainData($data);
$data = $this->loadAction($data);
$this->loadTemplate($data);
}

protected function validate()
{
if (!$this->user->hasPermission('modify', 'extension/module/offers_product')) {
$this->error['warning'] = $this->language->get('error_permission');
}

return !$this->error;
}


public function install()
{
if ($this->user->hasPermission('modify', 'extension/extension')) {
$this->load->model('extension/offers_product');
$this->model_extension_offers_product->install();
}
}

public function uninstall()
{
$this->load->model('extension/offers_product');
$this->model_extension_offers_product->uninstall();
}


private function loadLanguage()
{
$data = [];
$data['heading_title'] = $this->language->get('heading_title');

$data['text_disabled'] = $this->language->get('text_disabled');
$data['text_enabled'] = $this->language->get('text_enabled');
$data['text_edit'] = $this->language->get('text_edit');

$data['entry_status'] = $this->language->get('entry_status');
$data['entry_log'] = $this->language->get('entry_log');

$data['button_save'] = $this->language->get('button_save');
$data['button_apply'] = $this->language->get('button_apply');
$data['button_cancel'] = $this->language->get('button_cancel');

$data['label_attr'] = $this->language->get('label_attr');
$data['entry_prefix_attr'] = $this->language->get('entry_prefix_attr');
$data['entry_view_only_master'] = $this->language->get('entry_view_only_master');

return $data;
}

private function loadError($data)
{
if (isset($this->error['warning'])) {
$data['error_warning'] = $this->error['warning'];
} else {
$data['error_warning'] = '';
}
return $data;
}

private function loadBreadcrumbs($data)
{
$data['breadcrumbs'] = [];

$data['breadcrumbs'][] = [
'text' => $this->language->get('text_home'),
'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
];

$data['breadcrumbs'][] = [
'text' => $this->language->get('text_extension'),
'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true),
];

$data['breadcrumbs'][] = [
'text' => $this->language->get('heading_title'),
'href' => $this->url->link('extension/module/offers_product', 'token=' . $this->session->data['token'], true),
];
return $data;
}

private function loadMainData($data)
{
$data['token'] = $this->session->data['token'];

if (isset($this->request->post['offers_product_status'])) {
$data['offers_product_status'] = $this->request->post['offers_product_status'];
} else {
$data['offers_product_status'] = $this->config->get('offers_product_status');
}
if (isset($this->request->post['offers_product_view_only_master'])) {
$data['offers_product_view_only_master'] = $this->request->post['offers_product_view_only_master'];
} else {
$data['offers_product_view_only_master'] = $this->config->get('offers_product_view_only_master');
}
if (isset($this->request->post['offers_product_en_log'])) {
$data['offers_product_en_log'] = $this->request->post['offers_product_en_log'];
} else {
$data['offers_product_en_log'] = $this->config->get('offers_product_en_log');
}
if (isset($this->request->post['offers_product_prefix_attr'])) {
$data['offers_product_prefix_attr'] = $this->request->post['offers_product_prefix_attr'];
} elseif ($this->config->get('offers_product_prefix_attr')) {
$data['offers_product_prefix_attr'] = json_decode($this->config->get('offers_product_prefix_attr'), true);
} else {
$data['offers_product_prefix_attr'] = [""];
}


return $data;
}

private function loadAction($data)
{
$data['action'] = $this->url->link('extension/module/offers_product', 'token=' . $this->session->data['token'], true);
$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
return $data;
}

private function loadTemplate($data)
{
$data['header'] = $this->load->controller('common/header');
$data['column_left'] = $this->load->controller('common/column_left');
$data['footer'] = $this->load->controller('common/footer');
$this->response->setOutput($this->load->view('extension/module/offers_product', $data));
}

public function autocomplete()
{
$json = [];

if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
$this->load->model('catalog/product');
$this->load->model('catalog/option');
$this->load->model('extension/offers_product');

if (isset($this->request->get['filter_name'])) {
$filter_name = $this->request->get['filter_name'];
} else {
$filter_name = '';
}

if (isset($this->request->get['filter_model'])) {
$filter_model = $this->request->get['filter_model'];
} else {
$filter_model = '';
}

if (isset($this->request->get['limit'])) {
$limit = $this->request->get['limit'];
} else {
$limit = 5;
}
if (isset($this->request->get['master_product'])) {
$master_product = $this->request->get['master_product'];
} else {
$master_product = 0;
}

$filter_data = [
'filter_name'    => $filter_name,
'filter_model'   => $filter_model,
'start'          => 0,
'limit'          => $limit,
'master_product' => $master_product,
];

$results = $this->model_extension_offers_product->getProducts($filter_data);

foreach ($results as $result) {
$option_data = [];

$product_options = $this->model_catalog_product->getProductOptions($result['product_id']):
    'product_id' -> $result['product_id'];
    'name' -> strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
    'model' -> $result['model'];

    'price' -> $result['price'];
                ];
            }
}

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

}
