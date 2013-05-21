<?php
/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extention project. The EZB/DBIS-Extention project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
* Project sponsored by:
*  Avonis - New Media Agency - http://www.avonis.com/
***************************************************************/

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');


class tx_libconnect_controllers_ezb extends tx_lib_controller {
	

	public function displayListAction() {	
		
		$model = $this->makeInstance('tx_libconnect_models_ezb');
		
		if ($this->parameters->get('subject')) {
			
			$model->loadList(
				$this->parameters->get('subject'), 
				$this->parameters->get('index'),
				$this->parameters->get('sc'),
				$this->parameters->get('lc')
			);
			$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
			$view->setTemplatePath($this->configurations->get('templatePath'));
			$output = $view->render("ezb_list.tpl");

		} else if ($this->parameters->get('search')) {
			
			$model->loadSearch();
			$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
			$view->setTemplatePath($this->configurations->get('templatePath'));
			$output = $view->render("ezb_search.tpl");

		} else {
			$model->loadOverview();
			$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
			$view->setTemplatePath($this->configurations->get('templatePath'));
			$output = $view->render("ezb_overview.tpl");
		}
		return $output;
	}

	public function displayDetailAction() {
		
		if (! $this->parameters->get('jourid')) {			
			return "<b>Fehler: Es wurde keine Zeitschrift mit der angegeben URL gefunden.</b>";
		}
			
		$model = $this->makeInstance('tx_libconnect_models_ezb');	
		$model->loadDetail(intval($this->parameters->get('jourid')));

		$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
		$view->setTemplatePath($this->configurations->get('templatePath'));
		return $view->render("ezb_detail.tpl");
		
	}
	
	public function displayMiniFormAction() {
		$model = $this->makeInstance('tx_libconnect_models_ezb');	
		$model->loadMiniForm();
		
		$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
		$view->setTemplatePath($this->configurations->get('templatePath'));
		return $view->render("ezb_miniform.tpl");
	}

	public function displayFormAction() {
		$model = $this->makeInstance('tx_libconnect_models_ezb');	
		$model->loadForm();
		
		$view = $this->makeInstance('tx_libconnect_views_smarty', $model);
		$view->setTemplatePath($this->configurations->get('templatePath'));
		return $view->render("ezb_form.tpl");
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/controllers/class.tx_libconnect_controllers_ezb.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/controllers/class.tx_libconnect_controllers_ezb.php']);
}
?>