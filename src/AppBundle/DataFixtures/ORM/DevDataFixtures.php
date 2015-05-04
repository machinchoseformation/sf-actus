<?php

	namespace AppBundle\DataFixtures\ORM;

	use Doctrine\Common\DataFixtures\FixtureInterface;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DependencyInjection\ContainerAware;
	use Symfony\Component\Security\Core\Util\SecureRandom;


	use AppBundle\Entity\User;
	use AppBundle\Entity\Story;
	use AppBundle\Entity\Comment;

	class DevDataFixtures extends ContainerAware implements FixtureInterface
	{

		public function load(ObjectManager $em)
		{

			$faker = \Faker\Factory::create();

			//en boucle, créer quelques User
			//users de base
			$users = array("yo", "pouf", "admin", "test");

			foreach($users as $username){

				$user = new User();

				$user->setUsername($username);
				$user->setEmail( $faker->email );
				$user->setRoles( array("ROLE_ADMIN") );

				$generator = new SecureRandom();
				$salt = bin2hex( $generator->nextBytes(50) );
				$token = bin2hex( $generator->nextBytes(50) );

				$user->setSalt($salt);
				$user->setToken($token);

				$user->setDateRegistered( $faker->dateTimeBetween("-3 years") );
				$user->setDateModified( $user->getDateRegistered() );
				$user->setDateLastLogin( $user->getDateRegistered() );

				$encoder = $this->get("security.password_encoder");
				$encodedPassword = $encoder->encodePassword(
					$user, $username
				);

				$user->setPassword($encodedPassword);

				$em->persist($user);

				//pour chaque user, créer plusieurs Story
					//pour chaque Story, créer quelques Comment
				
				$storyNumber = $faker->numberBetween(0,30);
				for($i=0;$i<$storyNumber;$i++){
					$story = new Story();
					$story->setTitle( $faker->sentence );
					$story->setContent( $faker->text );

					$slug = $this->get("cocur_slugify")->slugify( $story->getTitle() );
					$story->setSlug( $slug );

					//crée la relation avec l'auteur de l'article
					//(l'utilisateur connecté)
					$story->setAuthor( $user );

					$story->setIsPublished( $faker->boolean($chanceOfGettingTrue = 90) );

					$story->setDateCreated( $faker->dateTimeBetween($user->getDateRegistered()) );
					$story->setDateModified( $story->getDateCreated() );

					//si l'article est publié, donner une date de publication
					if ($story->getIsPublished()){
						$story->setDatePublished( $faker->dateTimeBetween($story->getDateCreated()) );
						$story->setDateModified( $story->getDatePublished() );
					}

					$em->persist($story);

					$commentsNumber = $faker->numberBetween(0,12);

					for($j=0;$j<$commentsNumber;$j++){

						$comment = new Comment();
						$comment->setStory($story);
						$comment->setPseudo($faker->username);
						$comment->setEmail($comment->getPseudo() . "@" . $faker->domainName);
						$comment->setContent($faker->paragraph);
						$comment->setDateCreated( $faker->dateTimeBetween($story->getDateCreated()) );
						$comment->setDateModified($comment->getDateCreated());

						$em->persist($comment);
					}
				}
			}

			$em->flush();

		}


		private function get($service){
			return $this->container->get($service);
		}

	}