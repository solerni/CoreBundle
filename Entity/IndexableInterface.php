<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Entity;

/**
 * Implement this interface to make your entites indexable 
 *
 */
interface IndexableInterface
{
    
    /**
     * @param StdObject $doc document to fill
     * 
     * @return StdObject filled Document
     */
    public function fillIndexableDocument(&$doc);

    
    /*
     * @return string return uniq document id
     */
    public function getIndexableDocId();
}
