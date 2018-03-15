<?php 

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ImageManagerController extends AbstractActionController
{
    private $imageService;

    public function __construct($imageService)
    {
        $this->imageService = $imageService;
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