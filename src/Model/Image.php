<?php

namespace OpenPublicMedia\PbsMediaManager\Model;

use DateTime;

class Image
{
    private string $profile;
    private string $url;
    private DateTime $updated;

    /**
     * @return string
     */
    public function getProfile(): string
    {
        return $this->profile;
    }

    /**
     * @param string $profile
     *
     * @return Image
     */
    public function setProfile(string $profile): Image
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Image
     */
    public function setUrl(string $url): Image
    {
        $this->url = $url;
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
     * @return Image
     */
    public function setUpdated(DateTime $updated): Image
    {
        $this->updated = $updated;
        return $this;
    }
}
