<?php
namespace Awdd\Controller;

use Awdd\Model\Client;
use Awdd\Db\Db;
use Awdd\Form\ClientForm;

class ClientController{
	
	protected $db;
	protected $msg;
	
	public function __construct()
	{
		$client = new Client();
		$this->db = new Db(new Client());
	}
	
	public function indexAction ()
	{
		$clients =  $this->db->findAll();
		include_once('/../View/index.phtml');
	}
	
	public function addAction()
	{
		$form = new ClientForm();
		$form->setObject(new Client());
		$form->bind($_POST);
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if($form->isValid()):
				$this->db->persist($form->getObject());
				return 'redirect-@-admin.php?page=Client';
			endif;
		}
		include_once('/../View/add.phtml');
	}
	
	public function editAction($id)
	{
		$client = $this->db->find($id);
		if (!$client):
			$this->msg = 'Client not found';
			return $this->addAction();
		endif;
		$form = new ClientForm();
		$form->setObject(new Client());
		
		if (!$_POST):
			$form->bind($client->toArray());
		else :
			$form->bind($_POST);
		endif;
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			var_dump($form->isValid());
			if($form->isValid()):
				$this->db->persist($form->getObject());
				return 'redirect-@-admin.php?page=Client';
			endif;
		}
		include_once('/../View/edit.phtml');
	}
	
	public function deleteAction($id)
	{
		$client = $this->db->find($id);
		if (!$client):
			$this->msg = 'Client not found';
			return $this->indexAction();
		endif;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if($_POST['deleteClient'] == 'Yes'):
				$this->db->remove($id);
				return 'redirect-@-admin.php?page=Client';
			endif;
		}
		include_once('/../View/delete.phtml');
	}
}

?>