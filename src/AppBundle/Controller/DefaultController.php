<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

	/**
	 * @Route("/{page}", 
	 * requirements={"page":"[1-9]\d*"}, 
	 * defaults={"page":1},
	 * name="home")
	 */
	public function homeAction($page)
	{
		//on récupére le repository de Story
		$storyRepo = $this->get("doctrine")->getRepository("AppBundle:Story");

		//récupère toutes les stories de la bdd
		$paginationResults = $storyRepo->findPaginated($page);

		if (!$paginationResults){
			throw $this->createNotFoundException();
		}

		//on va passer ces données à twig...
		$params = array(
			"paginationResults" => $paginationResults
		);
		return $this->render('default/index.html.twig', $params);
	}

	/**
	 * @Route("/contact", name="contact")
	 */
	public function contactAction()
	{
		return $this->render('default/contact.html.twig');
	}

	/**
	 * @Route("/a-propos", name="about")
	 */
	public function aboutAction()
	{

		$days = array("lundi", "mardi", "mercredi", "jeudi");

		return $this->render('default/about.html.twig', 
			array(
				"days" => $days,
				"myDate" => date("d-m-Y"),
				"randomNum" => rand()
			)
		);
	}
}
