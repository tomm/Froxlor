<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @author     Andreas Burchert <scarya@froxlor.org>
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    DMS
 */

/**
 * This class represents a (sub)domain.
 */
class domain {
	/**
	 * The domainname.
	 * @var string
	 */
	private $_domainname = "";
	
	/**
	 * TLD.
	 * @var string
	 */
	private $_tld = null;
	
	/**
	 * Contains the owner handle.
	 * @var handle
	 */
	private $_handle = null;
	
	/**
	 * Contains all subdomains.
	 * @var array[domain]
	 */
	private $_subdomains = array();
	
	/**
	 * Parent domain.
	 * @var domain
	 */
	private $_parent = null;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param string $tld
	 */
	public function __construct($name, $tld = null) {
		$this->_domainname = $name;
		$this->_tld = $tld;
	}
	
	/**
	 * Returns all subdomains.
	 *
	 * @return array of domain
	 */
	public function getSubdomains() {
		return $this->_subdomains;
	}
	
	/**
	 * Adds a subdomain.
	 *
	 * @param domain $domain
	 */
	public function addSubdomain($domain) {
		$domain->setParent($this);
		$this->_subdomains[] = $domain;
	}
	
	/**
	 * Removes a subdomain.
	 *
	 * @param domain $domain
	 *
	 * @return boolean true on success
	 */
	public function removeSubdomain($domain) {
		foreach ($this->_subdomains as $key => $d) {
			// is the domain available?
			if ($d->getName() === $domain->getName()) {
				unset($this->_subdomains[$key]);
				return true;
			}
			
			// subdomains?
			if (count($this->_subdomains) > 0) {
				if ($d->removeSubdomain($domain)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Sets the parent for this domain.
	 *
	 * @param domain $domain
	 */
	public function setParent($domain) {
		$this->_parent = $domain;
	}
	
	/**
	 * @return the parent domain
	 */
	public function getParent() {
		return $this->_parent;
	}
	
	/**
	 * @return string the domainname
	 */
	public function getName() {
		return $this->_domainname;
	}
	
	/**
	 * @return string the tld
	 */
	public function getTld() {
		return $this->_tld;
	}
	
	/**
	 * @return the fqdn
	 */
	public function getFQDN() {
		$parent = $this->getParent();
		$fqdn = $this->getName();
		
		// is there a parent?
		if (!is_null($parent)) {
			// circle as long as there isn't a tld
			while (is_null($parent->_tld)) {
				$parent = $parent->getParent();
				if (is_null($parent)) {
					break;
				}
				$fqdn .= ".". $parent->getName();
			}
			
			$fqdn .= ".". $parent->getName();
		} else {
			$parent = $this;
		}
		$fqdn .= ".". $parent->getTld();
		
		return $fqdn;
	}
	
	/**
	 * Sets the owner handle.
	 *
	 * @param handle $handle
	 */
	public function setOwner($handle) {
		$this->_handle = $handle;
	}
}