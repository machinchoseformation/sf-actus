<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Story;
use AppBundle\Form\StoryType;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
* @Route("/article")
*/
class StoryController extends Controller
{

	/**
	* @Route(
	* 	"/details/{slug}", 
	* 	requirements={"slug":"[a-z0-9-]+"}, 
	* 	name="story_details"
	* )	
	*/ 
	public function storyDetailsAction(Request $request, $slug )
	{

		$storyRepo = $this->get("doctrine")->getRepository("AppBundle:Story");
		$story = $storyRepo->findOneBySlug( $slug );

		if (!$story){
			throw $this->createNotFoundException("Oupsie !");
		}

		//traitement du formulaire de commentaire
		$comment = new Comment();
		$commentForm = $this->createForm(new CommentType, $comment);

		$commentForm->handleRequest($request);
		if ($commentForm->isValid()){
		
			$comment->setStory($story);

			$em = $this->get("doctrine")->getManager();
			$em->persist($comment);
			$em->flush();

			$this->addFlash("notice", "Commentaire enregistré !");

			//envoie un email à l'auteur de l'article
			$mailer = $this->get('mailer');
		    $message = $mailer->createMessage()
		        ->setSubject('Nouveau commentaire sur '.$story->getTitle().' !')
		        ->setFrom('info@sf-actus.com')
		        ->setTo($story->getAuthor()->getEmail())
		        ->setBody(
		            $this->renderView("email/comment_author_notification.html.twig", 
		            	array("story" => $story, "newComment" => $comment)
		            ),
		            'text/html'
		        );
		        $mailer->send($message);

		        return $this->redirectToRoute("story_details", array("slug" => $story->getSlug()));
		}

		$params = array(
			"story" => $story,
			"commentForm" => $commentForm->createView()
		);
		return $this->render("story/story_details.html.twig", $params);
	}



	/**
	 * @Security("has_role('ROLE_ADMIN')")
	 * @Route("/creation", name="create_story")
	 */
	public function createStoryAction(
		\Symfony\Component\HttpFoundation\Request $request)
	{

		//créer une entité vide à associer au formulaire
		$story = new Story();

		//créer une instance du formulaire
		$createStoryForm = $this->createForm(new StoryType(), $story); 

		//récupérer les infos depuis la requête
		//pour hydrater notre entité vide
		$createStoryForm->handleRequest( $request );

		//si le formulaire est valide (sous-entendu s'il est soumis)
		if ( $createStoryForm->isValid() ){

			//on hydrate les champs manquants
			$story->setDateCreated( new \DateTime() );
			$story->setDateModified( $story->getDateCreated() );

			$slug = $this->get("cocur_slugify")->slugify( $story->getTitle() );
			$story->setSlug( $slug );

			//crée la relation avec l'auteur de l'article
			//(l'utilisateur connecté)
			$story->setAuthor( $this->getUser() );

			//si l'article est publié, donner une date de publication
			if ($story->getIsPublished()){
				$story->setDatePublished( new \DateTime() );
			}

			//on sauvegarde
			$em = $this->get("doctrine")->getManager();
			$em->persist($story);
			$em->flush();

			//redirection
			return $this->redirectToRoute("story_details", 
				array("slug" => $story->getSlug())
			);
		}

		//on passe le formulaire à la vue
		$params = array(
			"createStoryForm" => $createStoryForm->createView()
		);

		return $this->render('story/create_story.html.twig', $params);
	}


	/**
	* @Security("has_role('ROLE_ADMIN')")
	* @Route("/effacer/{id}", requirements={"id":"\d+"}, name="story_delete")
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


	/**
	* @Route("test-ajax/{storyId}", name="test_ajax")
	*/
	public function testAjaxAction(Request $request, $storyId)
	{
		$storyRepo = $this->get("doctrine")->getRepository("AppBundle:Story");
		$story = $storyRepo->find($storyId);
		//plein de merveille en bdd avec cette story

		if ($request->isXmlHttpRequest()){
			return new JsonResponse(array("yo" => "bla"));
		}

		return $this->render("story/test_ajax.html.twig");
	}

}