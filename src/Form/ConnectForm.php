<?php

namespace NakalaImporter\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Select;

class ConnectForm extends Form
{
    public function init()
    {
        $this->setAttribute('action', 'nakala-importer/show-sets');

        $this->add([
            'name' => 'scope',
            'type' => Select::class,
            'options' => [
                'label' => 'Scope', // @translate
                'info' => 'See https://documentation.huma-num.fr/nakala/#tableau-recapitulatif-des-droits-par-role', // @translate
                'empty_option' => 'Select scope...', // @translate
                'value_options' => [
                    'deposited' => 'Deposited (ROLE_DEPOSITOR)', // @translate
                    'owned' => 'Owned (ROLE OWNER)', // @translate
                    'shared' => 'Shared (ROLE_ADMIN, ROLE_EDITOR or ROLE_READER or ROLE_READER but not ROLE_OWNER)', // @translate
                    'editable' => 'Editable (ROLE_OWNER, ROLE_ADMIN or ROLE_EDITOR)', // @translate
                    'readable' => 'Readable (ROLE_OWNER, ROLE_ADMIN, ROLE_EDITOR or ROLE_READER)', // @translate
                    'all' => 'All (ROLE_OWNER, ROLE_ADMIN, ROLE_EDITOR or ROLE_READER) and all publics sets', // @translate
                ],
            ],
            'attributes' => [
                'id' => 'scope',
                'required' => true,
            ],
        ]);
    }
}
