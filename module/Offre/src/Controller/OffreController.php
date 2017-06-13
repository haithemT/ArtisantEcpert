<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Offre\Controller;

use Offre\Model\OffreTable;
use Offre\Form\OffreForm;
use Offre\Model\Offre;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OffreController extends AbstractActionController
{
    private $table;

    public function __construct(PrestationTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'offres' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new OffreForm();
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $offre = new Offre();

        $form->setInputFilter($offre->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $offre->exchangeArray($form->getData());       
        $this->table->saveOffre($offre);
        return $this->redirect()->toRoute('offre');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('offre', ['action' => 'add']);
        }

        // Retrieve the prestation with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $offre = $this->table->getOffre($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('offre', ['action' => 'index']);
        }

        $form = new OffreForm();
        $form->bind($offre);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($prestation->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $this->table->saveOffre($offre);

        // Redirect to prestation list
        return $this->redirect()->toRoute('offre', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('offre');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getOffre('id');
                $this->table->deletePOffre($id);
            }

            // Redirect to list of offre
            return $this->redirect()->toRoute('offre');
        }

        return [
            'id'    => $id,
            'offre' => $this->table->getOffre($id),
        ];
    }
}
