<?php

declare (strict_types=1);

namespace Golden\Normalizer;


final class JsonNormalizer implements Normalizer
{

    public function normalize($subject): string
    {
        $prepared = $this->prepare($subject);
        $normalized = $this->jsonEncodeIfNeeded($prepared);
        return trim($normalized, '" ' . "\r");
    }

    private function prepare($subject)
    {
        if (!is_string($subject)) {
            return $subject;
        }

        return $this->decodeIfAlreadyJSON($subject);
    }

    /**
     * decodeIfAlreadyJSON avoids treating the json string subjects as a
     * plain string avoiding the introducing escape characters affecting
     * readability.
     *
     * As a nice side-effect, the json string will be prettified, so the
     * snapshot will be more readable.
     */
    private function decodeIfAlreadyJSON(string $subject)
    {
        $tmp = json_decode($subject, true);
        if (is_array($tmp) && json_last_error() == JSON_ERROR_NONE) {
            return $tmp;
        }

        return $subject;
    }

    /**
     * At this point, if prepared is a string we can return it to avoid
     * undesired changes.
     *
     * If not, we can encode as a JSON and return that.
     */
    private function jsonEncodeIfNeeded($prepared): string
    {
        if (is_string($prepared)) {
            return $prepared;
        }

        return json_encode($prepared, JSON_PRETTY_PRINT);
    }
}
