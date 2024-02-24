<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;


final class RegexScrubber implements Scrubber, CustomizableScrubber, FormatScrubber
{

    private string $regexp;
    private string $replacement;
    private string $context;
    private array $options;

    public function __construct(string $regexp, string $replacement, callable ...$options)
    {
        $this->regexp = $regexp;
        $this->replacement = $replacement;
        $this->context = '%s';
        $this->options = $options;
    }

    public function clean(string $subject): string
    {
        $this->applyOptions();

        [$regexp, $replacement] = $this->applyContext();

        $result = preg_replace($regexp, $replacement, $subject);

        if ($result === null) {
            return $subject;
        }

        return $result;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function setReplacement(string $replacement): void
    {
        $this->replacement = $replacement;
    }

    private function applyContext(): array
    {
        $expr = substr($this->regexp, 1, -1);
        $regexp = sprintf("%s%s%s", $this->regexp[0], sprintf($this->context, $expr), $this->regexp[-1]);
        $replacement = sprintf($this->context, $this->replacement);
        return [$regexp, $replacement];
    }

    public function applyOptions(): void
    {
        foreach ($this->options as $option) {
            $option($this);
        }
    }
}
