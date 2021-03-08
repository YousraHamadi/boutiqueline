<?php

namespace CreativeMail\Modules\Contacts\Handlers;

define('CE4WP_NF_EVENTTYPE', 'WordPress - NinjaForms');

use CreativeMail\Managers\RaygunManager;
use CreativeMail\Modules\Contacts\Models\ContactModel;
use CreativeMail\Modules\Contacts\Models\OptActionBy;

class NinjaFormsPluginHandler extends BaseContactFormPluginHandler
{
    function __construct()
    {
        parent::__construct();
    }

    private function getEmailFromForm($fields)
    {
        foreach ($fields as $field) {
            if ($field["key"] == "email" || $field["type"] == "email") {
                return $field["value"];
            }
        }
        return null;
    }

    private function getNameFromForm($fields)
    {
        $name = null;
        foreach ($fields as $field) {
            if ($field["key"] == "name" || $field["type"] == "name" || strpos($field["key"], "full_name") !== false) {
                return $field["value"];
            }
            if ($field["type"] == "firstname" || strpos($field["key"], "first_name") !== false) {
                $name = $field["value"];
                continue;
            }
            if ($field["type"] == "lastname" || strpos($field["key"], "last_name") !== false) {
                return implode(' ', array($name, $field["value"]));
            }
        }
        return !empty($name) ? $name : null;
    }

    public function convertToContactModel($contact)
    {
        $contactModel = new ContactModel();

        $contactModel->setEventType(CE4WP_NF_EVENTTYPE);
        //optin true on sync, false on form submission
        $contactModel->setOptIn($contact->opt_in);
        $contactModel->setOptOut(false);
        $contactModel->setOptActionBy(OptActionBy::Owner);

        if (isset($contact->optinByOwner)) {
            $contactModel->setOptIn(boolval($contact->optinByOwner));
        }

        $email = $contact->email;
        if (!empty($email)) {
            $contactModel->setEmail($email);
        }

        $name = !empty($contact->name) ? $contact->name : null;
        $firstName = null;
        $lastName = null;
        if (!empty($name)) {
            $values = explode(' ', $contact->name);
            $firstName = array_shift($values);
            $lastName = implode(' ', $values);
        } else {
            $firstName = isset($contact->firstName) ? $contact->firstName : null;
            $lastName = isset($contact->lastName) ? $contact->lastName : null;
        }

        if (!empty($firstName)) {
            $contactModel->setFirstName($firstName);
        }
        if (!empty($lastName)) {
            $contactModel->setLastName($lastName);
        }

        return $contactModel;
    }

    public function attemptAdditionalNameExtraction($contact, $field_key, $field_values)
    {
        //Attempt additional checking for name in an attempt to get custom form fields for names
        $name = null;
        if (strpos($field_key, "full_name") !== false || isset($field_values["name"])) {
            $contact->name = $field_values[$field_key];
            return;
        }
        if (strpos($field_key, "first_name") !== false || isset($field_values["firstname"])) {
            $contact->firstName = $field_values[$field_key];
            return;
        }
        if (strpos($field_key, "last_name") !== false || isset($field_values["lastname"])) {
            $contact->lastname = $field_values[$field_key];
            return;
        }
    }

    public function ceHandleNinjaFormSubmission($form_data)
    {
        try {
            $ninjaContact = new \stdClass();
            $ninjaContact->email = $this->getEmailFromForm($form_data["fields_by_key"]);

            if (empty($ninjaContact->email)) {
                return;
            };
            $ninjaContact->name = $this->getNameFromForm($form_data["fields_by_key"]);
            $ninjaContact->opt_in = false;
            $this->upsertContact($this->convertToContactModel($ninjaContact));
        } catch (\Exception $exception) {
            RaygunManager::get_instance()->exception_handler($exception);
        }
    }

    public function registerHooks()
    {
        add_action('ninja_forms_after_submission', array($this, 'ceHandleNinjaFormSubmission'), 10, 1);
        // add hook function to synchronize
        add_action(CE4WP_SYNCHRONIZE_ACTION, array($this, 'syncAction'));
    }

    public function unregisterHooks()
    {
        remove_action('ninja_forms_after_submission', array($this, 'ceHandleNinjaFormSubmission'));
        // remove hook function to synchronize
        remove_action(CE4WP_SYNCHRONIZE_ACTION, array($this, 'syncAction'));
    }

    public function syncAction($limit = null)
    {
        if (!is_int($limit) || $limit <= 0) {
            $limit = null;
        }
        try {
            // Relies on plugin => NinjaForms
            if (in_array('ninja-forms/ninja-forms.php', apply_filters('active_plugins', get_option('active_plugins')))) {

                $contactsArray = array();

                // Get an array of Form Models for All Forms
                $forms = Ninja_Forms()->form()->get_forms();
                foreach ($forms as $form) {
                    $formId = $form->get_id();
                    // Get all form fields and submissions for the form
                    $fields = Ninja_Forms()->form($formId)->get_fields();
                    $submissions = Ninja_Forms()->form($formId)->get_subs();
                    foreach ($submissions as $submission) {
                        $contact = new \stdClass();
                        // Get all values for a submission
                        $field_values = $submission->get_field_values();
                        foreach ($fields as $field) {
                            // Get field settings so we can map the values with it's field type
                            $field_settings = $field->get_settings();
                            $field_key = $field_settings["key"];
                            $field_type = $field_settings["type"];

                            switch ($field_type) {
                                case 'email';
                                    $email = isset($field_values[$field_key]) ? $field_values[$field_key] : null;
                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $contact->email = $email;
                                    }
                                    break;
                                case 'name';
                                case 'full_name';
                                    $contact->name = $field_values[$field_key];
                                    break;
                                case 'firstname';
                                case 'first_name';
                                    $contact->firstName = $field_values[$field_key];
                                    break;
                                case 'lastname';
                                case 'last_name';
                                    $contact->lastname = $field_values[$field_key];
                                    break;
                                case 'textbox';
                                case 'text';
                                    if (empty($contact->name) && (empty($contact->firstName) || empty($contact->lastName))) {
                                        $this->attemptAdditionalNameExtraction($contact, $field_key, $field_values);
                                    }
                                    break;
                                default;
                                    break;
                            }
                        }

                        if (!empty($contact->email) && $contact->email != null) {
                            //set optin by owner on db sync
                            $contact->optinByOwner = true;


                            //Convert to contactModel and push to the array
                            $contactModel = null;
                            try {
                                $contactModel = $this->convertToContactModel($contact);
                                if (!empty($contactModel->getEmail())) {
                                    array_push($contactsArray, $contactModel);
                                }
                            } catch (\Exception $exception) {
                                RaygunManager::get_instance()->exception_handler($exception);
                                continue;
                            }
                            if (isset($limit) && count($contactsArray) >= $limit) {
                                break;
                            }
                        }
                    }
                    if (isset($limit) && count($contactsArray) >= $limit) {
                        break;
                    }
                }

                //upsert the contacts
                if (!empty($contactsArray)) {
                    $batches = array_chunk($contactsArray, CE4WP_BATCH_SIZE);
                    foreach ($batches as $batch) {
                        $this->batchUpsertContacts($batch);
                    }
                }
            }
        } catch (\Exception $exception) {
            RaygunManager::get_instance()->exception_handler($exception);
        }
    }
}
