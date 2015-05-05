<?php

	namespace AppBundle\Uploader;

	use Symfony\Component\HttpFoundation\File\UploadedFile;

	class ImageUploader 
	{

		protected $kernel_root_dir;

		public function __construct($kernel_root_dir)
		{
			$this->kernel_root_dir = $kernel_root_dir;
		}

		public function moveTmpFileToWeb(UploadedFile $uploadedFile, $filename)
		{
			$path = $this->getPathToWeb();
			$fullPath = $path . "up/img/original/";

			//déplacer le fichier
			$uploadedFile->move($fullPath, $filename);
			return $fullPath.$filename;
		}

		public function createSmallerImages($pathToOriginal, $filename)
		{
			$img = new \abeautifulsite\SimpleImage($pathToOriginal);
			$img->best_fit(600, 600)->save($this->getPathToWeb() . "up/img/medium/" . $filename);
			$img->thumbnail(60)->save($this->getPathToWeb() . "up/img/thumbnail/" . $filename);
		}

		public function getSafeName(UploadedFile $uploadedFile)
		{
			$safeName = uniqid();
			$fileExtension = $uploadedFile->guessExtension();
			$safeName .= "." . $fileExtension;
			return $safeName;
		}

		protected function getPathToWeb()
		{
			$ds = DIRECTORY_SEPARATOR;
			//détermine le chemin vers lequel déplacer le fichier temp
			$path = $this->kernel_root_dir."$ds..$ds"."web".$ds;
			return $path;
		}

	}