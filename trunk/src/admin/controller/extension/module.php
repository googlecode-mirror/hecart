<?php
class ControllerExtensionModule extends Controller
{
	public function index()
	{
		$this->load->language('extension/module');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs']     = array();
		$this->data['breadcrumbs'][]   = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$this->data['breadcrumbs'][]   = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module'),
			'separator' => ' :: '
		);
		$this->data['heading_title']   = $this->language->get('heading_title');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_confirm']    = $this->language->get('text_confirm');
		$this->data['column_name']     = $this->language->get('column_name');
		$this->data['column_action']   = $this->language->get('column_action');

		if (isset($this->session->data['success']))
		{
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		}
		else
		{
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error']))
		{
			$this->data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		}
		else
		{
			$this->data['error'] = '';
		}

		$this->load->model('setting/extension');
		$extensions = $this->model_setting_extension->getInstalled('module');

		foreach ($extensions as $key => $value)
		{
			if (!file_exists(DIR_SITE . '/controller/module/' . $value . '.php'))
			{
				$this->model_setting_extension->uninstall('module', $value);

				unset($extensions[$key]);
			}
		}

		$this->data['extensions'] = array();
		$files                    = glob(DIR_SITE . '/controller/module/*.php');

		if ($files)
		{
			foreach ($files as $file)
			{
				$extension = basename($file, '.php');

				$this->load->language('module/' . $extension);

				$action = array();

				if (!in_array($extension, $extensions))
				{
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('extension/module/install', '&extension=' . $extension, 'SSL')
					);
				}
				else
				{
					$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('module/' . $extension . '')
					);

					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('extension/module/uninstall', '&extension=' . $extension, 'SSL')
					);
				}

				$this->data['extensions'][] = array(
					'name'   => $this->language->get('heading_title'),
					'action' => $action
				);
			}
		}

		$this->template = 'template/extension/module.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
	}

	public function install()
	{
		$this->load->language('extension/module');

		if (!$this->user->hasPermission('modify', 'extension/module'))
		{
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->redirect($this->url->link('extension/module'));
		}
		else
		{
			$this->load->model('setting/extension');
			$this->model_setting_extension->install('module', $this->request->get['extension']);
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'module/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'module/' . $this->request->get['extension']);

			require(DIR_SITE . '/controller/module/' . $this->request->get['extension'] . '.php');
			$class = 'ControllerModule' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);

			if (method_exists($class, 'install'))
			{
				$class->install();
			}

			$this->redirect($this->url->link('extension/module'));
		}
	}

	public function uninstall()
	{
		$this->load->language('extension/module');

		if (!$this->user->hasPermission('modify', 'extension/module'))
		{
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->redirect($this->url->link('extension/module'));
		}
		else
		{
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
			$this->model_setting_extension->uninstall('module', $this->request->get['extension']);
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);

			require(DIR_SITE . '/controller/module/' . $this->request->get['extension'] . '.php');
			$class = 'ControllerModule' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);

			if (method_exists($class, 'uninstall'))
			{
				$class->uninstall();
			}

			$this->redirect($this->url->link('extension/module'));
		}
	}
}

?>