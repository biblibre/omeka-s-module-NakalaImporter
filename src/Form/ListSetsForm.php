<?php

namespace NakalaImporter\Form;

use Laminas\Form\Form;

class ListSetsForm extends Form
{
    public function init()
    {
        $this->setAttribute('action', 'import');
    }
}
