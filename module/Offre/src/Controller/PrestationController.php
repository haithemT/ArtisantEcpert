<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Offre\Controller;

use Offre\Model\PrestationTable;
use Offre\Form\PrestationForm;
use Offre\Model\Prestation;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PrestationController extends AbstractActionController
{
    private $table;

    public function __construct(PostTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'prestations' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new PrestationForm();
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $prestation = new Prestation();

        $form->setInputFilter($prestation->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $prestation->exchangeArray($form->getData());       
        $this->table->savePost($prestation);
        return $this->redirect()->toRoute('prestation');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('prestation', ['action' => 'add']);
        }

        // Retrieve the prestation with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $prestation = $this->table->getPrestation($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('prestation', ['action' => 'index']);
        }

        $form = new PrestationForm();
        $form->bind($prestation);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($post->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $this->table->savePrestation($prestation);

        // Redirect to prestation list
        return $this->redirect()->toRoute('prestation', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('prestation');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPrestation('id');
                $this->table->deletePrestation($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('prestation');
        }

        return [
            'id'    => $id,
            'prestation' => $this->table->getPrestation($id),
        ];
    }
}
