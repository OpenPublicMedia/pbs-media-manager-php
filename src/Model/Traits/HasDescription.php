<?php

namespace OpenPublicMedia\PbsMediaManager\Model\Traits;

trait HasDescription
{
    private string $descriptionLong;
    private string $descriptionShort;

    /**
     * @return string
     */
    public function getDescriptionLong(): string
    {
        return $this->descriptionLong;
    }

    /**
     * @param string $descriptionLong
     *
     * @return $this
     */
    public function setDescriptionLong(string $descriptionLong): self
    {
        $this->descriptionLong = $descriptionLong;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionShort(): string
    {
        return $this->descriptionShort;
    }

    /**
     * @param string $descriptionShort
     *
     * @return $this
     */
    public function setDescriptionShort(string $descriptionShort): self
    {
        $this->descriptionShort = $descriptionShort;
        return $this;
    }
}
