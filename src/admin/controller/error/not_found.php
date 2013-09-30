<?php
class ControllerErrorNotFound extends Controller
{
	public function index()
	{
		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['heading_title']  = $this->language->get('heading_title');
		$this->data['text_not_found'] = $this->language->get('text_not_found');
		$this->data['breadcrumbs']    = array();
		$this->data['breadcrumbs'][]  = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$this->data['breadcrumbs'][]  = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('error/not_found'),
			'separator' => ' :: '
		);
		$this->template               = 'template/error/not_found.tpl';
		$this->children               = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
	}
}

?>