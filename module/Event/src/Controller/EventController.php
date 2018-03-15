<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License 
 */

namespace Event\Controller;

use Event\Model\EventTable;
use Event\Form\EventForm;
use Event\Model\Event;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EventController extends AbstractActionController
{
    private $table;
    private $imageService;

    public function __construct(EventTable $table, $imageService)
    {
        $this->table = $table;
        $this->imageService = $imageService;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'events' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new EventForm();
        $request = $this->getRequest();
        $eventStatusList = [
            'active'        => $this->translator()->translate('Active'),
            'archived'      => $this->translator()->translate('Archived'),
            'scheduled'     => $this->translator()->translate('Scheduled')
        ];
        $form->get('status')->setAttribute('options',$eventStatusList);
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
        } else {
            return ['form' => $form];
        }
        $event = new Event();

        $form->setInputFilter($event->getInputFilter());
        $form->setData($data);
        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $event->exchangeArray($form->getData());
        /**
         * 
         * SET CONNECTED USER IP
         * @todo get the real connected user ip not the proxied ip
         */
        $event->created_by=$this->identity()['id'];     
        $this->table->saveEvent($event);
        return $this->redirect()->toRoute('event');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('event', ['action' => 'add']);
        }

        // Retrieve the event with the specified id. Doing so raises
        // an exception if the event is not found, which should result
        // in redirecting to the landing page.
        try {
            $event = $this->table->getEvent($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('event', ['action' => 'index']);
        }

        $form = new EventForm();
        $form->bind($event);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('status')->setValue($event->status);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($event->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $event->updated_by=$this->identity()['id']; 
        $this->table->saveEvent($event);

        // Redirect to users list
        return $this->redirect()->toRoute('event', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('event');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteEvent($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('event');
        }

        return [
            'id'    => $id,
            'event' => $this->table->getEvent($id),
        ];
    }

    public function fileAction() 
    {
        // Get the file name from GET variable.
        $fileName = $this->params()->fromQuery('name', '');

        // Check whether the user needs a thumbnail or a full-size image.
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
    
        // Get path to image file.
        $fileName = $this->imageService->getImagePathByName($fileName);
        
        if($isThumbnail) {
        
            // Resize the image.
            $fileName = $this->imageService->resizeImage($fileName);
        }
                
        // Get image file info (size and MIME type).
        $fileInfo = $this->imageService->getImageFileInfo($fileName);        
        if ($fileInfo===false) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
                
        // Write HTTP headers.
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);        
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);
            
        // Write file content.
        $fileContent = $this->imageService->getImageFileContent($fileName);
        if($fileContent!==false) {                
            $response->setContent($fileContent);
        } else {        
            // Set 500 Server Error status code.
            $this->getResponse()->setStatusCode(500);
            return;
        }
        
        if($isThumbnail) {
            // Remove temporary thumbnail image file.
            unlink($fileName);
        }
        
        // Return Response to avoid default view rendering.
        return $this->getResponse();
    }    
}
