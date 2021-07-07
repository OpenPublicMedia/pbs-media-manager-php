<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasDescription;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasGenre;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasPlatforms;

class Show extends ModelBase
{
    use HasDescription, HasGenre, HasPlatforms;

    private array $audience;
    private bool $canEmbedPlayer;
    private bool $displayEpisodeNumber;
    private bool $excludedFromDfp;
    private Franchise $franchise;
    private string $funderMessage;
    private string $hashtag;
    private array $images;
    private string $language;
    private array $links;
    private string $nola;
    private bool $ordinalSeason;
    private DateTime $premiered;
    private bool $private;
    private string $slug;
    private string $titleSortable;
    private string $trackingGaEvent;
    private string $trackingGaPage;
    private DateTime $updated;

    /**
     * @return array
     */
    public function getAudience(): array
    {
        return $this->audience;
    }

    /**
     * @param array $audience
     *
     * @return Show
     */
    public function setAudience(array $audience): Show
    {
        $this->audience = $audience;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCanEmbedPlayer(): bool
    {
        return $this->canEmbedPlayer;
    }

    /**
     * @param bool $canEmbedPlayer
     *
     * @return Show
     */
    public function setCanEmbedPlayer(bool $canEmbedPlayer): Show
    {
        $this->canEmbedPlayer = $canEmbedPlayer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayEpisodeNumber(): bool
    {
        return $this->displayEpisodeNumber;
    }

    /**
     * @param bool $displayEpisodeNumber
     *
     * @return Show
     */
    public function setDisplayEpisodeNumber(bool $displayEpisodeNumber): Show
    {
        $this->displayEpisodeNumber = $displayEpisodeNumber;
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
     * @return Show
     */
    public function setExcludedFromDfp(bool $excludedFromDfp): Show
    {
        $this->excludedFromDfp = $excludedFromDfp;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Franchise
     */
    public function getFranchise(): Franchise
    {
        return $this->franchise;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Franchise $franchise
     *
     * @return Show
     */
    public function setFranchise(Franchise $franchise): Show
    {
        $this->franchise = $franchise;
        return $this;
    }

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
     * @return Show
     */
    public function setFunderMessage(string $funderMessage): Show
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
     * @return Show
     */
    public function setHashtag(string $hashtag): Show
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
     * @return Show
     */
    public function setImages(array $images): Show
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return Show
     */
    public function setLanguage(string $language): Show
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     *
     * @return Show
     */
    public function setLinks(array $links): Show
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
     * @return Show
     */
    public function setNola(string $nola): Show
    {
        $this->nola = $nola;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOrdinalSeason(): bool
    {
        return $this->ordinalSeason;
    }

    /**
     * @param bool $ordinalSeason
     *
     * @return Show
     */
    public function setOrdinalSeason(bool $ordinalSeason): Show
    {
        $this->ordinalSeason = $ordinalSeason;
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
     * @return Show
     */
    public function setPremiered(DateTime $premiered): Show
    {
        $this->premiered = $premiered;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     * @param bool $private
     *
     * @return Show
     */
    public function setPrivate(bool $private): Show
    {
        $this->private = $private;
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
     * @return Show
     */
    public function setSlug(string $slug): Show
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
     * @return Show
     */
    public function setTitleSortable(string $titleSortable): Show
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
     * @return Show
     */
    public function setTrackingGaEvent(string $trackingGaEvent): Show
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
     * @return Show
     */
    public function setTrackingGaPage(string $trackingGaPage): Show
    {
        $this->trackingGaPage = $trackingGaPage;
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
     * @return Show
     */
    public function setUpdated(DateTime $updated): Show
    {
        $this->updated = $updated;
        return $this;
    }
}
