<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: FormFirstWebsiteSetup.php 1609 2009-11-26 09:45:59Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_Installation
 */

/**
 * 
 * @package Piwik_Installation
 */
class Piwik_Installation_FormFirstWebsiteSetup extends Piwik_Form
{
	function init()
	{
		$urlToGoAfter = 'index.php' . Piwik_Url::getCurrentQueryString();

		$urlExample = 'http://example.org';
		$javascriptOnClickUrlExample = "\"javascript:if(this.value=='$urlExample'){this.value='http://';} this.style.color='black';\"";
		
		$formElements = array(
			array('text', 'siteName', Piwik_Translate('Installation_SetupWebSiteName')),
			array('text', 'url', Piwik_Translate('Installation_SetupWebSiteURL'), "style='color:rgb(153, 153, 153);' value=$urlExample onfocus=".$javascriptOnClickUrlExample." onclick=".$javascriptOnClickUrlExample),
		);
		$this->addElements( $formElements );
		
		$formRules = array();
		foreach($formElements as $row)
		{
			$formRules[] = array($row[1], Piwik_Translate('General_Required', $row[2]), 'required');
		}
		
		$this->addRules( $formRules );	
		
		$this->addElement('submit', 'submit', Piwik_Translate('Installation_SubmitGo'));
	}	
}
