<?php
class ControllerModuleFAproduct extends Controller {

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "faproduct_to_facategory` (
  						`product_id` int(11) NOT NULL,
						`facategory_id` int(11) NOT NULL,
						PRIMARY KEY (`product_id`,`facategory_id`))");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "facategory_to_faproduct` (
						`facategory_id` int(11) NOT NULL,
  						`product_id` int(11) NOT NULL,
						PRIMARY KEY (`facategory_id`,`product_id`))");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "facategory` (
  						`facategory_id` int(11) NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL,
  						PRIMARY KEY (`facategory_id`))");

	}

	public function uninstall() {
		$this->db->query("DROP TABLE `" . DB_PREFIX . "faproduct_to_facategory`");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "facategory_to_faproduct`");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "facategory`");
	}

	private $error = array();
    
    public function index(){
      
        $this->load->language('module/faproduct');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('setting/setting');
        
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			$this->model_setting_setting->editSetting('faproduct', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				//print_r($this->model_setting_setting->getSetting('faproduct'));

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		
		$this->data['title_txt'] = $this->language->get('title');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_image'] = $this->language->get('entry_image');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('faproduct', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/faproduct', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();
		
		$settings = $this->model_setting_setting->getSetting('faproduct');

		if (isset($this->request->post['faproduct'])) {
			$this->data['modules'] = $this->request->post['faproduct_module'];
		} elseif ($this->config->get('faproduct_module')) { 
			$this->data['modules'] = $settings['faproduct_module'];
		}	

		if (isset($settings['title'])) {	
			$this->data['title'] = $settings['title'];
		} else { 
			$this->data['title'] = $this->language->get('heading_title');
		}	
		

		$this->load->model('design/layout');
		
		$layouts_full = $this->model_design_layout->getLayouts();
		$layouts_full2[0] = $layouts_full[9];
		$this->data['layouts'] = $layouts_full2;
					
		
		$this->template = 'module/faproduct.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
        
    }
    
    private function validate() {
		if (!$this->user->hasPermission('modify', 'module/faproduct')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['faproduct_module'])) {
			foreach ($this->request->post['faproduct_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>