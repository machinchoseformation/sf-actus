<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\ImageType;
use AppBundle\Entity\Image;

/**
* @Route("/admin/media")
*/
class MediaController extends Controller
{
	/**
	* @Route("/image/televersement", name="upload_image")
	*/
	public function uploadImageAction(Request $request)
	{
		$image = new Image();
		$uploadForm = $this->createForm(new ImageType(), $image);

		$uploadForm->handleRequest($request);
		if ($uploadForm->isValid()){
			
			//récupère une instance de notre service
			$imageUploader = $this->get("image_uploader");

			$tmpFile = $image->getTmpFile();

			//génère un nouveau nom de fichier 
			$newFileName = $imageUploader->getSafeName( $tmpFile );

			//récupère les infos sur l'image avant de la déplacer
			$image->setSize( $tmpFile->getSize() );
			$image->setMimetype( $tmpFile->getMimeType() );
			$image->setFilename( $newFileName );
			$image->setDateUploaded( new \DateTime() );
			$image->setDateModified( new \DateTime() );

			//déplace le fichier temporaire vers le dossier web
			$pathToOriginal = $imageUploader->moveTmpFileToWeb( $tmpFile, $newFileName );

			//crée les miniatures
			$imageUploader->createSmallerImages($pathToOriginal, $newFileName);

			$em = $this->getDoctrine()->getManager();
			$em->persist($image);
			$em->flush();
		}

		$params = array(
			"uploadForm" => $uploadForm->createView()
		);
		return $this->render("media/upload_image.html.twig", $params);
	}

}