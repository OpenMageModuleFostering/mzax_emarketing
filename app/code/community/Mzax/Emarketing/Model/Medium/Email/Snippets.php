<?php
/**
 * Mzax Emarketing (www.mzax.de)
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this Extension in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @version     0.4.7
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * 
 * 
 *
 * @author Jacob Siefer
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version 0.4.7
 */
class Mzax_Emarketing_Model_Medium_Email_Snippets
{
    
    protected $_snippets = array();
    
    
    
    public function add(array $snippet)
    {
        if(!isset($snippet['value'])) {
            throw new Exception("No value property defined for snippet");
        }
        if(!isset($snippet['title'])) {
            throw new Exception("No title property defined for snippet");
        }
        if(!isset($snippet['snippet'])) {
            throw new Exception("No snippet property defined for snippet");
        }
        
        $this->_snippets[$snippet['value']] = $snippet;
        return $this;
    }
    
    
    
    public function addSnippets($value, $snippet, $title, $description = null, $shortcut = null)
    {
        return $this->add(array(
            'title'       => $title,
            'description' => $description,
            'snippet'     => $snippet,
            'value'       => $value,
            'shortcut'    => $shortcut
        ));
    }
    
    
    
    public function addVar($value, $title, $description = null, $shortcut = null)
    {
        return $this->addSnippets('mage.' . $value, '{{var ' . $value . '}}', $title, $description, $shortcut);
    }
    
    
    
    
    
    public function toArray()
    {
        $data = $this->_snippets;
        ksort($data);
        
        return array_values($data);
    }
    
    
    
}
