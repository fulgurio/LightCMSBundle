<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fulgurio\LightCMSBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class PageFilter extends SQLFilter
{
    /**
     * (non-PHPdoc)
     * @see Doctrine\ORM\Query\Filter\SQLFilte::addFilterConstraint()
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the Page entity
        if ($targetEntity->reflClass->getName() !== 'Fulgurio\LightCMSBundle\Entity\Page')
        {
            return '';
        }
        return $targetTableAlias . '.status = ' . $this->getParameter('status');
    }
}
