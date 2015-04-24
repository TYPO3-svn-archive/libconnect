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

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */

require_once(t3lib_extMgm::extPath('libconnect') . 'Classes/UserFunctions/IsfirstPlugInUserFunction.php'); 
require_once(t3lib_extMgm::extPath('libconnect') . 'Classes/UserFunctions/RepairHTMLUserFunction.php'); 

class Tx_Libconnect_Controller_DbisController extends Tx_Extbase_MVC_Controller_ActionController {
    
    /**
     * shows top databases
     */
    public function displayTopAction() {
        //include CSS
        $this->decideIncludeCSS();
        
        $config['subject'] = $this->settings['flexform']['subject'];
        $config['detailPid'] = $this->settings['flexform']['detailPid'];

        $top =  $this->dbisRepository->loadTop($config);
        
        //variables for template
        $this->view->assign('top', $top);
    }
    
    /**
     * shows a list of databases (for general, search, choosed subject)
     */
    public function displayListAction() {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');

        //include CSS
        $this->decideIncludeCSS();
        
        if (!empty($params['subject'])) {//choosed subject after start point
            $config['sort'] = $this->settings['flexform']['sortParameter'];
            $config['detailPid'] = $this->settings['flexform']['detailPid'];
            
            //user sorted list
            if(isset($params['sort']) && !empty($params['sort'])) {
                $config['sort'] = $params['sort'];
            }
            
            $liste =  $this->dbisRepository->loadList($params['subject'], $config);
            
            //variables for template
            $this->view->assign('listhead', $liste['subject']);
            $this->view->assign('subject', $params['subject']);
            $this->view->assign('list', $liste['list']);

        } else if (!empty($params['search'])) {//search results
            $config['detailPid'] = $this->settings['flexform']['detailPid'];
            
            $liste =  $this->dbisRepository->loadSearch($params['search'], $config);
            
            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displaySearch');
            $this->view->setControllerContext($controllerContext);
            
            //variables for template
            $this->view->assign('list', $liste);

        } else {//start point
            $liste =  $this->dbisRepository->loadOverview();

            //andere View verwenden
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displayOverview');
            $this->view->setControllerContext($controllerContext);
            
            //variables for template
            $this->view->assign('list', $liste);
        }
    }
    
    /**
     * creates instance of DbisRepository
     */
    public function injectDbisRepository(Tx_Libconnect_Domain_Repository_DbisRepository $dbisRepository){
        $this->dbisRepository = $dbisRepository;
    }
    
    /**
     * creates instance of SubjectRepository
     */
    public function injectSubjectRepository(Tx_Libconnect_Domain_Repository_SubjectRepository $subjectRepository){
        $this->subjectRepository = $subjectRepository;
    }
    
    /**
     * shows deatail view
     */
    public function displayDetailAction() {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        
        //include CSS
        $this->decideIncludeCSS();
        
        if (!($params['titleid'])){
            //Variable Template übergeben
            $this->view->assign('error', 'Error');
            
            return;
        }
        $liste =  $this->dbisRepository->loadDetail($params['titleid']);
        
        //repair broken HMTL
        $liste = RepairHTMLUserFunction($liste);
        
        if(!$liste){
            //variables for template
            $this->view->assign('error', 'Error');
            
        }else{
            //BG> Hide start research link for internal access only items
            if($liste['access_id']!='access_4'){
                $liste['access_workaround']=$liste['access_id'];
            }
            //variables for template
            $this->view->assign('db', $liste);
        }        
    }
    
