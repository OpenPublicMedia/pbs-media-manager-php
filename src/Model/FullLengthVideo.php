<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasDescription;

abstract class FullLengthVideo
{
    use HasDescription;

    private DateTime $encored;
    private string $nola;
    private int $ordinal;
    private string $language;
    private array $links;
    private DateTime $premiered;
    private Show $show;
    private string $slug;
    private string $titleSortable;
    private DateTime $updated;

    /**
     * @return \DateTime
     */
    public function getEncored(): DateTime
    {
        return $this->encored;
    }

    /**
     * @param \DateTime $encored
     *
     * @return $this
     */
    public function setEncored(DateTime $encored): self
    {
        $this->encored = $encored;
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
     * @return $this
     */
    public function setNola(string $nola): self
    {
        $this->nola = $nola;
        return $this;
    }

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
     * @return $this
     */
    public function setOrdinal(int $ordinal): self
    {
        $this->ordinal = $ordinal;
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
     * @return $this
     */
    public function setLanguage(string $language): self
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
     * @return $this
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
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
     * @return $this
     */
    public function setPremiered(DateTime $premiered): self
    {
        $this->premiered = $premiered;
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
     * @return $this
     */
    public function setShow(Show $show): self
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
     * @return $this
     */
    public function setSlug(string $slug): self
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
     * @return $this
     */
    public function setTitleSortable(string $titleSortable): self
    {
        $this->titleSortable = $titleSortable;
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
     * @return $this
     */
    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;
        return $this;
    }
}
