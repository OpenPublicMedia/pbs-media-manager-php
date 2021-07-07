<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

class Episode extends FullLengthVideo
{
    private Season $season;
    private int $segment;

    /**
     * @return \OpenPublicMedia\PbsMediaManager\Model\Season
     */
    public function getSeason(): Season
    {
        return $this->season;
    }

    /**
     * @param \OpenPublicMedia\PbsMediaManager\Model\Season $season
     *
     * @return Episode
     */
    public function setSeason(Season $season): Episode
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return int
     */
    public function getSegment(): int
    {
        return $this->segment;
    }

    /**
     * @param int $segment
     *
     * @return Episode
     */
    public function setSegment(int $segment): Episode
    {
        $this->segment = $segment;
        return $this;
    }
}
