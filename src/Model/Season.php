<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasDescription;

class Season
{
    use HasDescription;

    private int $ordinal;
    private DateTime $updated;
    private Image $image;
    private array $latestAssetImages;
    private Show $show;
    private array $links;

    /**
     * @return int
     */
    public function getOrdinal(): int
    {
        return $this->ordinal;
    }

    /**
     * @param int $ordinal
     *
     * @return Season
     */
    public function setOrdinal(int $ordinal): Season
    {
        $this->ordinal = $ordinal;
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
     * @return Season
     */
    public function setUpdated(DateTime $updated): Season
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Image $image
     *
     * @return Season
     */
    public function setImage(Image $image): Season
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return array
     */
    public function getLatestAssetImages(): array
    {
        return $this->latestAssetImages;
    }

    /**
     * @param array $latestAssetImages
     *
     * @return Season
     */
    public function setLatestAssetImages(array $latestAssetImages): Season
    {
        $this->latestAssetImages = $latestAssetImages;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Show
     */
    public function getShow(): Show
    {
        return $this->show;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Show $show
     *
     * @return Season
     */
    public function setShow(Show $show): Season
    {
        $this->show = $show;
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
     * @return Season
     */
    public function setLinks(array $links): Season
    {
        $this->links = $links;
        return $this;
    }
}
