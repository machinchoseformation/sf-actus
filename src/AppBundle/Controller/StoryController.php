<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Story;

class StoryController extends Controller
{

	/**
	* @Route("/article/details/{id}", requirements={"id":"\d+"}, name="story_details")
	*/ 
	public function storyDetailsAction( $id )
	{
		$storyRepo = $this->get("doctrine")->getRepository("AppBundle:Story");
		$story = $storyRepo->find( $id );

		if (!$story){
			throw $this->createNotFoundException("Oupsie !");
		}

		$params = array(
			"story" => $story
		);
		return $this->render("story/story_details.html.twig", $params);
	}



	/**
	 * @Route("/article/creation", name="create_story")
	 */
	public function createStoryAction()
	{

		//récupère le gestionnaire d'entité
		//qui permet de faire des create, update ou delete
		$em = $this->get("doctrine")->getManager();

		for($i=0;$i<50;$i++){

			//crée une instance
			$newStory = new Story();

			//hydrate l'instance
			$newStory->setTitle( "Un titre éêàö ! pouf pif ? bla $i" );

			//crée un slug à partir du titre
			$slug = $this->get('cocur_slugify')->slugify( $newStory->getTitle() );
			$newStory->setSlug( $slug );

			$newStory->setContent( mt_rand(1000,9999) . " lorem ipsum dolor sit amet..." );
			$newStory->setDateCreated( new \DateTime() );
			$newStory->setDateModified( new \DateTime() );
			$newStory->setDatePublished( null );
			$newStory->setIsPublished( false );

			//sauvegarde l'instance en bdd
			$em->persist( $newStory );
		}
		
		$em->flush();

		return $this->render('story/create_story.html.twig');
	}


	/**
	* @Route("/article/effacer/{id}", requirements={"id":"\d+"}, 
	* name="story_delete")
	*/ 	
	public function deleteStoryAction( $id )
	{

		//efface la Story en bdd
		$doctrine = $this->get("doctrine");
		$em = $doctrine->getManager();
		$storyRepo = $doctrine->getRepository("AppBundle:Story");

		//on récupère la story pour l'effacer...
		$dyingStory = $storyRepo->find($id);

		if (!$dyingStory){
			throw $this->createAccessDeniedException("Wtf dude.");
		}

		$em->remove($dyingStory);
		$em->flush();

		//crée un message à afficher 
		$this->addFlash(
            'notice',
            'Actualité effacée !!'
        );

		//redirige sur accueil
		return $this->redirectToRoute("home");
	}


}