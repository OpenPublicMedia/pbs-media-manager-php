<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;

class Asset extends ModelBase
{
    private array $audio;
    private array $captions;
    private ?Episode $episode = null;
    private ?Franchise $franchise = null;
    private array $platforms;
    private ?Season $season = null;
    private ?Show $show = null;
    private string $slug;
    private string $titleSortable;
    private string $type;
    private DateTime $updated;
    private array $videos;
    private array $windows;

    /**
     * @return array
     */
    public function getAudio(): array
    {
        return $this->audio;
    }

    /**
     * @param array $audio
     *
     * @return Asset
     */
    public function setAudio(array $audio): Asset
    {
        $this->audio = $audio;
        return $this;
    }

    /**
     * @return array
     */
    public function getCaptions(): array
    {
        return $this->captions;
    }

    /**
     * @param array $captions
     *
     * @return Asset
     */
    public function setCaptions(array $captions): Asset
    {
        $this->captions = $captions;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Episode|null
     */
    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Episode|null $episode
     *
     * @return Asset
     */
    public function setEpisode(?Episode $episode): Asset
    {
        $this->episode = $episode;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Franchise|null
     */
    public function getFranchise(): ?Franchise
    {
        return $this->franchise;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Franchise|null $franchise
     *
     * @return Asset
     */
    public function setFranchise(?Franchise $franchise): Asset
    {
        $this->franchise = $franchise;
        return $this;
    }

    /**
     * @return array
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * @param array $platforms
     *
     * @return Asset
     */
    public function setPlatforms(array $platforms): Asset
    {
        $this->platforms = $platforms;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Season|null
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Season|null $season
     *
     * @return Asset
     */
    public function setSeason(?Season $season): Asset
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Show|null
     */
    public function getShow(): ?Show
    {
        return $this->show;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Show|null $show
     *
     * @return Asset
     */
    public function setShow(?Show $show): Asset
    {
        $this->show = $show;
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
     * @return Asset
     */
    public function setSlug(string $slug): Asset
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
     * @return Asset
     */
    public function setTitleSortable(string $titleSortable): Asset
    {
        $this->titleSortable = $titleSortable;
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
     * @return Asset
     */
    public function setType(string $type): Asset
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
     * @return Asset
     */
    public function setUpdated(DateTime $updated): Asset
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return array
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * @param array $videos
     *
     * @return Asset
     */
    public function setVideos(array $videos): Asset
    {
        $this->videos = $videos;
        return $this;
    }

    /**
     * @return array
     */
    public function getWindows(): array
    {
        return $this->windows;
    }

    /**
     * @param array $windows
     *
     * @return Asset
     */
    public function setWindows(array $windows): Asset
    {
        $this->windows = $windows;
        return $this;
    }
}
