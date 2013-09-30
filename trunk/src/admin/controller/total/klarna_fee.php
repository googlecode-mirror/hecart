<?php
class ControllerTotalKlarnaFee extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('total/klarna_fee');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate()))
		{
			$this->model_setting_setting->editSetting('klarna_fee', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/total'));
		}

		$this->data['heading_title']    = $this->language->get('heading_title');
		$this->data['text_enabled']     = $this->language->get('text_enabled');
		$this->data['text_disabled']    = $this->language->get('text_disabled');
		$this->data['text_none']        = $this->language->get('text_none');
		$this->data['entry_total']      = $this->language->get('entry_total');
		$this->data['entry_fee']        = $this->language->get('entry_fee');
		$this->data['entry_tax_class']  = $this->language->get('entry_tax_class');
		$this->data['entry_status']     = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['button_save']      = $this->language->get('button_save');
		$this->data['button_cancel']    = $this->language->get('button_cancel');

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$this->data['breadcrumbs']   = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_total'),
			'href'      => $this->url->link('extension/total'),
			'separator' => ' :: '
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('total/klarna_fee'),
			'separator' => ' :: '
		);
		$this->data['action']        = $this->url->link('total/klarna_fee');
		$this->data['cancel']        = $this->url->link('extension/total');

		if (isset($this->request->post['klarna_fee_total']))
		{
			$this->data['klarna_fee_total'] = $this->request->post['klarna_fee_total'];
		}
		else
		{
			$this->data['klarna_fee_total'] = $this->config->get('klarna_fee_total');
		}

		if (isset($this->request->post['klarna_fee_fee']))
		{
			$this->data['klarna_fee_fee'] = $this->request->post['klarna_fee_fee'];
		}
		else
		{
			$this->data['klarna_fee_fee'] = $this->config->get('klarna_fee_fee');
		}

		if (isset($this->request->post['klarna_fee_tax_class_id']))
		{
			$this->data['klarna_fee_tax_class_id'] = $this->request->post['klarna_fee_tax_class_id'];
		}
		else
		{
			$this->data['klarna_fee_tax_class_id'] = $this->config->get('klarna_fee_tax_class_id');
		}

		if (isset($this->request->post['klarna_fee_status']))
		{
			$this->data['klarna_fee_status'] = $this->request->post['klarna_fee_status'];
		}
		else
		{
			$this->data['klarna_fee_status'] = $this->config->get('klarna_fee_status');
		}

		if (isset($this->request->post['klarna_fee_sort_order']))
		{
			$this->data['klarna_fee_sort_order'] = $this->request->post['klarna_fee_sort_order'];
		}
		else
		{
			$this->data['klarna_fee_sort_order'] = $this->config->get('klarna_fee_sort_order');
		}

		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		$this->template            = 'template/total/klarna_fee.tpl';
		$this->children            = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'total/klarna_fee'))
		{
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return (!$this->error) ? true : false;
	}
}

?>