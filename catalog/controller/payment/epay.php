<?php
class ControllerPaymentePay extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
			$this->data['button_back'] = $this->language->get('button_back');

		if ($this->config->get('epay_demo') == "" || $this->config->get('epay_demo') == "demo") {
			$this->data['action'] = 'https://demo.epay.bg/';
		} else {
			$this->data['action'] = 'https://www.epay.bg/';
		}
  				
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	

		$this->data['secret'] = $this->config->get('epay_security');
		$this->data['min'] = $this->config->get('epay_merchant');;
		$this->data['invoice'] = sprintf('%.0f', rand(10, 99999) * 100000);;
		$this->data['exp_date'] = '01.08.2020';
		$this->data['email'] = $this->config->get('epay_email');
		$this->data['description'] = $this->config->get('epay_description');

		$this->data['ep_merchant'] = $this->config->get('epay_merchant');

		$total_bgn = $this->currency->convert($order_info['total'], $order_info['currency_code'], 'BGN');
		$amount = $this->currency->format($total_bgn, 'BGN', $order_info['currency_value'], FALSE);
		
		$this->data['ep_amount'] = $amount;
		$this->data['ep_currency'] = 'BGN';
		//$this->currency->format($order_info['total'], /*$order_info['currency_code']*/'BGN', $order_info['currency_value'], FALSE);
//		$this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);

		$this->data['ep_currency'] = $order_info['currency_code'];
		$this->data['order_info'] = $order_info;
		
		$this->data['ep_purchasetype'] = 'Item';
		$this->data['ep_itemname'] = $this->config->get('config_name') . ' - #' . $this->session->data['order_id'];
		$this->data['ep_itemcode'] = $this->session->data['order_id'];


		$this->data['ep_statusurl'] = $this->url->link('payment/epay/callback/');
		$this->data['ep_returnurl'] = $this->url->link('payment/epay/callback/');

		$_description = '';
		if (!empty($this->data['description']))
			$_description = 
				''.$this->data['email'].' '.
				iconv("utf-8", "windows-1251", $this->data['description']);
		if(isset($this->session->data['comment']) && !empty($this->session->data['comment'])) 
			$_description .= ' '.iconv("utf-8", "windows-1251", $this->session->data['comment']);
		$_data = "
		MIN={$this->data['min']}
		MERCHANTCODE={$this->data['ep_merchant']}
		INVOICE={$this->data['invoice']}
		CURRENCY=BGN
		AMOUNT={$this->data['ep_amount']}
		EXP_TIME={$this->data['exp_date']}
		DESCR={$_description}
		";

		$this->data['ENCODED'] = base64_encode($_data);
		$this->data['CHECKSUM'] = $this->hmac('sha1', $this->data['ENCODED'], $this->data['secret']); 

		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['ep_cancelurl'] = $this->url->link('checkout/checkout');
		} else {
			$this->data['ep_cancelurl'] = $this->url->link('checkout/checkout');
		}
				
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['back'] = $this->url->link('checkout/payment');
		} else {
			$this->data['back'] = $this->url->link('checkout/guest_step_2');
		}
		
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/epay.tpl';
		} else {
			$this->template = 'default/template/payment/epay.tpl';
		}	

		$this->render();	
	}

	public function hmac($algo,$data,$passwd){
	      /* md5 and sha1 only */
	      $algo=strtolower($algo);
	      $p=array('md5'=>'H32','sha1'=>'H40');
	      if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
	      if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));
	
	      $ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
	      $opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);
	
	      return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
	}
	
	public function confirm() {
		$this->language->load('payment/epay');

		$this->load->model('checkout/order');

		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('epay_order_status_id'), true);
	}
	
	public function callback() {
		$this->load->model('checkout/order');

		if (isset($this->session->data['order_id']) && $this->session->data['order_id']) {
			$order_id = $this->session->data['order_id'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_checkout_order->getOrder($order_id);
			
		if (is_array($order_info)) {
			$order_status_id = $this->config->get('epay_order_status_id');
		}

		if (!$order_info['order_status_id']) {

			$this->model_checkout_order->confirm($order_id, $order_status_id);

		} else {

			$this->model_checkout_order->update($order_id, $order_status_id);

		}
		
		$this->redirect($this->url->link('checkout/success', '', 'SSL'));	

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/epay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/epay.tpl';
		} else {
			$this->template = 'default/template/payment/epay.tpl';
		}	

		$this->render();	


	}
	
}
?>