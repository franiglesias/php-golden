<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;


use JsonPath\InvalidJsonException;
use JsonPath\JsonObject;

final class PathScrubber implements Scrubber, CustomizableScrubber
{
    private string $path;
    private string $replacement;
    private array $options;

    public function __construct(string $path, string $replacement, callable ...$opts)
    {
        $this->path = $path;
        $this->replacement = $replacement;
        $this->options = $opts;
    }

    public function clean(string $subject): string
    {
        $this->applyOptions();
        try {
            $jsonObject = new JsonObject($subject);
        } catch (InvalidJsonException $e) {
            return $subject;
        }
        $jsonObject->{'$.' . $this->path} = $this->replacement;
        return $jsonObject->getJson(JSON_PRETTY_PRINT);
    }

    public function setReplacement(string $replacement)
    {
        $this->replacement = $replacement;
    }

    public function applyOptions(): void
    {
        foreach ($this->options as $option) {
            $option($this);
        }
    }
}
