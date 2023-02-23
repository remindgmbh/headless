<?php

declare(strict_types=1);

namespace Remind\Headless\Event;

// TODO: remove once implemended in EXT:headless
class ModifyFilePropertiesEvent
{
    private array $metaData;
    private array $properties;

    public function __construct(
        array $properties,
        array $metaData
    ) {
        $this->metaData = $metaData;
        $this->properties = $properties;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }
}
