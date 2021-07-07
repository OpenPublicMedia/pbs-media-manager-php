<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasDescription;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasGenre;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasPlatforms;

class Franchise extends ModelBase
{
    use HasDescription, HasGenre, HasPlatforms;

    private string $funderMessage;
    private string $hashtag;
    private array $images;
    private bool $excludedFromDfp;
    private string $links;
    private string $nola;
    private DateTime $premiered;
    private string $slug;
    private string $titleSortable;
    private string $trackingGaEvent;
    private string $trackingGaPage;
    private string $type;
    private DateTime $updated;

    /**
     * @return string
     */
    public function getFunderMessage(): string
    {
        return $this->funderMessage;
    }

    /**
     * @param string $funderMessage
     *
     * @return Franchise
     */
    public function setFunderMessage(string $funderMessage): Franchise
    {
        $this->funderMessage = $funderMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashtag(): string
    {
        return $this->hashtag;
    }

    /**
     * @param string $hashtag
     *
     * @return Franchise
     */
    public function setHashtag(string $hashtag): Franchise
    {
        $this->hashtag = $hashtag;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return Franchise
     */
    public function setImages(array $images): Franchise
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExcludedFromDfp(): bool
    {
        return $this->excludedFromDfp;
    }

    /**
     * @param bool $excludedFromDfp
     *
     * @return Franchise
     */
    public function setExcludedFromDfp(bool $excludedFromDfp): Franchise
    {
        $this->excludedFromDfp = $excludedFromDfp;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinks(): string
    {
        return $this->links;
    }

    /**
     * @param string $links
     *
     * @return Franchise
     */
    public function setLinks(string $links): Franchise
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return string
     */
    public function getNola(): string
    {
        return $this->nola;
    }

    /**
     * @param string $nola
     *
     * @return Franchise
     */
    public function setNola(string $nola): Franchise
    {
        $this->nola = $nola;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPremiered(): DateTime
    {
        return $this->premiered;
    }

    /**
     * @param \DateTime $premiered
     *
     * @return Franchise
     */
    public function setPremiered(DateTime $premiered): Franchise
    {
        $this->premiered = $premiered;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Franchise
     */
    public function setSlug(string $slug): Franchise
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleSortable(): string
    {
        return $this->titleSortable;
    }

    /**
     * @param string $titleSortable
     *
     * @return Franchise
     */
    public function setTitleSortable(string $titleSortable): Franchise
    {
        $this->titleSortable = $titleSortable;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackingGaEvent(): string
    {
        return $this->trackingGaEvent;
    }

    /**
     * @param string $trackingGaEvent
     *
     * @return Franchise
     */
    public function setTrackingGaEvent(string $trackingGaEvent): Franchise
    {
        $this->trackingGaEvent = $trackingGaEvent;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackingGaPage(): string
    {
        return $this->trackingGaPage;
    }

    /**
     * @param string $trackingGaPage
     *
     * @return Franchise
     */
    public function setTrackingGaPage(string $trackingGaPage): Franchise
    {
        $this->trackingGaPage = $trackingGaPage;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Franchise
     */
    public function setType(string $type): Franchise
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return Franchise
     */
    public function setUpdated(DateTime $updated): Franchise
    {
        $this->updated = $updated;
        return $this;
    }
}
