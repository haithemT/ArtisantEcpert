<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License 
 */

namespace Poll\Controller;

use Poll\Model\PollTable;
use Event\Model\EventTable;
use Poll\Form\PollForm;
use Poll\Model\Poll;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PollController extends AbstractActionController
{
    private $table;
    private $eventTable;

    public function __construct(PollTable $table ,EventTable $eventTable)
    {
        $this->table        = $table;
        $this->eventTable   = $eventTable;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'polls' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new PollForm();
        $request = $this->getRequest();
        $pollStatusList = [
            'active'        => $this->translator()->translate('Active'),
            'archived'      => $this->translator()->translate('Archived'),
            'scheduled'     => $this->translator()->translate('Scheduled')
        ];
        $eventsList = $this->eventTable->fetchAll();
        $eventsListOptions = array_map(function($e){
            return array('value' => $e['id'], 'label' => $e['eventName']);
        }, $eventsList);

        $form->get('status')->setAttribute('options',$pollStatusList);
        $form->get('event')->setAttribute('options',$eventsListOptions);
        if (! $request->isPost()) {
            return ['form' => $form];
        }
        $poll = new Poll();

        $form->setInputFilter($poll->getInputFilter());
        $form->setData($request->getPost());
        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $poll->exchangeArray($form->getData());
   
        $poll->created_by=$this->identity()['id']; 
        $this->table->addPoll($poll);
        return $this->redirect()->toRoute('poll');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('poll', ['action' => 'add']);
        }

        // Retrieve the poll with the specified id. Doing so raises
        // an exception if the poll is not found, which should result
        // in redirecting to the landing page.
        try {
            $poll = $this->table->getPoll($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('poll', ['action' => 'index']);
        }

        $form = new PollForm();
        $form->bind($poll);
        $pollStatusList = [
            'active'        => $this->translator()->translate('Active'),
            'archived'      => $this->translator()->translate('Archived'),
            'scheduled'     => $this->translator()->translate('Scheduled')
        ];
        $eventsList = $this->eventTable->fetchAll();
        $eventsListOptions = array_map(function($e){
            return array('value' => $e['id'], 'label' => $e['eventName']);
        }, $eventsList);

        $form->get('status')->setAttribute('options',$pollStatusList);
        $form->get('event')->setAttribute('options',$eventsListOptions);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('status')->setValue($poll->status);
        $form->get('event')->setValue($poll->event_id);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form, 'response' => $poll->response];

        if ( ! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($poll->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $poll->updated_by=$this->identity()['id']; 
        $this->table->updatePoll($poll);

        // Redirect to polls list
        return $this->redirect()->toRoute('poll', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('poll');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPoll('id');
                $this->table->deletePoll($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('poll');
        }

        return [
            'id'    => $id,
            'poll' => $this->table->getPoll($id),
        ];
    }  
}
