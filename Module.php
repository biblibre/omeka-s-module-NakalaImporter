<?php

namespace NakalaImporter;

use Omeka\Module\AbstractModule;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
        CREATE TABLE nakala_importer_import (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, created DATETIME NOT NULL, collections_imported LONGTEXT NOT NULL COMMENT '(DC2Type:json)', UNIQUE INDEX UNIQ_82462CAEBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ALTER TABLE nakala_importer_import ADD CONSTRAINT FK_82462CAEBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id);
        SQL;

        $sqls = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($sqls as $sql) {
            $connection->exec($sql);
        }
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec('DROP TABLE IF EXISTS nakala_importer_import');
    }

    public function getConfigForm($renderer)
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');

        $form = $formElementManager->get('NakalaImporter\Form\ConfigForm');
        $form->setData([
            'api_key' => $settings->get('nakalaimporter_api_key', ''),
        ]);

        return $renderer->formCollection($form, false);
    }

    public function handleConfigForm($controller)
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');

        $form = $formElementManager->get('NakalaImporter\Form\ConfigForm');
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }

        $formData = $form->getData();
        $settings->set('nakalaimporter_api_key', $formData['api_key']);

        return true;
    }
}
