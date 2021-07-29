<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Console\Output;

/**
 * Just a bunch of ANSI colour codes,
 */
class Ansi
{
    // Reset,
    const STYLE_NONE                = '0';   
    const RESET                     = '0';   
    
    // Styles.
    const STYLE_INTENSITY_BRIGHT    = '1';      
    const STYLE_BOLD                = '1';      
    const STYLE_INTENSITY_FAINT     = '2';      
    const STYLE_ITALIC              = '3';      
    const STYLE_UNDERLINE           = '4';
    const STYLE_BLINK               = '5';
    const STYLE_BLINK_RAPID         = '6';
    const STYLE_NEGATIVE            = '7';
    const STYLE_CONCEAL             = '8';
    const STYLE_STRIKETHROUGH       = '9';
    const STYLE_INTENSITY_NORMAL    = '22';
    const STYLE_BOLD_OFF            = '22';
    const STYLE_ITALIC_OFF          = '23';
    const STYLE_UNDERLINE_OFF       = '24';
    const STYLE_STEADY              = '5';
    const STYLE_BLINK_OFF           = '5';
    const STYLE_POSITIVE            = '27';
    const STYLE_REVEAL              = '28';
    const STYLE_STRIKETHROUGH_OFF   = '29';

    // Foreground.
    const COLOR_FG_BLACK            = '30';
    const COLOR_FG_RED              = '31';
    const COLOR_FG_GREEN            = '32';
    const COLOR_FG_YELLOW           = '33';
    const COLOR_FG_BLUE             = '34';
    const COLOR_FG_PURPLE           = '35';
    const COLOR_FG_CYAN             = '36';
    const COLOR_FG_WHITE            = '37';
    const COLOR_FG_RESET            = '39';

    // Background.
    const COLOR_BG_BLACK            = '40';
    const COLOR_BG_RED              = '41';
    const COLOR_BG_GREEN            = '42';
    const COLOR_BG_YELLOW           = '43';
    const COLOR_BG_BLUE             = '44';
    const COLOR_BG_PURPLE           = '45';
    const COLOR_BG_CYAN             = '46';
    const COLOR_BG_WHITE            = '47';
    const COLOR_BG_RESET            = '49';

    // These are not widely supported.
    const STYLE_FRAMED                  = '51';
    const STYLE_ENCIRCLED               = '52';
    const STYLE_OVERLINED               = '53';
    const STYLE_FRAMED_ENCIRCLED_OFF    = '54';
    const STYLE_OVERLINED_OFF           = '55';

    // These are non-standard,
    const COLOR_FG_BLACK_BRIGHT         = '90';     
    const COLOR_FG_RED_BRIGHT           = '91';
    const COLOR_FG_GREEN_BRIGHT         = '92';
    const COLOR_FG_YELLOW_BRIGHT        = '93';
    const COLOR_FG_BLUE_BRIGHT          = '94';
    const COLOR_FG_PURPLE_BRIGHT        = '95';
    const COLOR_FG_CYAN_BRIGHT          = '96';
    const COLOR_FG_WHITE_BRIGHT         = '97';
    const COLOR_BG_BLACK_BRIGHT         = '100';

    const COLOR_BG_RED_BRIGHT           = '101';
    const COLOR_BG_GREEN_BRIGHT         = '102';
    const COLOR_BG_YELLOW_BRIGHT        = '103';
    const COLOR_BG_BLUE_BRIGHT          = '104';
    const COLOR_BG_PURPLE_BRIGHT        = '105';
    const COLOR_BG_CYAN_BRIGHT          = '106';
    const COLOR_BG_WHITE_BRIGHT         = '107';

    // Escape.
    const ESCAPE                        = "\033";

    /**
     * Print out one particular codeset.
     * 
     * @param   string      $codeset    Codeset to print.
     * @return  string                  Fill codeset.
     */
    public static function cs(string $codeset): string
    {
        return self::ESCAPE . '[' . $codeset . 'm';
    }

    /**
     * Returns true if the stream supports colorization.
     * 
     * @param   resource    $stream     Stream to check.
     * @return  bool 
     */
    public static function streamHasColourSupport($stream)
    {
        if (isset($_SERVER['NO_COLOR']) || false !== getenv('NO_COLOR')) {
            return false;
        }

        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            return (function_exists('sapi_windows_vt100_support')
                && @sapi_windows_vt100_support($stream))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        return stream_isatty($stream);
    }
}