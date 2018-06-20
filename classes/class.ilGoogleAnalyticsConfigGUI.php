<?php

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

/**
 * Example user interface plugin
 *
 * @author Stefan Born <stefan.born@phzh.ch>
 *
 */
class ilGoogleAnalyticsConfigGUI extends ilPluginConfigGUI {

	/**
	 * Handles all commmands, default is 'configure'
	 *
	 * @access public
	 */
	public function performCommand($cmd) {
		switch ($cmd) {
			case 'configure':
			case 'save':
				$this->$cmd();
				break;
		}
	}


	/**
	 * Configure screen
	 *
	 * @access public
	 */
	public function configure() {
		global $DIC;

		/**
		 * @var $plugin ilGoogleAnalyticsPlugin
		 */
		$plugin = $this->getPluginObject();
		$form = $this->initConfigurationForm($plugin);

		// get binary
		$account_id = $plugin->getAccountId();
		if ($account_id == NULL) {
			ilUtil::sendFailure($plugin->txt("warning_no_account_id"));
		}

		// set all plugin settings values
		$val = array();
		$val["account_id"] = $account_id;
		$val["anonymize_ip"] = $plugin->getAnonymizeIp();
		$val["track_downloads"] = $plugin->getTrackDownloads();
		$form->setValuesByArray($val);

		$DIC->ui()->mainTemplate()->setContent($form->getHTML());
	}


	/**
	 * Save form input
	 *
	 */
	public function save() {
		global $DIC;

		/**
		 * @var $plugin ilGoogleAnalyticsPlugin
		 */
		$plugin = $this->getPluginObject();
		$form = $this->initConfigurationForm($plugin);

		if ($form->checkInput()) {
			$plugin->setAccountId($_POST["account_id"]);
			$plugin->setAnonymizeIp($_POST["anonymize_ip"]);
			$plugin->setTrackDownloads($_POST["track_downloads"]);

			ilUtil::sendSuccess($DIC->language()->txt("saved_successfully"), true);
			$DIC->ctrl()->redirect($this, "configure");
		} else {
			$form->setValuesByPost();
			$DIC->ui()->mainTemplate()->setContent($form->getHtml());
		}
	}


	/**
	 * Init configuration form.
	 *
	 * @param $plugin ilGoogleAnalyticsPlugin
	 *
	 * @return object form object
	 * @access public
	 */
	private function initConfigurationForm($plugin) {
		global $DIC;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setTableWidth("100%");
		$form->setTitle($plugin->txt("plugin_configuration"));
		$form->setFormAction($DIC->ctrl()->getFormAction($this));

		// account id
		$input = new ilTextInputGUI($plugin->txt("account_id"), "account_id");
		$input->setRequired(true);
		$input->setValue($plugin->getAccountId());
		$input->setInfo($plugin->txt("account_id_info"));
		$form->addItem($input);

		// anonymize ip
		$input = new ilCheckboxInputGUI($plugin->txt("anonymize_ip"), "anonymize_ip");
		$input->setValue("1");
		$input->setChecked($plugin->getAnonymizeIp());
		$input->setInfo($plugin->txt("anonymize_ip_info"));
		$form->addItem($input);

		// track downloads
		$input = new ilCheckboxInputGUI($plugin->txt("track_downloads"), "track_downloads");
		$input->setValue("1");
		$input->setChecked($plugin->getTrackDownloads());
		$input->setInfo($plugin->txt("track_downloads_info"));
		$form->addItem($input);

		$form->addCommandButton("save", $DIC->language()->txt("save"));

		return $form;
	}
}

?>