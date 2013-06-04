<?php
/**
 * Class BaseCommand
 * Version 1.1.0
 * Author: Deepak Dutta, http://www.eocene.net
 * Unrestricted license, subject to no modifcations to the line above.
 * Please include any modification history.
 * 10/01/2002 Initial creation.
 * 03/17/2003 Modified to adapt SmartWrapper
 * BaseCommand is the super class of all command classes.
 * It has methods to facilitate easy template handling.
 *
 * PUBLIC PROPERTIES
 *	var $templateEngine							An instance of the template engine
 * PUBLIC METHODS
 *	initialize()								createa the template and add vars: t_receiver, t_rootURL
 *	initFormVars(&$formVariables)				replaces all form variables (values obtained from $fc->request) in the template
 *	addVar($tokenName,&$theValue)				add a value of the replacement variable ($tokenName) in the template
 *	addLoop($loopName,$tokenName,&$theValue)	add value (an array of array) for loop processing
 *	addSelectLoop($loopName,$tokenName,$selectName,&$theValue)	A loop for html <option> construct. 
 *	addEmptyVar($tokenName)						does an addVar with an empty string
 *	addBlock($blockName)						adds a block to the template. By default all blocks are removed
 *  processTemplate($templateAlias)				processed the template aliased as $templateAlias
 *	processSuccess()							process the success template
 *	processFailure()							process the failure template
 *	&getContents($templateName,$includePath)	get the processed template. Subclasses should overwrite if there is no template
*/
class BaseCommand {
	var $templateEngine;

	function initBase(){
		$fc=&FrontController::instance();
		if(empty($fc->templateEngine)){
			$this->_initializeEoceneTemplateEngine();
			return;
		}
		
		$engineType=$fc->templateEngine;
		$engineType=strtoupper($engineType);
		switch($engineType){
			case "SMARTY":
				$fc->import('SmartyWrapper');
				$this->templateEngine=new SmartyWrapper();
				break;
			case "EOCENETEMPLATEENGINE":
				$this->_initializeEoceneTemplateEngine();
				break;
			default:
				$fc->writeError(100);
		}
	}
	
	function initFormVars(&$formVariables){
		$fc=&FrontController::instance();
		$size=count($formVariables);
		for($i=0;$i<$size;$i++){
			$v=$formVariables[$i];
			if(!isset($fc->request->$v))
				$fc->request->$v='';
			$this->addVar($v,$fc->request->$v);				
		}
	}
	
	function addVar($tokenName,&$theValue){
		$this->templateEngine->setVar($tokenName,$theValue);
	}
	
	function addEmptyVar($tokenName){
		$emptyString='';
		$this->addVar($tokenName,$emptyString);
	}
	
	function addBlock($blockName){
		$this->templateEngine->setBlock($blockName);
	}
	
	function addLoop($loopName,$tokenName,&$theValue){
		$theLoops=&$this->templateEngine->loops;
		if(isset($theLoops[$loopName])){
			$this->_addIntoExistingLoop($loopName,$tokenName,$theValue);
		}
		else{
			$this->_addIntoNewLoop($loopName,$tokenName,$theValue);
		}
	}
	
	function addSelectLoop($loopName,$tokenName,$selectName,&$theValue){
		$fc=&FrontController::instance();
		$size=count($theValue);
		$optionArray=array();
		$isSelected=!empty($fc->request->$selectName);
		if($isSelected){
			$selectedValue=$fc->request->$selectName;
			for($i=0;$i<$size;$i++){
				$arrayValue=$theValue[$i];
				if($selectedValue==$arrayValue)
					$optionArray[$i]="<option selected>".$arrayValue."</option>";
				else
					$optionArray[$i]="<option>".$arrayValue."</option>";
			}
		}
		else{
			$optionArray[0]="<option selected>".$theValue[0]."</option>";
			for($i=1;$i<$size;$i++)
				$optionArray[$i]="<option>".$theValue[$i]."</option>";		
		}
		$this->addLoop($loopName,$tokenName,$optionArray);
		unset($optionArray);
	}
	
	function addLoopUsingDBResults($loopName,$tokenName,$dbColumnName,&$dbResultArray){
		$size=count($dbResultArray);
		$loopValues=array();
		for($i=0;$i<$size;$i++)
			$loopValues[$i]=$dbResultArray[$i][$dbColumnName];
		$this->addLoop($loopName,$tokenName,$loopValues);
	}
	
	function processSuccess(){
		$this->processTemplate("success");
	}
	
	function processFailure(){
			$this->processTemplate("failure");
	}
	
	function processTemplate($templateAlias){
		$fc=&FrontController::instance();
		if(!isset($fc->templates[$templateAlias])) $fc->writeError(101,$templateAlias);
		$this->_processTemplate($fc->templates[$templateAlias]);
	}
	
	//Must initialize $template by calling initBase in sub class
	function &getContentsFromBaseCommand($templateName,$includePath){
		$this->templateEngine->process($templateName,$includePath);
		return $this->templateEngine->fileContents;		
	}
	
	function &getContents(){
		$fc=&FrontController::instance();
		$fc->writeError(102);
	}
	
	/******************************************************************************
	PRIVATE METHODS AND PROPERTIES
	*******************************************************************************/
	function execute(){
		$fc=&FrontController::instance();
		$c=&$fc->commandClass;
		$fc->writeError(103,$c,$c);
	}

	function _initializeEoceneTemplateEngine(){
		$fc=&FrontController::instance();
		$fc->import('TemplateEngine');
		$this->templateEngine=new TemplateEngine();
		$this->addVar("t_receiver",$fc->receivers);
		$this->addVar("t_rootURL",$fc->rootURL);	
	}

	function _addIntoExistingLoop($loopName,$tokenName,&$theValue){
		$theLoops=&$this->templateEngine->loops;
		$outerArray=&$theLoops[$loopName];
		$currentSize=count($outerArray);
		$incomingSize=count($theValue);
		if($incomingSize<=$currentSize) $size=$incomingSize;
		else $size=$currentSize;
		for($i=0;$i<$size;$i++){
			$innerArray=&$outerArray[$i];
			$innerArray[$tokenName]=$theValue[$i];
		}	
	}
	
	function _addIntoNewLoop($loopName,$tokenName,&$theValue){
		$outerArray=array();
		$size=count($theValue);
		for($i=0;$i<$size;$i++){
			$innerArray=array();
			$innerArray[$tokenName]=$theValue[$i];
			$outerArray[$i]=$innerArray;
		}
		$this->templateEngine->setLoop($loopName,$outerArray);
	}

	//Must initialize $template by calling initBase in sub class	
	function _processTemplate(&$templateName){
		$fc=&FrontController::instance();
		$this->templateEngine->process($templateName,$fc->paths['templates']);
		$fc->response->write($this->templateEngine->fileContents);
	}	
}
?>