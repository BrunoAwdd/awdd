<?php

namespace Awdd\Db;

use \ReflectionClass;

class Db {
	
	protected $wpdb;

	protected $class;
	
	protected $table;
	
	protected $tableName;
	
	protected $methods;
	
	protected $properties = array();
	
	protected $reflection;
	
	protected $id;
	
	protected $sql;
	
	public function __construct ($class)
	{
		global $wpdb;
		$this->wpdb = $wpdb;	
		$this->class = new $class;
		$this->reflection = new ReflectionClass($class);
		$this->setTable($this->reflection->getDocComment());
		$this->setProperties($this->reflection->getProperties());
		$this->createTable();
				
	}
	
	/**
	 * propertis setter
	 * @param Array
	 * @return void
	 */
	protected function setProperties(Array $properties)
	{
		foreach ($properties as $property) :
			$prop = $this->reflection->getProperty($property->name)->getDocComment();
			preg_match_all('#@(.*?)\n#s', $prop, $annotations);
			
			if(!empty($annotations[0])):
				$settings = $this->setProperty($annotations[0], $property->name);
				$this->properties[$property->name] = $settings;
			endif;
			
		endforeach;
	}
	
	protected function setTable($annotation)
	{
		preg_match_all('#@(.*?)\n#s', $annotation, $annotations);	
		$settings = $this->setProperty($annotations[0]);
		$this->table = $settings;
		$this->tableName = $this->wpdb->prefix . $this->table['table']['options']['name'];
		
	}
	
	protected function setProperty($property, $propertyName = null)
	{
		$settings = array();
		foreach ($property as $annotation) :
			if (strpos($annotation, '(')):
				
				preg_match('#\(.*?\)#', $annotation, $optionsStr);
				$name = trim(str_replace(array($optionsStr[0],'@'), '', $annotation));
				
				$optionStr = str_replace(array('@'.$name, '(', ')', '"', '\''),'',$optionsStr[0]);
				
				$option = explode(',', $optionStr);
				
				foreach ($option as $item) {
					
					$item = explode('=', $item);
					$options[trim($item[0])] = trim($item[1]);
					
				}
					
			else:
				$name = trim(str_replace('@', '', $annotation));
				$options = '';
			endif;

			
	
			if ($name == 'Id')
			{
				$this->id = $propertyName;
			}
				

			$settings[$name] = array(
				'name'	=> $name,
				'options' => $options,
			
			);
			
		endforeach;
		return $settings;
	}	
	
	protected function existTable()
	{
		$table = $this->tableName;
		// create the ECPT metabox database table
		if($this->wpdb->get_var("show tables like '$table'") == $table):
			return true;
		else:
			return false;
		endif;	
		
	}
	
	protected function createTable()
	{
		
		$table = $this->tableName;
		$sql ='';
		if(!$this->existTable()):
			$sql .= "CREATE TABLE ";
			$sql .= $table;
			$sql .= " (";
			$id = null;
			$countProperties = count ($this->properties);
			$comma = 0;
			foreach ($this->properties as $key => $property):
				
				foreach ($property as $config) {
					if ($config['name'] == 'Id' && $id === null):
						$id = true;
					endif;
					if($config['name'] == 'Column'):
						$sql .= "" . $key  .   " ";
						$sql .= $config['options']['type'] . ' ';
						
						
						if(isset($config['options']['length'])):
							$sql .= '(' . $config['options']['length'] . ')';
						endif;
						
						if(isset($property['options']['nullable']) && $config['options']['nullable'] == true):
							$sql .= ' NULL ';
						else:
							$sql .= ' NOT NULL ';
						endif;
						
						if ($id):
							$sql .= 'PRIMARY KEY AUTO_INCREMENT';
							$id = false;
						endif;
						
						$comma++;
						if ($comma < $countProperties):
							$sql .=' , '.PHP_EOL;
						endif;
						
							
					endif;
				}
				
			endforeach;
			
			$sql .= " ) CHARACTER SET utf8 COLLATE utf8_bin;";
			
			$this->wpdb->query($sql);
		endif;	
		return ($sql);
		
	}
	
	public function findAll()
	{
		$sql ='';
		if($this->existTable()):
			$sql .= "SELECT * FROM  ";
			$sql .= $this->tableName;
			
			$this->sql = $sql;
			
			$result = $this->wpdb->get_results( $sql );
		endif;
		$r = array();
		foreach ($result  as $item) {
			$r[] = $this->hydrateModule($item);
		}
		return ($r);
	}
	
	public function find($id)
	{
		
		$sql ='';
		if($this->existTable()):
			$sql .= "SELECT * FROM  {$this->tableName} WHERE {$this->id} = %d";
			$sql = $this->wpdb->prepare($sql, $id);
			$this->sql = $sql;
			$result = $this->wpdb->get_row( $sql );
			if ($result):
				$module = $this->hydrateModule($result);
				
			endif;
		endif;
		return ($module);
	}
	
	public function findBy($param)
	{
		$sql ='';
		if($this->existTable()):
			$sql .= "SELECT * FROM  ";
			$sql .= $this->tableName;
			$sql .= " WHERE ";
			
			$countParam = count($param);
			$i = 0;
			foreach ($param as $key => $criteria) {
				if (!isset($this->properties[$key])):
					continue;
				endif;
				if ($this->properties[$key]['Column']['options']['type'] == 'int'):
					$where = "{$key} = %d";
				else:
					$where = "{$key} LIKE %s";
				endif;	
				
				$where = $this->wpdb->prepare($where, $criteria);
				$sql .= $where;
				$i++;
				if($countParam > $i):
					$sql .= ' AND ';
				endif;
				
			}
			$this->sql = $sql;
			$result = $this->wpdb->get_results( $sql );
		endif;
		$r = array();
		foreach ($result  as $item) {
			$r[] = $this->hydrateModule($item);
		}
		return ($r);
	}

	public function flush()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($this->sql);
	}
	
	public function persist($object)
	{
		$data = $object->toArray();
		if($this->existTable()):
			
			$filter = array();
			foreach ($this->properties as $key => $property) {
				if ($property['Column']['options']['type'] == 'varchar'):
					$filter[] = '%s';
				elseif($property['Column']['options']['type'] == 'varchar'):
					$filter[] = '%d';
				endif;
			}
			
			if (isset($data[$this->id])):
				$exist = $this->find($data[$this->id]);
				if ($exist != null):
					$this->wpdb->update($this->tableName, $data, array($this->id => $data[$this->id]), $filter, $where_format = null );
				else:
					$this->wpdb->insert($this->tableName, $data, $filter );
				endif;
			endif;
			//
			//
		endif;

	}
	
	public function remove($id)
	{
		
		if($this->existTable()):
			$exist = $this->find($id);
			if ($exist != null):
				$this->wpdb->delete($this->tableName, array($this->id => $id), $where_format = null );
			else:
				return false;
			endif;
		endif;

	}
	
	public function hydrateModule($params)
	{
		$class = new $this->class();
		 foreach ($params as $param => $value)
        {
            if (method_exists($class, 'set' . ucfirst($param))) {
                $class->{'set' . ucfirst($param)}($value);
				
            }
        }
		return $class;
	}
	public function dropTable()
	{
		if($this->existTable()):	
			$this->wpdb->query("DROP TABLE {$this->tableName}");
		endif;	
	}
	
	
}


?>