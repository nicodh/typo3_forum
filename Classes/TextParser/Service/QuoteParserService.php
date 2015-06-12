<?php

/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2011 Martin Helmich <m.helmich@mittwald.de>                     *
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
 * Text parser class for parsing quotes.
 *
 * @author     Martin Helmich <m.helmich@mittwald.de>
 * @package    Typo3Forum
 * @subpackage TextParser_Service
 * @version    $Id$
 *
 * @copyright  2010 Martin Helmich <m.helmich@mittwald.de>
 *             Mittwald CM Service GmbH & Co. KG
 *             http://www.mittwald.de
 * @license    GNU Public License, version 2
 *             http://opensource.org/licenses/gpl-license.php
 *
 */
class Tx_Typo3Forum_TextParser_Service_QuoteParserService extends Tx_Typo3Forum_TextParser_Service_AbstractTextParserService {



	/**
	 * The post repository.
	 * @var Tx_Typo3Forum_Domain_Repository_Forum_PostRepository
	 */
	protected $postRepository;



	/**
	 * A standalone fluid view, used to render each individual quote.
	 * @var Tx_Fluid_View_StandaloneView
	 */
	protected $standaloneView;



	/**
	 * Injects an instance of the post repository class.
	 *
	 * @param  Tx_Typo3Forum_Domain_Repository_Forum_PostRepository $postRepository
	 *                             An instance of the post repository class
	 * @return void
	 */

	public function injectPostRepository(Tx_Typo3Forum_Domain_Repository_Forum_PostRepository $postRepository) {
		$this->postRepository = $postRepository;
	}



	/**
	 * Injects an instance of the fluid standalone view.
	 * @param  \TYPO3\CMS\Fluid\View\StandaloneView $view An instance of the fluid standalone view.
	 * @return void
	 */
	public function injectView(\TYPO3\CMS\Fluid\View\StandaloneView $view) {
		$this->view = $view;
	}



	/**
	 * Renders the parsed text.
	 *
	 * @param  string $text The text to be parsed.
	 * @return string       The parsed text.
	 */

	public function getParsedText($text) {
		do {
			$text = preg_replace_callback('/\[quote](.*?)\[\/quote\]\w*/is', array($this, 'replaceSingleCallback'), $text, -1, $c);
		} while($c > 0);
		do {
			$text = preg_replace_callback('/\[quote=([0-9]+)\](.*?)\[\/quote\]\w*/is', array($this, 'replaceCallback'), $text, -1, $c);
		} while($c > 0);
		return $text;
	}


	/**
	 *
	 * Callback function for rendering quotes.
	 *
	 * @param  string $matches PCRE matches.
	 *
	 * @return string          The quote content.
	 *
	 */

	protected function replaceSingleCallback($matches) {
		$this->view->setControllerContext($this->controllerContext);
		$this->view->setTemplatePathAndFilename(Tx_Typo3Forum_Utility_File::replaceSiteRelPath($this->settings['template']));
		$this->view->assign('quote', trim($matches[1]));
		$this->view->assign('post', null);
		return $this->view->render();
	}

	/**
	 *
	 * Callback function for rendering quotes.
	 *
	 * @param  string $matches PCRE matches.
	 *
	 * @return string          The quote content.
	 *
	 */

	protected function replaceCallback($matches) {
		$this->view->setControllerContext($this->controllerContext);
		$this->view->setTemplatePathAndFilename(Tx_Typo3Forum_Utility_File::replaceSiteRelPath($this->settings['template']));

		$tmp = $this->postRepository->findByUid((int)$matches[1]);
		if(!empty($tmp)){
		$this->view->assign('post', $tmp);
		}

		$this->view->assign('quote', trim($matches[2]));
		return $this->view->render();
	}

}

?>
