<?php

	namespace AppBundle\Controller;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Security\Core\Util\SecureRandom;

	use AppBundle\Form\UserType;
	use AppBundle\Entity\User;

	class UserController extends Controller 
	{

		/**
		* @Route("/utilisateur/enregistrement", name="register_user")
		*/
		public function registerUserAction(Request $request)
		{
			$user = new User();
			$registerForm = $this->createForm(new UserType(), $user);

			$registerForm->handleRequest($request);
			if ($registerForm->isValid()){

				$user->setRoles( array("ROLE_ADMIN") );

				$generator = new SecureRandom();
				$salt = bin2hex( $generator->nextBytes(50) );
				$token = bin2hex( $generator->nextBytes(50) );

				$user->setSalt($salt);
				$user->setToken($token);

				$user->setDateRegistered( new \DateTime() );
				$user->setDateModified( new \DateTime() );
				$user->setDateLastLogin( new \DateTime() );

				dump($user);
			}

			$params = array(
				"registerForm" => $registerForm->createView()
			);
			return $this->render("user/register_user.html.twig", $params);
		}

	}