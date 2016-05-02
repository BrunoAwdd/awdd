<?php

namespace Awdd;

use Awdd\Db\Db;
use Awdd\Model\Client;
use Awdd\Controller\ClientController;
	
	
class Module
{
	protected $db;
	
	protected $route;
	
    public function __construct() {
		$this->db = new Db(new Client());
		$this->getRoute();
    }
	
	function loadController()
	{

		$controller = new ClientController();
		
		if (isset($this->route['id'])):
			$id = $this->route['id'];
		else:
			$id = null;
		endif;
		if (isset($this->route['action'])):
			$action = $this->route['action'];
		else :
			$action = null;
		endif;
		
		if (!$action):
			return $controller->indexAction();
		else:
			if (method_exists($controller,  strtolower($action).'Action')):
				$result = $controller->{strtolower($action).'Action'}($id);
				
				if ($result):
					$route = explode('-@-',$result);
					wp_redirect( $route[1]);
					exit;
				endif;
			else:
				return $controller->indexAction();
			endif;
		endif;
	}
	
	protected function getRoute()
	{
		foreach ($_GET as $key => $get) {
			$this->route[$key] = $get;
		}
	}
	
	public function addAction()
	{
		add_menu_page('Client', 'Client', 'activate_plugins', 'Client', array($this, 'loadController'));
	}
	
	public function uninstallAction()
	{
		$this->db->dropTable();
	}

} 