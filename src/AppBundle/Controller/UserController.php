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

				//use Symfony\Component\Security\Core\Util\SecureRandom;
				$generator = new SecureRandom();
				$salt = bin2hex( $generator->nextBytes(50) );
				$token = bin2hex( $generator->nextBytes(50) );

				$user->setSalt($salt);
				$user->setToken($token);
				
				dump($user);
			}

			$params = array(
				"registerForm" => $registerForm->createView()
			);
			return $this->render("user/register_user.html.twig", $params);
		}

	}