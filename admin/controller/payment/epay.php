<?php
class ControllerPaymentePay extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/epay');

		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			
			$this->model_setting_setting->editSetting('epay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		
		$this->data['entry_demo'] = $this->language->get('entry_demo');
		$this->data['entry_demo_yes'] = $this->language->get('entry_demo_yes');
		$this->data['entry_demo_no'] = $this->language->get('entry_demo_no');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_description'] = $this->language->get('entry_description');

		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_security'] = $this->language->get('entry_security');
//		$this->data['entry_callback'] = $this->language->get('entry_callback');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

  	if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  	if (isset($this->error['currency'])) {
			$this->data['error_currency'] = $this->error['currency'];
		} else {
			$this->data['error_currency'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

 		if (isset($this->error['security'])) {
			$this->data['error_security'] = $this->error['security'];
		} else {
			$this->data['error_security'] = '';
		}
		
 		$this->data['breadcrumbs'] = array();
			
   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('payment/epay', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/epay', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['epay_demo'])) {
			$this->data['epay_demo'] = $this->request->post['epay_demo'];
		} else {
			$this->data['epay_demo'] = $this->config->get('epay_demo');
		}
		if (isset($this->request->post['epay_email'])) {
			$this->data['epay_email'] = $this->request->post['epay_email'];
		} else {
			$this->data['epay_email'] = $this->config->get('epay_email');
		}
		if (isset($this->request->post['epay_description'])) {
			$this->data['epay_description'] = $this->request->post['epay_description'];
		} else {
			$this->data['epay_description'] = $this->config->get('epay_description');
		}

		
		if (isset($this->request->post['epay_merchant'])) {
			$this->data['epay_merchant'] = $this->request->post['epay_merchant'];
		} else {
			$this->data['epay_merchant'] = $this->config->get('epay_merchant');
		}

		if (isset($this->request->post['epay_security'])) {
			$this->data['epay_security'] = $this->request->post['epay_security'];
		} else {
			$this->data['epay_security'] = $this->config->get('epay_security');
		}
		
//		$this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/epay/callback';
		
		if (isset($this->request->post['epay_order_status_id'])) {
			$this->data['epay_order_status_id'] = $this->request->post['epay_order_status_id'];
		} else {
			$this->data['epay_order_status_id'] = $this->config->get('epay_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['epay_geo_zone_id'])) {
			$this->data['epay_geo_zone_id'] = $this->request->post['epay_geo_zone_id'];
		} else {
			$this->data['epay_geo_zone_id'] = $this->config->get('epay_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['epay_status'])) {
			$this->data['epay_status'] = $this->request->post['epay_status'];
		} else {
			$this->data['epay_status'] = $this->config->get('epay_status');
		}
		
		if (isset($this->request->post['epay_sort_order'])) {
			$this->data['epay_sort_order'] = $this->request->post['epay_sort_order'];
		} else {
			$this->data['epay_sort_order'] = $this->config->get('epay_sort_order');
		}


		$this->template = 'payment/epay.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function checkCurrencyBGN() {
		$query = $this->db->query("SELECT currency_id FROM " . DB_PREFIX . "currency WHERE LCASE(code)='bgn'");
		if($query->row) {
			return true;
		} else {
			return false;
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/epay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->checkCurrencyBGN()) {
			$this->error['currency'] = $this->language->get('error_bgn_currency');
		}
		
		if (!$this->request->post['epay_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['epay_security']) {
			$this->error['security'] = $this->language->get('error_security');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>