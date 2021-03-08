<?php

namespace CreativeMail\Modules\Contacts\Handlers;

define('CE4WP_EL_EVENTTYPE', 'WordPress - Elementor');

use CreativeMail\Managers\RaygunManager;
use CreativeMail\Modules\Contacts\Models\ContactModel;
use CreativeMail\Modules\Contacts\Models\OptActionBy;

class ElementorPluginHandler extends BaseContactFormPluginHandler
{
    function __construct()
    {
        parent::__construct();
    }

    private function GetNameFromForm($fields)
    {
        foreach ($fields as $field) {
            //Try to find a name value based on the default Elementor form with name field
            if(array_key_exists('type', $field) && ($field['type'] === "text" && $field['id'] === "name")) {
                return $field["value"];
            }
        }
        return null;
    }

    private function GetEmailFromForm($fields)
    {
        foreach ($fields as $field) {
            if(array_key_exists('type', $field) && $field['type'] === "email") {
                return $field["value"];
            }
        }
        return null;
    }

    public function convertToContactModel($contact)
    {
        $contactModel = new ContactModel();

        $contactModel->setEventType(CE4WP_EL_EVENTTYPE);

        $contactModel->setOptIn(false);
        $contactModel->setOptOut(false);
        $contactModel->setOptActionBy(OptActionBy::Owner);

        $email = $contact->email;
        if (!empty($email)) {
            $contactModel->setEmail($email);
        }

        $values = explode(' ', $contact->name);
        $firstName = array_shift($values);
        $lastName = implode(' ', $values);

        if (!empty($firstName)) {
            $contactModel->setFirstName($firstName);
        }
        if (!empty($lastName)) {
            $contactModel->setLastName($lastName);
        }

        return $contactModel;
    }

    public function ceHandleElementorFormSubmission($settings, $record)
    {
        try {
            $fields = $record->get("fields");
            $elemContact = new \stdClass();
            $elemContact->name = $this->GetNameFromForm($fields);
            $elemContact->email = $this->GetEmailFromForm($fields);
            if (empty($elemContact->email)) {
                return;
            };
            $this->upsertContact($this->convertToContactModel($elemContact));
        } catch (\Exception $exception) {
            RaygunManager::get_instance()->exception_handler($exception);
        }
    }

    public function registerHooks()
    {
        add_action('elementor_pro/forms/mail_sent', array($this, 'ceHandleElementorFormSubmission'), 10, 2);
        // add hook function to synchronize
        add_action(CE4WP_SYNCHRONIZE_ACTION, array($this, 'syncAction'));
    }

    public function unregisterHooks()
    {
        remove_action('elementor_pro/forms/mail_sent', array($this, 'ceHandleElementorFormSubmission'));
        // remove hook function to synchronize
        remove_action(CE4WP_SYNCHRONIZE_ACTION, array($this, 'syncAction'));
    }

    public function syncAction($limit = null)
    {
        //Elementor seems to not store form submissions locally
    }
}
