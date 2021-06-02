<?php

require_once 'CRM/Core/Page.php';

class CRM_discounter_Page_discounter extends CRM_Core_Page {

  function run() {
    CRM_Utils_System::setTitle(ts('Discounter'));
    $url = CRM_Utils_System::baseURL() . 'civicrm/ctrl/discounter';
    $this->assign('url', $url);
    $exclude = Civi::settings()->get('discounter-exclude');
    // On form action.
    if (isset($_REQUEST['discounterSettings']) && $_REQUEST['discounterSettings'] == 1) {
      $exclude = TRUE;
      Civi::settings()->set('discounter-exclude', $exclude);
    }
    if (isset($_REQUEST['discounterSettings']) && $_REQUEST['discounterSettings'] == 0) {
      $exclude = FALSE;
      Civi::settings()->set('discounter-exclude', $exclude);
    }
    // Build form.
    $form = "";
    $form .= "<form action=" . $url . " method='post'>";
    $form .= "<input type='hidden' name='discounter'>";
    if ($exclude) {
      $form .= "<input type='radio' id='ds_true' name='discounterSettings' value='1' checked><label for='ds_true'>Exclude discount input from event registration pages</label><br>";
      $form .= "<input type='radio' id='ds_false' name='discounterSettings' value='0'><label for='ds_false'>Include discount input from event registration pages</label><br>";
    }
    else {
      $form .= "<input type='radio' id='ds_true' name='discounterSettings' value='1'><label for='ds_true'>Exclude discount input from event registration pages</label><br>";
      $form .= "<input type='radio' id='ds_false' name='discounterSettings' value='0' checked><label for='ds_false'>Include discount input from event registration pages</label><br>";
    }
    $form .= "<div class='crm-submit-buttons'><span class='crm-button'><input class='crm-form-submit default' type='submit' value='Submit'></span></div>";
    $form .= "</form>";
    // Assign form.
    $this->assign('content', $form);
    // render.
    parent::run();
  }
}
