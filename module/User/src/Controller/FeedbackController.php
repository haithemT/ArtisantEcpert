<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use User\Model\FeedbackTable;
use User\Form\FeedbackForm;
use User\Model\Feedback;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FeedbackController extends AbstractActionController
{
    private $table;

    public function __construct(FeedbackTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'feedbacks' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new FeedbackForm();
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $feedback = new Feedback();

        $form->setInputFilter($feedback->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $feedback->exchangeArray($form->getData());

        $feedback->user_id=$this->identity()['id'];        
        $this->table->saveFeedback($feedback);
        return $this->redirect()->toRoute('feedback');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('feedback', ['action' => 'add']);
        }

        // Retrieve the feedback with the specified id. Doing so raises
        // an exception if the feedback is not found, which should result
        // in redirecting to the landing page.
        try {
            $feedback = $this->table->getFeedback($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('feedback', ['action' => 'index']);
        }

        $form = new FeedbackForm();
        $form->bind($feedback);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($feedback->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $this->table->saveFeedback($feedback);

        // Redirect to feedbacks list
        return $this->redirect()->toRoute('feedback', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('feedback');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteFeedback($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('feedback');
        }

        return [
            'id'    => $id,
            'post' => $this->table->getFeedback($id),
        ];
    }
}
