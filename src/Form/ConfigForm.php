<?php

namespace NakalaImporter\Form;

use Laminas\Form\Form;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'api_key',
            'type' => 'text',
            'options' => [
                'label' => 'API key', // @translate
            ],
            'attributes' => [
                'id' => 'api_key',
                'required' => true,
            ],
        ]);
    }
}
