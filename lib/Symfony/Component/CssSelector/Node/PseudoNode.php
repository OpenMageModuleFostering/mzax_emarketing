<?php
/*
 * NOTICE:
 * This code has been slightly altered by Jacob Siefer to use old php namespaces.
 */
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Symfony\Component\CssSelector\Node;

/**
 * Represents a "<selector>:<identifier>" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class Symfony_Component_CssSelector_Node_PseudoNode extends Symfony_Component_CssSelector_Node_AbstractNode
{
    /**
     * @var Symfony_Component_CssSelector_Node_NodeInterface
     */
    private $selector;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @param Symfony_Component_CssSelector_Node_NodeInterface $selector
     * @param string        $identifier
     */
    public function __construct(Symfony_Component_CssSelector_Node_NodeInterface $selector, $identifier)
    {
        $this->selector = $selector;
        $this->identifier = strtolower($identifier);
    }

    /**
     * @return Symfony_Component_CssSelector_Node_NodeInterface
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecificity()
    {
        return $this->selector->getSpecificity()->plus(new Symfony_Component_CssSelector_Node_Specificity(0, 1, 0));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s:%s]', $this->getNodeName(), $this->selector, $this->identifier);
    }
}
