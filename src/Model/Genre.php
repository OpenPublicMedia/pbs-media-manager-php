<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

class Genre extends ModelBase
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
     * @return \OpenPublicMedia\PbsMediaManager\Model\Genre
     */
    public function setSlug(string $slug): Genre
    {
        $this->slug = $slug;
        return $this;
    }
}
