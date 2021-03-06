<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends EntityRepository
{
    /**
     * Up pages position
     *
     * @param number $parentId
     * @param number $position
     * @param number $positionLimit
     */
    public function upPagesPosition($parentId, $position, $positionLimit = NULL)
    {
        if (is_null($positionLimit))
        {
            $query = $this->getEntityManager()->createQuery('UPDATE FulgurioLightCMSBundle:Page p SET p.position=p.position-1 WHERE p.position>=:position AND p.parent=:parentId AND p.page_type=:pageType');
        }
        else
        {
            $query = $this->getEntityManager()->createQuery('UPDATE FulgurioLightCMSBundle:Page p SET p.position=p.position-1 WHERE p.position>=:position AND p.position<=:positionLimit AND p.parent=:parentId AND p.page_type=:pageType');
            $query->setParameter('positionLimit', $positionLimit);
        }
        $query->setParameter('pageType', 'page');
        $query->setParameter('position', $position);
        $query->setParameter('parentId', $parentId);
        $query->getResult();
    }

    /**
     * Down pages position
     *
     * @param number $parentId
     * @param number $position
     * @param number $positionLimit
     */
    public function downPagesPosition($parentId, $position, $positionLimit = NULL)
    {
        if (is_null($positionLimit))
        {
            $query = $this->getEntityManager()->createQuery('UPDATE FulgurioLightCMSBundle:Page p SET p.position=p.position+1 WHERE p.position>=:position AND p.parent=:parentId AND p.page_type=:pageType');
        }
        else
        {
            $query = $this->getEntityManager()->createQuery('UPDATE FulgurioLightCMSBundle:Page p SET p.position=p.position+1 WHERE p.position>=:position AND p.position<=:positionLimit AND p.parent=:parentId AND p.page_type=:pageType');
            $query->setParameter('positionLimit', $positionLimit);
        }
        $query->setParameter('pageType', 'page');
        $query->setParameter('position', $position);
        $query->setParameter('parentId', $parentId);
        $query->getResult();
    }

    /**
     * Get next position in tree
     *
     * @param number $parentId
     * @return number
     */
    public function getNextPosition($parentId)
    {
        $query = $this->getEntityManager()->createQuery('SELECT MAX(p.position) FROM FulgurioLightCMSBundle:Page p WHERE p.parent=:parentId AND p.page_type=:pageType');
        $query->setParameter('pageType', 'page');
        $query->setParameter('parentId', $parentId);
        return $query->getSingleScalarResult() + 1;
    }
}