<?php

require_once 'discounter.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function discounter_civicrm_config(&$config) {
  _discounter_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function discounter_civicrm_xmlMenu(&$files) {
  _discounter_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function discounter_civicrm_install() {
  _discounter_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function discounter_civicrm_postInstall() {
  _discounter_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function discounter_civicrm_uninstall() {
  _discounter_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function discounter_civicrm_enable() {
  _discounter_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function discounter_civicrm_disable() {
  _discounter_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function discounter_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _discounter_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function discounter_civicrm_managed(&$entities) {
  _discounter_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function discounter_civicrm_caseTypes(&$caseTypes) {
  _discounter_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function discounter_civicrm_angularModules(&$angularModules) {
  _discounter_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function discounter_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _discounter_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @param $content
 *   Previously generated content.
 * @param $context
 *   Context of content - page or form.
 * @param $tplName
 *   The file name of the tpl.
 * @param $object
 *   A reference to the page or form object.
 *
 */
function discounter_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  if ($context == 'form') {
    // Replace '-' with '=' for price amount label separator for memberships.
    if ($tplName == 'CRM/Contribute/Form/Contribution/Main.tpl') {
      $find = '<span class="crm-price-amount-label-separator">&nbsp;-&nbsp;</span>';
      $replace = '<span class="crm-price-amount-label-separator">&nbsp;=&nbsp;</span>';
      $content = str_replace($find, $replace, $content);
    }

    // Replace '-' with '=' for price amount label separator for events.
    if ($tplName == 'CRM/Event/Form/Registration/Register.tpl') {
      $find = '<span class="crm-price-amount-label-separator">&nbsp;-&nbsp;</span>';
      $replace = '<span class="crm-price-amount-label-separator">&nbsp;=&nbsp;</span>';
      $content = str_replace($find, $replace, $content);
    }
  }
}

/**
 * Implements hook_civicrm_buildAmount().
 *
 * Set a default value for an event price set field.
 *
 * @param string $pageType
 * @param CRM_Core_Form $form
 * @param array $amount
 *
 * @throws \CiviCRM_API3_Exception
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildAmount/
 */
function discounter_civicrm_buildAmount($pageType, &$form, &$amount) {

  // Alter options for 'contribution' pages only.
  if (get_class($form) == 'CRM_Contribute_Form_Contribution_Main') {
    // Alter options for 'membership' pages only.
    if ($pageType == 'membership') {
      // Fetch price_set(s).
      $fields = reset($amount);
      $fields_id = $fields['id'];
      $options = $amount[$fields_id]['options'];
      // Loop price_set(s).
      foreach ($options as $key => $option) {
        // Only alter when discount is applied.
        if (isset($option['discount_applied'])) {
          // Fetch default currency.
          $currency = civicrm_api3('Setting', 'getSingle', ['return' => ["defaultCurrency"]]);
          // Fetch price_field value.
          $pricefield = civicrm_api3('PriceFieldValue', 'getsingle', ['id' => $option['id']]);
          // Get discount label.
          $d_label = $option['discount_description'];
          // Set parameters.
          $d_amount = CRM_Utils_Money::format($option['discount_applied'], $currency['defaultCurrency']);
          $m_label = $pricefield['label'];
          $m_amount = CRM_Utils_Money::format($pricefield['amount'], $currency['defaultCurrency']);
          // Set new label.
          $amount[$fields_id]['options'][$key]['label'] = "$m_label ($m_amount) - $d_label ($d_amount)";
        }
      }
    }
  }

  // Alter options for 'event' pages only.
  if (get_class($form) == 'CRM_Event_Form_Registration_Register') {
    // Alter options for 'membership' pages only.
    if ($pageType == 'event') {
      // Fetch price_set(s).
      $fields = reset($amount);
      $fields_id = $fields['id'];
      $options = $amount[$fields_id]['options'];
      // Loop price_set(s).
      foreach ($options as $key => $option) {
        // Only alter when discount is applied.
        if (isset($option['discount_applied'])) {
          // Fetch default currency.
          $currency = civicrm_api3('Setting', 'getSingle', ['return' => ["defaultCurrency"]]);
          // Fetch price_field value.
          $pricefield = civicrm_api3('PriceFieldValue', 'getsingle', ['id' => $option['id']]);
          // Get discount label.
          $d_label = $option['discount_description'];
          // Set parameters.
          $d_amount = CRM_Utils_Money::format($option['discount_applied'], $currency['defaultCurrency']);
          $m_label = $pricefield['label'];
          $m_amount = CRM_Utils_Money::format($pricefield['amount'], $currency['defaultCurrency']);
          // Set new label.
          $amount[$fields_id]['options'][$key]['label'] = "$m_label ($m_amount) - $d_label ($d_amount)";
        }
      }
    }
  }
}
