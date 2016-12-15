<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Redirect
 *
 * @ORM\Table(name="redirect")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RedirectRepository")
 */
class Redirect
{
    /**
     * @var guid
     *
     * @ORM\Column(name="slug", type="guid", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=2048, unique=true)
     * @Assert\NotBlank(message="We need an URL to shorten")
     * @Assert\Url()
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="total_views", type="integer")
     */
    private $totalViews;


    /**
     * Set slug
     *
     * @param guid $slug
     *
     * @return Redirect
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return guid
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Redirect
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set totalViews
     *
     * @param integer $totalViews
     *
     * @return Redirect
     */
    public function setTotalViews($totalViews)
    {
        $this->totalViews = $totalViews;

        return $this;
    }

    /**
     * Get totalViews
     *
     * @return int
     */
    public function getTotalViews()
    {
        return $this->totalViews;
    }
}

