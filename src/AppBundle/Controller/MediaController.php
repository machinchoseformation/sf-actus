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
	* @Route("/image/televersement")
	*/
	public function uploadImageAction(Request $request)
	{
		$image = new Image();
		$uploadForm = $this->createForm(new ImageType(), $image);

		$uploadForm->handleRequest($request);

		if ($uploadForm->isValid()){
			dump($image);
			dump(get_class_methods($image->getTmpFile()));
		}

		$params = array(
			"uploadForm" => $uploadForm->createView()
		);
		return $this->render("media/upload_image.html.twig", $params);
	}

}