<?php
/**
 * @package dompdf
 * @link    https://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license https://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Dompdf\FrameReflower;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;

/**
 * Dummy reflower
 *
 * @package dompdf
 */
class NullFrameReflower extends AbstractFrameReflower
{

    function __construct(Frame $frame)
    {
        parent::__construct($frame);
    }

    function reflow(BlockFrameDecorator $block = null)
    {
        return;
    }

}
