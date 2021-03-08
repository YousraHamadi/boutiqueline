<?php

namespace CreativeMail\Modules\Contacts\Handlers;

use CreativeMail\Modules\Contacts\Services\ContactsSyncService;
use Exception;

abstract class BaseContactFormPluginHandler
{
    private $contactSyncService;

    public abstract function convertToContactModel($contactForm);
    public abstract function registerHooks();
    public abstract function unregisterHooks();
    public abstract function syncAction($limit = null);

    public function upsertContact($model)
    {

        if (!isset($model)) {
            throw new Exception('No model provided');
        }

        $contactModel = null;
        if (!is_a($model, 'CreativeMail\Modules\Contacts\Models\ContactModel')) {
            $contactModel = $this->convertToContactModel($model);
        }
        else {
            $contactModel = $model;
        }

        $this->contactSyncService->upsertContact($contactModel);
    }

    public function batchUpsertContacts($models)
    {
        if (!isset($models)) {
            throw new Exception('No models provided');
        }

        $this->contactSyncService->upsertContacts($models);
    }

    protected function isNullOrEmpty($value)
    {
        return !isset($value) && empty($value);
    }

    function __construct()
    {
        $this->contactSyncService = new ContactsSyncService();
        $this->registerHooks();
    }

    function __destruct()
    {
        $this->unregisterHooks();
    }
}
