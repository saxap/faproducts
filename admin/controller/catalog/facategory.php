<?php 
class ControllerCatalogFACategory extends Controller { 
	private $error = array();
	private $facategory_id = 0;
	private $path = array();
 
	public function index() {
		$this->load->language('catalog/faproduct');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/faproduct');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('catalog/faproduct');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/faproduct');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_faproduct->addFACategory($this->request->post['facategory_description']);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/facategory', 'token=' . $this->session->data['token'], 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('catalog/faproduct');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/faproduct');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_faproduct->editFACategory($this->request->get['facategory_id'], $this->request->post['facategory_description']);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('catalog/facategory', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/faproduct');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/faproduct');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $facategory_id) {
				$this->model_catalog_faproduct->deleteFACategory($facategory_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('catalog/facategory', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/facategory', 'token=' . $this->session->data['token'] . '&path=', 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('catalog/facategory/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('catalog/facategory/delete', 'token=' . $this->session->data['token'], 'SSL');

		$fa_arr = $this->model_catalog_faproduct->getFACategories();

		if ($fa_arr) {
			foreach ($fa_arr as $fa) {
				$this->data['facategories'][] = array(
						'facategory_id' => $fa['facategory_id'],
						'name'        => $fa['name'],
						'href'        => $this->url->link('catalog/facategory/update', 'token=' . $this->session->data['token'] . '&facategory_id='.$fa['facategory_id'], 'SSL'),
						'text'		  => $this->language->get('change')
					);		
			}
		} else {
			$this->data['facategories'] = false;
		}
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->template = 'catalog/facategory_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
				
		$this->data['entry_name'] = $this->language->get('entry_name');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

    	$this->data['tab_general'] = $this->language->get('tab_general');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/category', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['facategory_id'])) {
			$this->data['action'] = $this->url->link('catalog/facategory/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('catalog/facategory/update', 'token=' . $this->session->data['token'] . '&facategory_id=' . $this->request->get['facategory_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('catalog/facategory', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['facategory_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$this->data['facategory_description'] = $this->model_catalog_faproduct->getFACategory($this->request->get['facategory_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];

						
		$this->template = 'catalog/facategory_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/facategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$value = $this->request->post['facategory_description'];

		if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
					
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}



}
?>