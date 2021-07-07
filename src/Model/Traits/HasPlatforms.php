<?php

namespace OpenPublicMedia\PbsMediaManager\Model\Traits;

use OpenPublicMedia\PbsMediaManager\Model\Genre;

trait HasPlatforms
{
    private array $platforms;

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Platform[]
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Platform[] $platforms
     *
     * @return $this
     */
    public function setPlatforms(array $platforms): self
    {
        $this->platforms = $platforms;
        return $this;
    }
}
