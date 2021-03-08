<?php

namespace CreativeMail\Modules\Contacts\Services;

use CreativeMail\CreativeMail;
use CreativeMail\Modules\Api\Models\ApiRequestItem;
use CreativeMail\Modules\Contacts\Models\ContactModel;
use Exception;

class ContactsSyncService
{
    private function validate_email_address($emailAddress)
    {
        if (!isset($emailAddress) && empty($emailAddress)) {
            throw new Exception('No valid email address provided');
        }
    }

    private function ensure_event_type($eventType)
    {
        // DEV: For now, we only support WordPress.
        if (isset($eventType) && !empty($eventType)) {
            return $eventType;
        }

        return 'WordPress';
    }

    private function build_payload($contactModels)
    {
        $contacts = array();
        foreach ($contactModels as $model) {
            array_push($contacts, $model->toArray());
        }

        $data = array(
            "contacts" => $contacts
        );

        return wp_json_encode($data);
    }

    public function upsertContact(ContactModel $contactModel)
    {
        if(!isset($contactModel)) {
            return false;
        }

        $this->validate_email_address($contactModel->getEmail());
        $contactModel->setEventType($this->ensure_event_type($contactModel->getEventType()));

        $this->upsertContacts(array($contactModel), 1);

        return true;
    }

    public function upsertContacts($contactModels, $batchSize = 250)
    {

        $creativemail = CreativeMail::get_instance();

        // split in chunks of $batchSize
        $chunks = array_chunk($contactModels, $batchSize);
        foreach ($chunks as $chunk)
        {
            $jsonData = $this->build_payload($chunk);

            $creativemail->get_api_manager()->get_api_background_process()->push_to_queue(
                new ApiRequestItem(
                    'POST',
                    'application/json',
                    '/v1.0/contacts',
                    $jsonData
                )
            );
        }

        // Start the queue.
        $creativemail->get_api_manager()->get_api_background_process()->save()->dispatch();

        return true;
    }
}
