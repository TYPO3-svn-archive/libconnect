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
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * Doku: http://www.bibliothek.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * Doku: http://rzblx1.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * @author niklas guenther
 * @author Torsten Witt
 *
 */

class Tx_libconnect_Resources_Private_Lib_Ezb {

    // document search meta infos
    private $title;

    // general config
    private $overview_requst_url = 'http://ezb.uni-regensburg.de/ezeit/fl.phtml?xmloutput=1&';
    private $detailview_request_url = 'http://ezb.uni-regensburg.de/ezeit/detail.phtml?';
    private $search_url = 'http://ezb.uni-regensburg.de/ezeit/search.phtml?xmloutput=1&';
    //private $journal_link_url = 'http://rzblx1.uni-regensburg.de/ezeit/warpto.phtml?bibid=SUBHH&colors=7&lang=de&jour_id=';
    private $search_result_page = 'http://ezb.uni-regensburg.de/ezeit/searchres.phtml?&xmloutput=1&';
    //private $search_result_page = 'http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?&xmloutput=1&bibid=SUBHH&colors=7&lang=de&';
    //private $search_result_page = 'http://ezb.uni-regensburg.de/searchres.phtml?xmloutput=1&bibid=SUBHH&colors=7&lang=de';
    private $participants_url = 'http://ezb.uni-regensburg.de/ezeit/where.phtml?';
    private $participants_xml_url = 'http://ezb.uni-regensburg.de/ezeit/where.phtml?&xmloutput=1&';
    private $contact_url = 'http://ezb.uni-regensburg.de/ezeit/kontakt.phtml?';
    private $search_zd_id = 'http://ezb.uni-regensburg.de/?';

    private $lang = 'de';
    private $colors = 7;

    // Fachbereich Journals
    public $notation;
    public $sc;
    public $lc;
    public $sindex;

    // typoscript Konfigurationsvariablen
    private $bibID;

    // XML Daten
    private $XMLPageConnection;

    // Lizenzinfos
    private $shortAccessInfos = Array();
    private $longAccessInfos = Array();

    //Suchtypen
    public $jq_type = 	array(
        'KT' => 'Titelwort(e)',
        'KS' => 'Titelanfang',
        'IS' => 'ISSN',
        'PU' => 'Verlag',
        'KW' => 'Schlagwort(e)',
        'ID' => 'Eingabedatum',
        'LC' => 'Letzte Änderung',
        'ZD' => 'ZDB-Nummer');

    /**
     * Konstruktor
     *
     */
    public function __construct() {
        $this->XMLPageConnection = t3lib_div::makeInstance('tx_libconnect_resources_private_lib_xmlpageconnection');
        
        //set configurations
        $this->setBibID();
        $this->setLanguage();
    }

    /**
     * Funktion setzt die EZB Bibliothek ID Klassenvariable
     *
     */
    private function setBibID() {
		$this->bibID = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
    }

    /**
     * returns the bibID
     * 
     * @return string
     */
    public function getBibID() {
		return $this->bibID;
    }

    /**
     * Fachbereiche laden
     *
     * @return array()
     */
    public function getFachbereiche() {

        $fachbereiche = array();
        $url = "{$this->overview_requst_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&";
        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        if (isset($xml_request->ezb_subject_list->subject)) {
            foreach ($xml_request->ezb_subject_list->subject AS $key => $value) {
                $fachbereiche[(string) $value['notation'][0]] = array('title' => (string) $value[0], 'journalcount' => (int) $value['journalcount'], 'id' => (string) $value['notation'][0], 'notation' => (string) $value['notation'][0]);
            }
        }

        return $fachbereiche;
    }

