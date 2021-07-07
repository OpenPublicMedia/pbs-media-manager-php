<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;
use OpenPublicMedia\PbsMediaManager\Model\Traits\HasDescription;

class Collection
{
    use HasDescription;

    private bool $featured;
    private DateTime $updated;

    /**
     * @return bool
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * @param bool $featured
     *
     * @return Collection
     */
    public function setFeatured(bool $featured): Collection
    {
        $this->featured = $featured;
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
     * @return Collection
     */
    public function setUpdated(DateTime $updated): Collection
    {
        $this->updated = $updated;
        return $this;
    }
}
