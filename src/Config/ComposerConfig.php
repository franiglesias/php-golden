<?php

declare (strict_types=1);

namespace Golden\Config;


final class ComposerConfig
{

    public function key(string $key)
    {
        $composerJson = file_get_contents('composer.json');
        $composerData = json_decode($composerJson, true);

        $keys = explode('.', $key);
        $value = $composerData;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }

        return $value;
    }
}
