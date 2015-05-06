<?php

	namespace AppBundle\Command;

	use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	use Symfony\Component\DomCrawler\Crawler;

	use AppBundle\Entity\Ad;

	class LbcCommand extends ContainerAwareCommand
	{
	    protected function configure()
	    {
	        $this
	            ->setName('hitema:lbc')
	            ->setDescription('Parse LeBonCoin')
	        ;
	    }

	    protected function execute(InputInterface $input, OutputInterface $output)
	    {

	    	// Goutte 

	    	$doctrine = $this->getContainer()->get("doctrine");
	    	$em = $doctrine->getManager();
	    	$adRepo = $doctrine->getRepository("AppBundle:Ad");

	    	$lbc = file_get_contents("http://www.leboncoin.fr/annonces/offres/ile_de_france/?f=a&th=1&q=macbook+pro");

	    	$crawler = new Crawler($lbc);

	    	$crawler->filter('.list-lbc > a')
	    		->each(function (Crawler $node, $i) use ($adRepo, $em, $output, &$ads) {
	    			$ad = new Ad();
		    		$ad->setTitle( $node->attr("title") );
		    		$ad->setLink( $node->attr("href") );

		    		if (preg_match("#/(\d+).htm#", $ad->getLink(), $matches)){
		    			$ad->setLbcId( $matches[1] );
		    		}

		    		$found = $adRepo->findOneByLbcId($ad->getLbcId());
		    		if ($found){
		    			return;
		    		}

		    		$priceCrawler = $node->filter(".price");
		    		$price = null;
		    		if ($priceCrawler->count() > 0){
		    			$price = preg_replace("/[^0-9]/", "", $priceCrawler->text());
		    		}
		    		$ad->setPrice( $price );

		    		$ad->setDateCreated(new \DateTime() );
				   // $output->writeln( $adTitle );
				    $em->persist($ad);

				    $output->writeln("Saving " . $ad->getTitle());
				}
			);

	    	$em->flush();

	        $output->writeln("Done.");
	    }
	}