    /**
     * shows sidebar
     */
    public function displayMiniFormAction() {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');

        //include CSS
        $this->decideIncludeCSS();
        
        $cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
        $form = $this->dbisRepository->loadMiniForm();
        
        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
        $this->view->assign('searchUrl', $cObject->getTypolink_URL($this->settings['flexform']['searchPid']));//Link zur Suchseite
        $this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID der Listendarstellung
        
        //possibility for sorting the entries of the subject for the choosed subject
        if(!empty($params['subject'])) {
            if($params['subject'] != 'all'){
                $this->view->assign('listingsWrapper', true);
            
            //new in DBIS in alphabetical list
            }  else {
                $this->view->assign('newUrl', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
            }

            //if new activated should here the new for subject be active
           // if(!empty($this->settings['flexform']['newPid'])){
                $subject = $this->dbisRepository->getSubject($params['subject']);
                $count = (int) $this->getNewCount($subject['dbisid']);

                if($count >0){
                    $this->view->assign('newUrlSub', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
                        array('libconnect' => array('subject' => $params['subject'] )) ) );//URL der New-Darstellung
                    
                    $this->view->assign('newInSubjectCount',  $count);
                }
            //}
        //new in all subjects
        }elseif(!empty($this->settings['flexform']['newPid'])){
            $this->view->assign('newUrl', $cObject->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
        }
    }
    
    /**
     * shows the search
     */
    public function displayFormAction() {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        
        //include CSS
        $this->decideIncludeCSS();
                
        $cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
    
        $form = $this->dbisRepository->loadForm($params['search']);
        
        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
        $this->view->assign('listUrl', $cObject->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//Link zur Listendarstellung
    }

    /**
     * shows the new entries
     */
    public function displayNewAction() {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        $params['jq_type1'] = 'LD';
        $params['sc'] = $params['search']['sc'];

        if(!empty($params['subject'])){
            $subject = $this->dbisRepository->getSubject($params['subject']);
            $params['gebiete'][]=$subject['dbisid'];
        }
        unset($params['subject']);
        unset($params['search']);
        
        //include CSS
        $this->decideIncludeCSS();
        
        date_default_timezone_set('GMT+1');//@todo get the information from system
        
        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');
  
        if(!empty($this->settings['flexform']['countDays'])){
            $numDays = $this->settings['flexform']['countDays'];
        }
        
        //calcaulate date
        $date = date("d.m.Y",$today-($numDays * $oneDay));
        $params['jq_term1'] = $date;//date how long entry is new
        
        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        
        //request
        $liste =  $this->dbisRepository->loadSearch($params, $config);

        //variables for template
        $this->view->assign('list', $liste);
        $this->view->assign('new_date', date("d.m.Y",$today-($numDays * $oneDay)));
        $this->view->assign('subject', $subject['title']);
    }
    
    /**
     * count the new entries
     */
    private function getNewCount($subjectId) {
        $params['jq_type1'] = 'LD';
        $params['sc'] = $params['search']['sc'];
        $params['gebiete'][]=$subjectId;

        unset($params['subject']);
        unset($params['search']);

        date_default_timezone_set('GMT+1');//@todo get the information from system
        
        $oneDay = 86400;//seconds
        $numDays = 70; //default are 7 days
        $today = strtotime('now');
  
        if(!empty($this->settings['flexform']['countDays'])){
            $numDays = $this->settings['flexform']['countDays'];
        }
        
        //calcaulate date
        $date = date("d.m.Y",$today-($numDays * $oneDay));
        $params['jq_term1'] = $date;//date how long entry is new
        
        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        
        //request
        $list = $this->dbisRepository->loadSearch($params, $config);

        return $list['db_count'];
    }

    /**
     * check if css file is need and includes it
     */
    private function decideIncludeCSS(){
        //if user don´t want to use our css
        $noCSS = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['dbisNoCSS'];
        if($noCSS == 1){
            return;
        }

        //get UID of PlugIn
        $this->contentObj = $this->configurationManager->getContentObject();
        $uid = $this->contentObj->data['uid'];
        unset($this->contentObj);

        //only the first PlugIn needs to include the css
        if(IsfirstPlugInUserFunction('dbis', $uid)){
            $this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');    
        }
    }
}
?>