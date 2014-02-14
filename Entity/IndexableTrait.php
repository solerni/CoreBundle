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

/*
 * Reusable code in indexable classes 
 */
trait IndexableTrait
{

    public function getIndexableDocId()
    {
        return base64_encode(get_class($this) . ':' . $this->getId());
    }

}