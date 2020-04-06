<?php
/**
 * @package php-svg-lib
 * @link    https://github.com/PhenX/php-svg-lib
 * @author  Fabien M�nager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ https://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Tag;

use Sabberworm\CSS;

class StyleTag extends AbstractTag
{
    protected $text = "";

    public function end()
    {
        $parser = new CSS\Parser($this->text);
        $this->document->appendStyleSheet($parser->parse());
    }

    public function appendText($text)
    {
        $this->text .= $text;
    }
} 