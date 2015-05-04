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

		private $em;
		private $faker;

		public function load(ObjectManager $em)
		{
			$this->em = $em;

			$this->faker = \Faker\Factory::create();

			//en boucle, créer quelques User
			//users de base
			$users = array("yo", "pouf", "admin", "test");

			foreach($users as $username){

				$user = $this->createUser($username);

				//pour chaque user, créer plusieurs Story				
				$storyNumber = $this->faker->numberBetween(0,30);
				for($i=0;$i<$storyNumber;$i++){
					
					$story = $this->createStory($user);

					//pour chaque Story, créer quelques Comment
					$commentsNumber = $this->faker->numberBetween(0,12);
					for($j=0;$j<$commentsNumber;$j++){
						$this->createComment($story);
					}
				}
			}

			$this->em->flush();

		}


		private function createComment($story)
		{
			$comment = new Comment();
			$comment->setStory($story);
			$comment->setPseudo($this->faker->username);
			$comment->setEmail($comment->getPseudo() . "@" . $this->faker->domainName);
			$comment->setContent($this->faker->paragraph);
			$comment->setDateCreated( $this->faker->dateTimeBetween($story->getDateCreated()) );
			$comment->setDateModified($comment->getDateCreated());

			$this->em->persist($comment);
			return $comment;
		}


		private function createStory($user)
		{
			$story = new Story();
			$story->setTitle( $this->faker->sentence );
			$story->setContent( $this->faker->text );

			$slug = $this->get("cocur_slugify")->slugify( $story->getTitle() );
			$story->setSlug( $slug );

			//crée la relation avec l'auteur de l'article
			//(l'utilisateur connecté)
			$story->setAuthor( $user );

			$story->setIsPublished( $this->faker->boolean($chanceOfGettingTrue = 90) );

			$story->setDateCreated( $this->faker->dateTimeBetween($user->getDateRegistered()) );
			$story->setDateModified( $story->getDateCreated() );

			//si l'article est publié, donner une date de publication
			if ($story->getIsPublished()){
				$story->setDatePublished( $this->faker->dateTimeBetween($story->getDateCreated()) );
				$story->setDateModified( $story->getDatePublished() );
			}

			$this->em->persist($story);
			return $story;
		}


		private function createUser($username)
		{
			$user = new User();

			$user->setUsername($username);
			$user->setEmail( $this->faker->email );
			$user->setRoles( array("ROLE_ADMIN") );

			$generator = new SecureRandom();
			$salt = bin2hex( $generator->nextBytes(50) );
			$token = bin2hex( $generator->nextBytes(50) );

			$user->setSalt($salt);
			$user->setToken($token);

			$user->setDateRegistered( $this->faker->dateTimeBetween("-3 years") );
			$user->setDateModified( $user->getDateRegistered() );
			$user->setDateLastLogin( $user->getDateRegistered() );

			$encoder = $this->get("security.password_encoder");
			$encodedPassword = $encoder->encodePassword(
				$user, $username
			);

			$user->setPassword($encodedPassword);

			$this->em->persist($user);
			return $user;
		}


		private function get($service){
			return $this->container->get($service);
		}

	}