    /**
     * Alle Journals eines Fachbereichs laden
     *
     * @param string $jounal
     * @param string $letter
     * @param string $lc
     * @param $sindex int
     *
     * @return array()
     */
    public function getFachbereichJournals($jounal, $sindex = 0, $sc = 'A', $lc = '') {

        $journals = array();
        $url = "{$this->overview_requst_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&sc={$sc}&lc={$lc}&sindex={$sindex}&notation={$jounal}&";

        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        if ($xml_request->page_vars) {
            $this->notation = (string) $xml_request->page_vars->notation->attributes()->value;
            $this->sc = (string) $xml_request->page_vars->sc->attributes()->value;
            $this->lc = (string) $xml_request->page_vars->lc->attributes()->value;
            $this->sindex = (string) $xml_request->page_vars->sindex->attributes()->value;
        }

        //Navigationsliste
        if ($xml_request->ezb_alphabetical_list) {

            $journals['subject'] = (string) $xml_request->ezb_alphabetical_list->subject;
            $journals['navlist']['current_page'] = (string) $xml_request->ezb_alphabetical_list->navlist->current_page;
            $journals['navlist']['current_title'] = (string) $xml_request->ezb_alphabetical_list->current_title;

            foreach ($xml_request->ezb_alphabetical_list->navlist->other_pages AS $key2 => $value2) {
                foreach ($value2->attributes() AS $key3 => $value3) {
                    $journals['navlist']['pages'][(string) $value2[0]][(string) $key3] = (string) $value3;
                }
                // set title
                $journals['navlist']['pages'][(string) $value2[0]]['title'] = (string) $value2[0];
            }
        }
        $journals['navlist']['pages'][$journals['navlist']['current_page']] = $journals['navlist']['current_page'];
        ksort($journals['navlist']['pages']);


        //Ergebnisse
        if (isset($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal)) {
            foreach ($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal AS $key => $value) {
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
                $journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
            }
        }

        $i = 0;

        if (isset($xml_request->ezb_alphabetical_list->next_fifty)) {
            foreach ($xml_request->ezb_alphabetical_list->next_fifty AS $key => $value) {
                $journals['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
                $journals['alphabetical_order']['next_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
                $journals['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
                $journals['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
                $i++;
            }
        }

        $i = 0;

        if (isset($xml_request->ezb_alphabetical_list->first_fifty)) {
            foreach ($xml_request->ezb_alphabetical_list->first_fifty AS $key => $value) {
                $journals['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
                $journals['alphabetical_order']['first_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
                $journals['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
                $journals['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
                $i++;
            }
        }

        return $journals;
    }

    /**
     * Details zu einem Journal laden
     *
     * @param journalId int
     *
     * @return string
     */
    public function getJournalDetail($journalId) {

        $journal = array();
        $url = $this->getDetailviewRequestUrl() . '&xmloutput=1&colors=' . '&jour_id=' . $journalId . '&bibid='. $this->bibID . '&lang=' . $this->lang;
        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        if (!is_object($xml_request->ezb_detail_about_journal->journal)) {
            return FALSE;
        }

        $journal['id'] = (int) $xml_request->ezb_detail_about_journal->journal->attributes()->jourid;
        $journal['title'] = (string) $xml_request->ezb_detail_about_journal->journal->title;
        $journal['color'] = (string) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color;
        $journal['color_code'] = (int) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color_code;
        $journal['publisher'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->publisher;
        $journal['ZDB_number'] = (string) @$xml_request->ezb_detail_about_journal->journal->detail->ZDB_number;
        $journal['ZDB_number_link'] = (string) @$xml_request->ezb_detail_about_journal->journal->detail->ZDB_number->attributes()->url;
        $journal['subjects'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->detail->subjects->subject)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->detail->subjects->subject as $subject) {
                $journal['subjects'][] = (string) $subject;
            }
        }
        $journal['subjects_join'] = join(', ', $journal['subjects']);
        $journal['pissns'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN as $pissn) {
                $journal['pissns'][] = (string) $pissn;
            }
        }
        $journal['pissns_join'] = join(', ', $journal['pissns']);
        $journal['eissns'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN as $eissn) {
                $journal['eissns'][] = (string) $eissn;
            }
        }
        $journal['eissns_join'] = join(', ', $journal['eissns']);
        $journal['keywords'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->detail->keywords->keyword)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->detail->keywords->keyword as $keyword) {
                $journal['keywords'][] = (string) $keyword;
            }
        }
        $journal['keywords_join'] = join(', ', $journal['keywords']);
        $journal['fulltext'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext;

        if (isset($xml_request->ezb_detail_about_journal->journal->detail->fulltext)) {
            $i = 1;
            $warpto = urlencode((string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext->attributes()->url);
            $journal['fulltext_link'] = 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang . '&jour_id=' . $journalId . '&url=' . $warpto;
            //$journal['fulltext_link'] = str_replace('http%3A%2F%2F', 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid='.$bibid.'&colors='.$this->colors.'&lang='.$this->lang.'&jour_id='.$journalId.'&url=http%3A%2F%2F', $warpto, $i);
        }

        $journal['homepages'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->detail->homepages->homepage)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->detail->homepages->homepage as $homepage) {
                $journal['homepages'][] = array(
                        'linktext' => (string) $homepage,
                        'url' => (string) $homepage->attributes()->url);
            }
        }
        $journal['first_fulltext'] = array(
            'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_volume,
            'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_issue,
            'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_date
        );
        if ($xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue) {
            $journal['last_fulltext'] = array(
                'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_volume,
                'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_issue,
                'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_date
            );
        }
        if(isset($xml_request->ezb_detail_about_journal->journal->detail->moving_wall)){
            $temp = (string) $xml_request->ezb_detail_about_journal->journal->detail->moving_wall;
            $journal['moving_wall'] = $this->getMovingwall($temp);
        }
        $journal['appearence'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->appearence;
        $journal['costs'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->costs;
        $journal['remarks'] = trim((string) $xml_request->ezb_detail_about_journal->journal->detail->remarks);

        if(empty($journal['remarks'])){
            unset($journal['remarks']);
        }

        //check institutions having access to this journal
        $journal['participants'] = $this->getParticipants($journalId);

        // periods

        $color_map = array(
            'green' => 1,
            'yellow' => 2,
            'red' => 4,
            'yellow_red' => 6
        );
        $journal['periods'] = array();
        if (isset($xml_request->ezb_detail_about_journal->journal->periods->period)) {
            foreach ($xml_request->ezb_detail_about_journal->journal->periods->period as $period) {
                $i = 1;
                $warpto = '';
                $domain = '';

                if (@$period->warpto_link->attributes()->url) {
                    $warpto = urlencode((string) $period->warpto_link->attributes()->url);
                }

                $test = (string) @$period->readme_link->attributes()->url;

                if(!empty($test)){
                    if(!preg_match('/^http/', $test)){
                        $domain = 'http://rzblx1.uni-regensburg.de/ezeit/';
                    }
                }

                $journal['periods'][] = array(
                    'label' => (string) $period->label,
                    'color' => (string) @$period->journal_color->attributes()->color,
                    'color_code' => $color_map[(string) @$period->journal_color->attributes()->color],
                    'link' => 'http%3A%2F%2Frzblx1.uni-regensburg.de%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '%26colors=' . $this->colors . '%26lang=' . $this->lang . '%26jour_id=' . $journalId . '%26url=' . $warpto,
                    'readme' => $domain. (string) @$period->readme_link->attributes()->url
                );
            }
        }

        return $journal;
    }

    /**
     * Detailsuche Formular ausgeben
     *
     * @return array
     */
    public function detailSearchFormFields() {
        $url = "{$this->search_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}";
        $xml_such_form = $this->XMLPageConnection->getDataFromXMLPage($url);

        foreach ($xml_such_form->ezb_search->option_list AS $key => $value) {
            foreach ($value->option AS $key2 => $value2) {
                $form[(string) $value->attributes()->name][(string) $value2->attributes()->value] = (string) $value2;
            }
        }

        // Schlagwort und issn tauschen...
        //$form['jq_type'] = $this->jq_type;

        return $form;
    }

    /**
     * Suchurl erzeugen
     *
     * @param term string
     * @param searchVars array
     *
     * @return string
     */
    private function createSearchUrl($term, $searchVars/* , $lett = 'k' */) {

        //Falls Suchweiterleitung aus der ursprünglichen EZB-Ansicht
        if(isset($searchVars['jq_type1']) && $searchVars['jq_type1'] == 'ZD'){
            $searchUrl = $this->search_zd_id . $searchVars['jq_term1']. '&bibid=' . $this->bibID . '&lang=' . $this->lang . '&xmloutput=1';

            return $searchUrl;
        }else{
            $searchUrl = $this->search_result_page . 'bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang . 
                            '&xmlv=3';
        }

        //falls jemand kein utf-8 verwendet, sollte das nicht gemacht werden
        if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
            $term = utf8_decode($term);
        }

        // urlencode termi
        $term = rawurlencode($term);

        if (!$searchVars['sc']) {
            $searchVars['sc'] = 'A';
        }

        foreach ($searchVars as $var => $values) {

            if (!is_array($values)) {
                //falls jemand kein utf-8 verwendet, sollte das nicht gemacht werden
                if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
                    $values = utf8_decode($values);
                }
                $searchUrl .= '&' . $var . '=' . urlencode($values);
            } else {
                foreach ($values as $value) {
                    if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
                        $value = utf8_decode($value);
                    }
                    $searchUrl .= '&' . $var . '[]=' . urlencode($value);
                }
            }
        }

        return $searchUrl;
    }

    /**
     * Suche durchführen
     *
     * @param string Suchstring
     *
     * @return array
     */
    public function search($term, $searchVars = array()) {
        $searchUrl = str_replace(' ', '', $this->createSearchUrl($term, $searchVars));
        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($searchUrl);

        if (!$xml_request) {
            return FALSE;
        }
        $i = 0;
        $result = array('page_vars');
        foreach ($xml_request->page_vars->children() AS $key => $value) {
            $result = array('page_vars' => array($key => (string) $value->attributes()->value));
        }

        foreach ($xml_request->page_vars->children() AS $key => $value) {
            $result['page_vars'][$key] = (string) $value->attributes()->value;
        }

        //Zur Bearbeitung einer Suchweiterleitung aus der ursprünglichen EZB-Ansicht
        if(isset($xml_request->ezb_alphabetical_list)){
	
            if (isset($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal)) {
                foreach ($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal AS $key => $value) {
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
                    $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
                }
	
                //Anzahl Suchergebnisse
                $result['page_vars']['search_count'] = count($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal);
            }

            return $result;
        }

        //precise hits
        if (isset($xml_request->ezb_alphabetical_list_searchresult->precise_hits->journals->journal)) {
            foreach ($xml_request->ezb_alphabetical_list_searchresult->precise_hits->journals->journal AS $key => $precise_hit) {
                $result['precise_hits'][] = array(
                    'jourid' => (string) $precise_hit->attributes()->jourid,
                    'title' => (string) $precise_hit->title,
                    'color_code' => (int) $precise_hit->journal_color->attributes()->color_code,
                    'color' => (int) $precise_hit->journal_color->attributes()->color
                );
            }
        }

        //count Hits
        $result['page_vars']['search_count'] = (int) $xml_request->ezb_alphabetical_list_searchresult->search_count;

        if (isset($xml_request->ezb_alphabetical_list_searchresult->navlist->other_pages)) {
            foreach ($xml_request->ezb_alphabetical_list_searchresult->navlist->other_pages AS $key2 => $value2) {
                foreach ($value2->attributes() AS $key3 => $value3) {
                    $result['navlist']['pages'][(string) $value3] = array(
                        'id' => (string) $value3,
                        'title' => (string) $value2
                    );
                }
            }
        }
        $current_page = (string) $xml_request->ezb_alphabetical_list_searchresult->navlist->current_page;

        if ($current_page) {
            $result['navlist']['pages'][$current_page] = $current_page;
        }
        if (is_array($result['navlist']['pages'])) {
            ksort($result['navlist']['pages']);
        }

        if ($xml_request->ezb_alphabetical_list_searchresult->current_title) {
            $result['alphabetical_order']['current_title'] = (string) $xml_request->ezb_alphabetical_list_searchresult->current_title;
        }

        if (isset($xml_request->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal)) {
            foreach ($xml_request->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal AS $key => $value) {
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
                $result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
            }
        }
        $i = 0;
        foreach ($xml_request->ezb_alphabetical_list_searchresult->next_fifty AS $key => $value) {
            $result['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
            $result['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
            $result['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
            $i++;
        }

        $i = 0;
        foreach ($xml_request->ezb_alphabetical_list_searchresult->first_fifty AS $key => $value) {
            $result['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
            $result['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
            $result['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
            $i++;
        }

        return $result;
    }

    /**
     * setzt kurze Zugriffsinformationen
     */
    public function setShortAccessInfos() {
        $this->shortAccessInfos = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezbshortaccessinfos.'][$this->lang.'.'];
    }

    /**
     * liefert kurze Zugriffsinformationen
     * 
     * @return array $return
     */
    public function getShortAccessInfos(){
        $this->setShortAccessInfos();

        $return = array('shortAccessInfos' => $this->shortAccessInfos);

        return $return;
    }

    /**
     * setzt umfangreiche Zugriffsinformationen
     */
    public function setLongAccessInfos() {
        $this->longAccessInfos = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezblongaccessinfos.'][$this->lang.'.'];
    }

    /**
     * liefert umfangreiche Zugriffsinformationen
     * 
     * @return array $return
     */
    public function getLongAccessInfos(){
        $this->setLongAccessInfos();

        $return = array('longAccessInfos' => $this->longAccessInfos, 'force' => $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezblongaccessinfos.']['force']);

        return $return;
    }

    /**
     * returns List where Journal at Partners available
     * 
     * @param type $jour_id
     * @return array full list of Partner with Journal
     */
    public function getParticipantsList($jour_id){
        $participants_xml_request = $this->XMLPageConnection->getDataFromXMLPage($this->participants_xml_url.'&bibid=' . $this->bibID.'&jour_id='.$jour_id);

        $participantsList = array();

        if (!$participants_xml_request) {
            return FALSE;
        }

        if ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->countries->country){
            foreach ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->countries->country as $country){   
                $participantsList['countries'][(string) $country->attributes()->ID] = (string) $country[0];
            }

            foreach ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->categories->category as $category){   
                $participantsList['categories'][(string) $category->attributes()->ID]['countryrefs'] = (string)$category->attributes()->countryrefs;
                $participantsList['categories'][(string) $category->attributes()->ID]['category_name'] = (string)$category->category_name;
            }

            foreach ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->institutions->institution  as $institution ){   
                $participantsList['institutions'][(string) $institution->attributes()->ID]['catrefs'] = (string)$institution->attributes()->catrefs;
                $participantsList['institutions'][(string) $institution->attributes()->ID]['countryref'] = (string)$institution->attributes()->countryref;
                $participantsList['institutions'][(string) $institution->attributes()->ID]['name'] = (string) $institution->name;
                $participantsList['institutions'][(string) $institution->attributes()->ID]['city'] = (string) $institution->city;
            }
        }

        return $participantsList; 
    }

    /**
     * set colors
     * 
     * @param int $colors sum of colors
     */
    public function setColors($colors){
        $this->colors = $colors;
    }
    
    /**
     * get colors
     * 
     * @param int $colors sum of colors
     */
    public function getColors(){
        return $this->colors;
    }

    /**
     * check institutions having access to this journal
     * 
     * @param type $jour_id
     * @return boolean
     */
    public function getParticipants($jour_id){
        $participants = FALSE;

        $participants_xml_request = $this->XMLPageConnection->getDataFromXMLPage("{$this->participants_xml_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$jour_id}");

        if ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->institutions->institution){
            foreach ($participants_xml_request->ezb_where_journal_at_partners->partner_selection->institutions->institution->children() as $childs)  {
                $participants = "{$this->participants_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$jour_id}";
                break;
            }
        }

        return $participants;
    }

    /**
     * returns detailview_request_url
     * 
     * @return string
     */
    public function getDetailviewRequestUrl(){
        $url = $this->detailview_request_url . $this->colors .'&lang='. $this->lang;

        return $url;
    }

    public function getContact(){
        $contact = FALSE;

        $contact_xml_request = $this->XMLPageConnection->getDataFromXMLPage("{$this->contact_url}bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&xmloutput=1");

        if ($contact_xml_request->library ){
            $contact['library']['library'] = (string)$contact_xml_request->library;
            $contact['library']['www'] = (string)$contact_xml_request->library ->attributes()->href;
        }

        if ($contact_xml_request->ezb_contact){
            $contact['email'] = (string)$contact_xml_request->ezb_contact->attributes()->href;
            $contact['person'] = (string)$contact_xml_request->ezb_contact;
        }

        return $contact;
    }
    
    /**
     * sets the movin wall
     * 
     * @param string $value
     * @return array
     */
    private function getMovingwall($value){
        $value = trim($value);

        //prefix
        $prefixTemp = rtrim(rtrim($value, "YMDIV"), "0123456789");
        if($prefixTemp == "-"){
            $prefix = "not";
        }  else {
            $prefix = "yes";
        }

        //number
        $number = (int)rtrim(ltrim($value, "+-"), "YMDIV");

        //duration
        $duration = strtolower (ltrim(ltrim($value, "+-"), "0123456789"));

        $moving_wall = array('prefix' => array($prefix => TRUE), 'number' => $number, 'duration' => array($duration => TRUE));

        return $moving_wall;
    }

    /**
     * set language
     */
    private function setLanguage(){
        //get current language
        $lang = $GLOBALS['TSFE']->config['config']['language'];

        //only de and en is allowed
        if(($lang != 'de') && ($lang != 'en')){
            $lang = 'de';
        }

        $this->lang = $lang;
    }
}
?>