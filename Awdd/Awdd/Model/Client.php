<?php

namespace Awdd\Model;
/**
 * @table(name="cliente")
 */
class Client {
	
	/**
	 * @Id
	 * @Column(type="int", length="10", nullable=false)
	 */
	protected $id;
	
	/**
	 * @Column(type="varchar", length="255", nullable=false)
	 */
	protected $name;
	
	/**
	 * @Column(type="varchar", length="30", nullable=false)
	 */
	protected $fone;
	
	/**
	 * @Column(type="varchar", length="50", nullable=false)
	 */
	protected $email;
	
	
	/**
	 * id Setter
	 * @param integer
	 * @return void
	 */
	public function setId($value)
	{
		$this->id = (int)$value;
	}
	/**
	 * id Getter
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * name setter
	 * @param string
	 * @return void
	 */
	public function setName($value)
	{
		$this->name = $value;
	}
	
	/**
	 * name getter
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * fone setter
	 * @param string
	 * @return void
	 */
	public function setFone($value)
	{
		$this->fone = $value;
	}
	
	/**
	 * fone getter
	 * @return string
	 */
	public function getFone()
	{
		return $this->fone;
	}
	
	/**
	 * email setter
	 * @param string
	 * @return void
	 */
	public function setEmail($value)
	{
		$this->email = $value;
	}
	
	/**
	 * email getter
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	public function toArray()
	{
		return get_object_vars($this);
	}
	
}


?>