<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

class Platform extends ModelBase
{
    private string $slug;

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
     * @return \OpenPublicMedia\PbsMediaManager\Model\Platform
     */
    public function setSlug(string $slug): Platform
    {
        $this->slug = $slug;
        return $this;
    }
}
