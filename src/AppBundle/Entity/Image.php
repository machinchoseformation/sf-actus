<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Image
 *
 * @UniqueEntity("filename")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ImageRepository")
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, unique=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="mimetype", type="string", length=255)
     */
    private $mimetype;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUploaded", type="datetime")
     */
    private $dateUploaded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

    /**
    * @var \Symfony\Component\HttpFoundation\File\UploadedFile
    * 
    * @Assert\NotBlank(message="Veuillez choisir une image à téléverser !")
    * @Assert\Image(
    *     maxSize = "5000k",
    *     maxSizeMessage = "The file is too large ({{ size }} {{ suffix }}). 
    *          Allowed maximum size is {{ limit }} {{ suffix }}."
    * )
    */
    private $tmpFile;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Story")
    */
    private $story;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     * @return Image
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string 
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Image
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set dateUploaded
     *
     * @param \DateTime $dateUploaded
     * @return Image
     */
    public function setDateUploaded($dateUploaded)
    {
        $this->dateUploaded = $dateUploaded;

        return $this;
    }

    /**
     * Get dateUploaded
     *
     * @return \DateTime 
     */
    public function getDateUploaded()
    {
        return $this->dateUploaded;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Image
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set tmpFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $tmpFile
     * @return Image
     */
    public function setTmpFile($tmpFile)
    {
        $this->tmpFile = $tmpFile;

        return $this;
    }

    /**
     * Get tmpFile
     *
     * @return string 
     */
    public function getTmpFile()
    {
        return $this->tmpFile;
    }

    /**
     * Set story
     *
     * @param \AppBundle\Entity\Story $story
     * @return Image
     */
    public function setStory(\AppBundle\Entity\Story $story = null)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story
     *
     * @return \AppBundle\Entity\Story 
     */
    public function getStory()
    {
        return $this->story;
    }
}
