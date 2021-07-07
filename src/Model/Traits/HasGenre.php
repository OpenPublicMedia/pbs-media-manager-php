<?php

namespace OpenPublicMedia\PbsMediaManager\Model\Traits;

use OpenPublicMedia\PbsMediaManager\Model\Genre;

trait HasGenre
{
    /**
     * @var \OpenPublicMedia\PbsMediaManager\Model\Genre
     */
    private Genre $genre;

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Genre
     */
    public function getGenre(): Genre
    {
        return $this->genre;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Genre $genre
     *
     * @return $this
     */
    public function setGenre(Genre $genre): self
    {
        $this->genre = $genre;
        return $this;
    }
}
