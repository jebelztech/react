<?php

namespace Leftor\Eps\Block\Form;

/**
 * Block for Bank Transfer payment method form
 */
class Insturctions extends \Leftor\Eps\Block\Form\AbstractInstruction
{
    /**
     * Bank transfer template
     *
     * @var string
     */
    protected $_template = 'form/instructions.phtml';
}
