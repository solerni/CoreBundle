<?php

namespace Claroline\CoreBundle\Entity\Resource;

use Claroline\CoreBundle\Entity\IndexableInterface;
use Claroline\CoreBundle\Entity\IndexableTrait;
use Claroline\CoreBundle\Entity\Resource\AbstractResource;

/**
 * Extend from this class to make your resource indexable 
 *
 */

abstract class AbstractIndexableResource extends AbstractResource implements IndexableInterface
{
   
    use IndexableTrait;
    
    public function fillIndexableDocument(&$doc)
    {
        $doc->id = $this->getIndexableDocId();
        $doc->entity_id = $this->getId();
        $doc->name = $this->getResourceNode()->getName();
        $doc->resource_id = $this->getId();
        $doc->wks_id = $this->getResourceNode()->getWorkspace()->getId();
        $doc->creation_date = $this->getResourceNode()->getCreationDate();
        $doc->modification_date = $this->getResourceNode()->getModificationDate();
        $doc->type_name = $this->getResourceNode()->getResourceType()->getName(); 
        $doc->owner_id = $this->getResourceNode()->getCreator()->getId();
        $doc->owner_name = $this->getResourceNode()->getCreator()->getFirstName() . ' ' .
                           $this->getResourceNode()->getCreator()->getLastName();

        return $doc;
    }

}
