<?php

	namespace AppBundle\DataFixtures\ORM;

	use Doctrine\Common\DataFixtures\FixtureInterface;
	use Doctrine\Common\Persistence\ObjectManager;

	class DevDataFixtures implements FixtureInterface
	{

		public function load(ObjectManager $em)
		{

			//en boucle, créer quelques User

				//pour chaque user, créer plusieurs Story

					//pour chaque Story, créer quelques Comment

		}

	}