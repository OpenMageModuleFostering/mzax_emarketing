<?php
/*
 * NOTICE:
 * This code has been slightly altered by the Mzax_Emarketing module to use old php namespaces.
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Symfony\Component\CssSelector\Parser\Shortcut;

#use Symfony\Component\CssSelector\Node\ElementNode;
#use Symfony\Component\CssSelector\Node\SelectorNode;
#use Symfony\Component\CssSelector\Parser\ParserInterface;

/**
 * CSS selector class parser shortcut.
 *
 * This shortcut ensure compatibility with previous version.
 * - The parser fails to parse an empty string.
 * - In the previous version, an empty string matches each tags.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Symfony_Component_CssSelector_Parser_Shortcut_EmptyStringParser implements Symfony_Component_CssSelector_Parser_ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        // Matches an empty string
        if ($source == '') {
            return array(new Symfony_Component_CssSelector_Node_SelectorNode(new Symfony_Component_CssSelector_Node_ElementNode(null, '*')));
        }

        return array();
    }
}
