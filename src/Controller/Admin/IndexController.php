<?php
namespace NakalaImporter\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Stdlib\Message;
use NakalaImporter\Form\ConnectForm;
use NakalaImporter\Form\ListSetsForm;

class IndexController extends AbstractActionController
{
    protected $services;

    public function __construct($serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function indexAction()
    {
        $form = $this->getForm(ConnectForm::class);

        $view = new ViewModel();
        $view->setVariable('form', $form);

        return $view;
    }

    public function showSetsAction()
    {
        $view = new ViewModel;
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('admin/nakala-importer');
        }
        $params = $request->getPost()->toArray();
        $scope = $params['scope'];

        $nakalaSets = $this->getSets($scope);
        $form = $this->getForm(ListSetsForm::class);

        $view->setVariable('form', $form);
        if (isset($nakalaSets['http_error'])) {
            $view->setVariable('httpError', $nakalaSets['http_error']);
        } else {
            $view->setVariable('totalRecords', $nakalaSets['totalRecords']);
            $view->setVariable('sets', $nakalaSets['data']);
        }

        return $view;
    }

    protected function getSets($scope)
    {
        $apiClient = $this->services->get('NakalaImporter\ApiClient');
        $endpoint = "/users/collections/$scope";
        $sets = $apiClient->sendRequest($endpoint, 'POST', []);

        return $sets;
    }

    public function importAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('admin/nakala-importer');
        }
        $post = $request->getPost()->toArray();
        $args['selectedSets'] = $post['selectedSets'];

        $dispatcher = $this->jobDispatcher();
        $job = $dispatcher->dispatch('NakalaImporter\Job\ImportJob', $args);

        $message = new Message(
            'Importing in background (%sjob #%d%s)', // @translate
                sprintf(
                    '<a href="%s">',
                    htmlspecialchars($this->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()]))
                ),
            $job->getId(),
            '</a>'
        );
        $message->setEscapeHtml(false);
        $this->messenger()->addSuccess($message);
        return $this->redirect()->toRoute('admin/nakala-importer/past-imports');
    }

    public function pastImportsAction()
    {
        $view = new ViewModel;
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->params()->fromQuery() + [
            'page' => $page,
            'sort_by' => $this->params()->fromQuery('sort_by', 'id'),
            'sort_order' => $this->params()->fromQuery('sort_order', 'desc'),
        ];
        $response = $this->api()->search('nakala_importer_import', $query);
        $this->paginator($response->getTotalResults(), $page);
        $view->setVariable('imports', $response->getContent());
        return $view;
    }
}
