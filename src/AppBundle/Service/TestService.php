<?php

	namespace AppBundle\Service;

	class TestService
	{

		protected $doctrine;

		public function __construct($myString, $param, $doctrine)
		{
			$this->doctrine = $doctrine;
		}

		public function yo()
		{
			dump($this->doctrine);
		}

	}