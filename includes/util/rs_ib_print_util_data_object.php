<?php
/*
 * Indiebooking - the Booking Software for your Homepage!
 * Copyright (C) 2016  ReWa Soft GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */
?>
<?php if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}
// if ( ! class_exists( 'rs_ib_print_util_data_object' ) ) :
/**
 * @author schmitt
 *
 */

class rs_ib_print_util_data_object {
	/*
	 * Allgemein
	 */
	private $waehrung;
	private $fileName;
	private $ueberschrift;
	private $createNewVersion = true;
	private $buchungsNrToCopy = 0;
	private $firstBillPrint = false;
	
	/*
	 * Buchungsdaten
	 */
	private $buchungNr;
	private $buchungsKopf;
	private $oberBuchungskopf;
	
	/*
	 * Firmendaten
	 */
	private $companyName;
	private $companyStreet;
	private $company_zip_code;
	private $companyLocation;
	
	/*
	 * Kontaktdaten
	 */
	private $firma;
	private $abteilung;
	private $anrede;
	private $titel;
	private $vorname;
	private $nachname;
	private $name;
	private $strasse;
	private $hausNr;
	private $plz;
	private $ort;
	private $land;
	
	
	public function setContactDataFromArray($contact) {
		/* @var $buchungObj RS_IB_Model_Appartment_Buchung */
		/* @var $aktion RS_IB_Model_Appartmentaktion */
		/* @var $position RS_IB_Buchungsposition */
		/* @var $optionPositions RS_IB_Buchungsposition */
		/* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
		$useAdress2 	= (array_key_exists('useAdress2', $contact) ? $contact['useAdress2'] : "0");
		if ($useAdress2 == false || $useAdress2 == 0 || $useAdress2 == "0") {
			$firma      = (array_key_exists('firma', $contact)      ? $contact['firma']      : "");
			$abteilung  = (array_key_exists('abteilung', $contact)  ? $contact['abteilung']  : "");
			$anrede     = (array_key_exists('anrede', $contact)     ? $contact['anrede']     : "");
			$titel      = (array_key_exists('title', $contact)      ? $contact['title']      : "");
			$anrede     = $anrede." ".$titel;
			 
			$vorname    = (array_key_exists('firstName', $contact)  ? $contact['firstName']  : "");
			$nachname   = (array_key_exists('name', $contact)       ? $contact['name']       : "");
			$name       = $vorname." ".$nachname;
			 
			$strasse    = (array_key_exists('strasse', $contact)    ? $contact['strasse']    : "");
			$hausNr     = (array_key_exists('strasseNr', $contact)  ? $contact['strasseNr']  : "");
			$strasse    = $strasse." ".$hausNr;
			 
			$plz        = (array_key_exists('plz', $contact)        ? $contact['plz']        : "");
			$ort        = (array_key_exists('ort', $contact)        ? $contact['ort']        : "");
			$land       = (array_key_exists('country', $contact)    ? $contact['country']    : "");
		} else {
			$firma      = (array_key_exists('firma2', $contact)     ? $contact['firma2']     : "");
			$abteilung  = (array_key_exists('abteilung2', $contact)  ? $contact['abteilung2']  : "");
			$anrede     = (array_key_exists('anrede2', $contact)    ? $contact['anrede2']    : "");
			$titel      = (array_key_exists('title2', $contact)     ? $contact['title2']     : "");
			$anrede     = $anrede." ".$titel;
			 
			$vorname    = (array_key_exists('firstName2', $contact) ? $contact['firstName2'] : "");
			$nachname   = (array_key_exists('name2', $contact)      ? $contact['name2']      : "");
			$name       = $vorname." ".$nachname;
			 
			$strasse    = (array_key_exists('strasse2', $contact)   ? $contact['strasse2']   : "");
			$hausNr     = (array_key_exists('strasseNr2', $contact) ? $contact['strasseNr2'] : "");
			$strasse    = $strasse." ".$hausNr;
			 
			$plz        = (array_key_exists('plz2', $contact)       ? $contact['plz2']       : "");
			$ort        = (array_key_exists('ort2', $contact)       ? $contact['ort2']       : "");
			$land       = (array_key_exists('country2', $contact)   ? $contact['country2']   : "");
		}
		
		$this->firma   	= $firma;
		$this->abteilung = $abteilung;
		$this->anrede  	= $anrede;
		$this->titel   	= $titel;
		$this->vorname 	= $vorname;
		$this->nachname	= $nachname;
		$this->name    	= $name;
		$this->strasse 	= $strasse;
		$this->hausNr  	= $hausNr;
		$this->plz     	= $plz;
		$this->ort     	= $ort;
		$this->land		= $land;
	}
	
	public function getBuchungNr() {
		return $this->buchungNr;
	}
	public function setBuchungNr($buchungNr) {
		$this->buchungNr = $buchungNr;
	}
	
	public function getCompanyName() {
		return $this->companyName;
	}
	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}
	
	public function getCompanyStreet() {
		return $this->companyStreet;
	}
	public function setCompanyStreet($companyStreet) {
		$this->companyStreet = $companyStreet;
	}
	
	public function getCompanyZipCode() {
		return $this->company_zip_code;
	}
	public function setCompanyZipCode($company_zip_code) {
		$this->company_zip_code = $company_zip_code;
	}
	
	public function getCompanyLocation() {
		return $this->companyLocation;
	}
	public function setCompanyLocation($companyLocation) {
		$this->companyLocation = $companyLocation;
	}
	
	public function getFirma() {
		return $this->firma;
	}
	public function setFirma($firma) {
		$this->firma = $firma;
	}
	
	public function getAnrede() {
		return $this->anrede;
	}
	public function setAnrede($anrede) {
		$this->anrede = $anrede;
	}
	
	public function getTitel() {
		return $this->titel;
	}
	public function setTitel($titel) {
		$this->titel = $titel;
	}
	
	public function getVorname() {
		return $this->vorname;
	}
	public function setVorname($vorname) {
		$this->vorname = $vorname;
	}
	
	public function getNachname() {
		return $this->nachname;
	}
	public function setNachname($nachname) {
		$this->nachname = $nachname;
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getStrasse() {
		return $this->strasse;
	}
	public function setStrasse($strasse) {
		$this->strasse = $strasse;
	}
	
	public function getHausNr() {
		return $this->hausNr;
	}
	public function setHausNr($hausNr) {
		$this->hausNr = $hausNr;
	}
	
	public function getPlz() {
		return $this->plz;
	}
	public function setPlz($plz) {
		$this->plz = $plz;
	}
	
	public function getOrt() {
		return $this->ort;
	}
	public function setOrt($ort) {
		$this->ort = $ort;
	}
	
	public function getLand() {
		return $this->land;
	}
	public function setLand($land) {
		$this->land = $land;
	}
	
	public function getBuchungsKopf() {
		return $this->buchungsKopf;
	}
	public function setBuchungsKopf($buchungsKopf) {
		$this->buchungsKopf = $buchungsKopf;
	}
	
	public function getOberBuchungskopf() {
		return $this->oberBuchungskopf;
	}
	public function setOberBuchungskopf($oberBuchungskopf) {
		$this->oberBuchungskopf = $oberBuchungskopf;
	}
	
	public function getWaehrung() {
		return $this->waehrung;
	}
	public function setWaehrung($waehrung) {
		$this->waehrung = $waehrung;
	}
	public function getFileName() {
		return $this->fileName;
	}
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}
	public function getUeberschrift() {
		return $this->ueberschrift;
	}
	public function setUeberschrift($ueberschrift) {
		$this->ueberschrift = $ueberschrift;
	}
	/**
	 * @return mixed
	 */
	public function getAbteilung()
	{
		return $this->abteilung;
	}

	/**
	 * @param mixed $abteilung
	 */
	public function setAbteilung($abteilung)
	{
		$this->abteilung = $abteilung;
	}
	/**
	 * @return boolean
	 */
	public function getCreateNewVersion()
	{
		if (!isset($this->createNewVersion) || is_null($this->createNewVersion)) {
			$this->createNewVersion = true;
		}
		return $this->createNewVersion;
	}

	/**
	 * @param boolean $createNewVersion
	 */
	public function setCreateNewVersion($createNewVersion)
	{
		$this->createNewVersion = $createNewVersion;
	}
	/**
	 * @return number
	 */
	public function getBuchungsNrToCopy()
	{
		return $this->buchungsNrToCopy;
	}

	/**
	 * @param number $buchungsNrToCopy
	 */
	public function setBuchungsNrToCopy($buchungsNrToCopy)
	{
		$this->buchungsNrToCopy = $buchungsNrToCopy;
	}
	/**
	 * @return boolean
	 */
	public function getFirstBillPrint()
	{
		return $this->firstBillPrint;
	}

	/**
	 * @param boolean $firstBillPrint
	 */
	public function setFirstBillPrint($firstBillPrint)
	{
		$this->firstBillPrint = $firstBillPrint;
	}




	
}
// endif;