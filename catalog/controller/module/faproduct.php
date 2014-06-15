<?php
class ControllerModuleFAproduct extends Controller {

	protected function index($setting) {
		static $module = 0;
		
		$this->data['is_logged'] = $this->customer->isLogged();

		$this->load->model('catalog/product'); 		
		$this->load->model('tool/image');
		$this->load->model('catalog/faproduct');
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('faproduct');

		$this->data['title'] = $settings['title'];
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['products'] = array();

		if (!isset($this->request->get['product_id'])) {
			return;
		}

		$product_id = $this->request->get['product_id'];

		if (empty($setting['limit'])) {
			$setting['limit'] = 5;
		}
		$products = $this->model_catalog_faproduct->getFAproducts($product_id);

		if (!$products) return;

		shuffle($products);

		$products = array_slice($products, 0, (int)$setting['limit']);

		foreach ($products as $product_id) {
			$product_id = $product_id['product_id'];
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
				} else {
					$image = false;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}
					
				$this->data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $product_info['name'],
					'price'   	 => $price,
					'special' 	 => $special,
					'rating'     => $rating,
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
				);
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/faproduct.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/faproduct.tpl';
		} else {
			$this->template = 'default/template/module/faproduct.tpl';
		}

		$this->render();
	}

}
?>