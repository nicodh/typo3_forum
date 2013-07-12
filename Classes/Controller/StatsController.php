<?php

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2013 Martin Helmich <m.helmich@mittwald.de>                     *
 *           Sebastian Gieselmann <s.gieselmann@mittwald.de>            *
 *           Ruven Fehling <r.fehling@mittwald.de>                      *
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
 * This class implements a simple dispatcher for a mm_form eID script.
 *
 * @author     Martin Helmich <m.helmich@mittwald.de>
 * @author     Sebastian Gieselmann <s.gieselmann@mittwald.de>
 * @author     Ruven Fehling <r.fehling@mittwald.de>
 * @package    MmForum
 * @subpackage Controller
 * @version    $Id$
 *
 * @copyright  2012 Martin Helmich <m.helmich@mittwald.de>
 *             Mittwald CM Service GmbH & Co. KG
 *             http://www.mittwald.de
 * @license    GNU Public License, version 2
 *             http://opensource.org/licenses/gpl-license.php
 *
 */
class Tx_MmForum_Controller_StatsController extends Tx_MmForum_Controller_AbstractController {



	/*
	 * ATTRIBUTES
	 */

	/**
	 * The report repository.
	 *
	 * @var Tx_MmForum_Domain_Repository_Stats_SummaryRepository
	 */
	protected $summaryRepository;


	/*
	 * DEPENDENCY INJECTORS
	 */

	/**
	 * @param Tx_MmForum_Domain_Repository_Stats_SummaryRepository $summaryRepository
	 */
	public function injectSummaryRepository(Tx_MmForum_Domain_Repository_Stats_SummaryRepository $summaryRepository) {
		$this->summaryRepository = $summaryRepository;

	}



	/*
	 * ACTION METHODS
	 */

	/**
	 * Listing Action.
	 * @return void
	 */
	public function listAction() {
		switch($this->settings['listStats']){
			default:
			case 'summary':
				$dataset['items'] = $this->summaryRepository->findLatestSummaryItems();
				$partial = 'Stats/Summary';
				break;
		}
		$this->view->assign('partial', $partial);
		$this->view->assign('dataset', $dataset);
	}


}
