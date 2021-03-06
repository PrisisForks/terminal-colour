<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020 Daniel Bannert
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/testomat/terminal-colour
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Testomat\TerminalColour\Formatter;
use Testomat\TerminalColour\StyleCode;
use Testomat\TerminalColour\Util;

$effects = [
    'none' => 0,
    'bold' => 1,
    'dark' => 2,
    'italic' => 3,
    'underscore' => 4,
    'blink' => 5,
    'blink_fast' => 6, // Limited support
    'reverse' => 7,
    'conceal' => 8,
    'crossed_out' => 9,
    'double_underline' => 21,
    'curly_underline' => '4:3', // Limited support
    'overline' => 53, // Limited support
];
$fgColors = [
    'black',
    'red',
    'green',
    'yellow',
    'blue',
    'magenta',
    'cyan',
    'white',
    //    'default',
    //    'dark_grey',
    'light_grey',
    'light_red',
    'light_green',
    'light_yellow',
    'light_blue',
    'light_magenta',
    'light_cyan',
    'light_white',
];

$styles = [];

for ($i = 0; $i <= 255; $i++) {
    $styles["fg_{$i}"] = new StyleCode($i);
}

for ($i = 0; $i <= 255; $i++) {
    $styles["bg_{$i}"] = new StyleCode(null, $i);
}

for ($green = 0; $green <= 255; $green += 51) {
    for ($red = 0; $red <= 255; $red += 51) {
        for ($blue = 0; $blue <= 255; $blue += 51) {
            $styles["fg_{$red};{$green};{$blue}"] = new StyleCode("38;2;{$red};{$green};{$blue}");
        }
    }
}

for ($green = 0; $green <= 255; $green += 51) {
    for ($red = 0; $red <= 255; $red += 51) {
        for ($blue = 0; $blue <= 255; $blue += 51) {
            $styles["bg_{$red};{$green};{$blue}"] = new StyleCode(null, "48;2;{$red};{$green};{$blue}");
        }
    }
}

for ($gray = 8; $gray < 256; $gray += 10) {
    $styles["fg_{$gray};{$gray};{$gray}"] = new StyleCode("38;2;{$gray};{$gray};{$gray}");
}

for ($gray = 8; $gray < 256; $gray += 10) {
    $styles["bg_{$gray};{$gray};{$gray}"] = new StyleCode(null, "48;2;{$gray};{$gray};{$gray}");
}

foreach ($effects as $n => $i) {
    $styles["effect_{$i}"] = new StyleCode(null, null, [$n]);
}

$styles['effect_21'] = new StyleCode(null, null, ['double_underline']);
$styles['effect_4:3'] = new StyleCode(null, null, ['curly_underline']);
$styles['effect_53'] = new StyleCode(null, null, ['overline']);

$color = Util::getSupportedColor();
$isColorSupported = $color !== 0;

$formatter = new Formatter($isColorSupported, $styles);

// Start Effects
echo \PHP_EOL . 'Effects:' . \PHP_EOL . \PHP_EOL;

foreach ($effects as $name => $i) {
    $text = str_pad('effect_' . $i, 14, ' ', \STR_PAD_BOTH);

    echo $formatter->format("<effect_{$i}>{$text}</>") . ' [' . str_pad((string) $i, 4, ' ', \STR_PAD_LEFT) . '] ' . $name . (in_array($i, [6, 9, 21], true) ? $formatter->format('<effect_2> (Not widely supported)</effect_2>') : '');

    echo \PHP_EOL;
}
// End Effects

// Start Colors
echo \PHP_EOL . sprintf('Colors are supported: %s' . \PHP_EOL, $isColorSupported ? 'YES' : 'NO');

$isColor256Supported = $color >= Util::COLOR256_TERMINAL;

echo \PHP_EOL . '16 colors are supported: ' . ($isColor256Supported ? 'YES' : 'NO') . \PHP_EOL;

$isColor256Supported = $color >= Util::COLOR256_TERMINAL;

echo \PHP_EOL . '256 colors are supported: ' . ($isColor256Supported ? 'YES' : 'NO') . \PHP_EOL;

if ($isColor256Supported) {
    for ($i = 0; $i <= 1; $i++) {
        $type = $i === 0 ? 'fg' : 'bg';

        echo \PHP_EOL . ' System colors:' . \PHP_EOL . \PHP_EOL;

        for ($color = 0; $color <= 8; $color++) {
            echo $formatter->format("<{$type}_{$color}>::</>");
        }

        echo \PHP_EOL;

        for ($color = 8; $color <= 16; $color++) {
            echo $formatter->format("<{$type}_{$color}>::</>");
        }

        echo \PHP_EOL;

        echo \PHP_EOL . ' Color cube, 6x6x6:' . \PHP_EOL . \PHP_EOL;

        for ($green = 0; $green <= 5; $green++) {
            for ($red = 0; $red <= 5; $red++) {
                for ($blue = 0; $blue <= 5; $blue++) {
                    $color = 16 + ($red * 36) + ($green * 6) + $blue;

                    echo $formatter->format("<{$type}_{$color}>::</>");
                }

                echo "\x1b[0m ";
            }

            echo \PHP_EOL;
        }

        echo \PHP_EOL . ' Grayscale ramp:' . \PHP_EOL . \PHP_EOL;

        for ($c = 232; $c < 256; $c++) {
            echo $formatter->format("<{$type}_{$c}>::</>");
        }

        echo \PHP_EOL;
    }
}

$isTrueColorSupported = $color >= Util::TRUECOLOR_TERMINAL;

echo \PHP_EOL . 'true colors are supported: ' . ($isTrueColorSupported ? 'YES' : 'NO') . \PHP_EOL;

if ($isTrueColorSupported) {
    echo \PHP_EOL . 'Examples for the 3-byte color mode: ' . \PHP_EOL;

    for ($i = 0; $i <= 1; $i++) {
        $type = $i === 0 ? 'fg' : 'bg';

        echo \PHP_EOL . ' Color cube:' . \PHP_EOL . \PHP_EOL;

        for ($green = 0; $green < 256; $green += 51) {
            for ($red = 0; $red < 256; $red += 51) {
                for ($blue = 0; $blue < 256; $blue += 51) {
                    echo $formatter->format("<{$type}_{$red};{$green};{$blue}>::</>");
                }

                echo "\x1b[0m ";
            }

            echo \PHP_EOL;
        }

        echo \PHP_EOL . ' Grayscale ramp:' . \PHP_EOL . \PHP_EOL;

        for ($gray = 8; $gray < 256; $gray += 10) {
            echo $formatter->format("<{$type}_{$gray};{$gray};{$gray}>::</>");
        }

        echo \PHP_EOL;
    }
}

echo \PHP_EOL;
