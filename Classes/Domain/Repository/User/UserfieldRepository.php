<?php

/*                                                                    - *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2012 Martin Helmich <m.helmich@mittwald.de>                     *
 *           Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */



/**
 *
 * Repository class for additional userfields. Unlike other repositories,
 * this one creates part of its data not from the database, but load it
 * from the extension's typoscript configuration instead.
 *
 * This is done e.g. for the so-called core-userfields, that are to be
 * displayed in the user profile view independently of whether they are
 * configured in the database.
 *
 * @author     Martin Helmich <m.helmich@mittwald.de>
 * @package    Typo3Forum
 * @subpackage Domain_Repository_User
 * @version    $Id$
 *
 * @copyright  2012 Martin Helmich <m.helmich@mittwald.de>
 *             Mittwald CM Service GmbH & Co. KG
 *             http://www.mittwald.de
 * @license    GNU Public License, version 2
 *             http://opensource.org/licenses/gpl-license.php
 *
 */
class Tx_Typo3Forum_Domain_Repository_User_UserfieldRepository extends Tx_Typo3Forum_Domain_Repository_AbstractRepository {



	/*
	 * ATTRIBUTES
	 */



	/**
	 * A list of core userfields.
	 *
	 * @var Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield
	 */
	private $coreUserfields = NULL;

    /**
     * ConfigurationManagerInterface
     * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
     */
    protected $configurationManagerInterface = NULL;

	/*
	 * CONSTRUCTOR
	 */


	/**
	 * Creates a new instance of the userfield repository.
	 *
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		parent::__construct($objectManager);
		$this->objectType = 'Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield';

	}

    /**
    * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManagerInterface
    */
    public function injectConfigurationManagerInterface(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManagerInterface) {
        $this->configurationManagerInterface = $configurationManagerInterface;
    }



	/*
	 * REPOSITORY METHODS
	 */


	/**
	 * Finds all core userfields. These are stored in the typoscript setting
	 * plugin.tx_typo3forum.settings.userfields.core_fields.
	 *
	 * @return Array<Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield>
	 *                             The core userfields that are generated from the
	 *                             typoscript configuration.
	 */
	protected function findCoreUserfields() {
		if ($this->coreUserfields === NULL) {
			$conf                 =  $this->configurationManagerInterface->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			$this->coreUserfields = array();

			foreach ($conf['settings']['userfields']['core_fields'] as $coreFieldKey => $coreFieldValues) {
				$className = $coreFieldValues['class'];
				if (!class_exists($className)) {
					throw new \TYPO3\CMS\Extbase\Object\UnknownClassException("The class $className does not exist!", 1287756385);
				}

				$object = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);

				if (!$object instanceof Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield) {
					throw new \TYPO3\CMS\Extbase\Object\UnknownClassException("The class $className is not a subclass of Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield", 1287756386);
				}

				foreach ($coreFieldValues['properties'] as $propertyName => $propertyValue) {
					if ($object->_hasProperty($propertyName)) {
						$object->_setProperty($propertyName, $propertyValue);
					}
				}
				$this->coreUserfields[] = $object;
			}
		}

		return $this->coreUserfields;
	}



	/**
	 * Finds all userfields. This method loads all userfields from the database
	 * and merges the result with the core userfields that are loaded from the
	 * typoscript setup.
	 *
	 * @return Traversable<Tx_Typo3Forum_Domain_Model_User_Userfield_AbstractUserfield>
	 *                             All userfields, both from the database and
	 *                             the core typoscript setup.
	 */
	public function findAll() {
		$query = $this->createQueryWithFallbackStoragePage();
		return array_merge($this->findCoreUserfields(), $query->execute()->toArray());
	}



}
