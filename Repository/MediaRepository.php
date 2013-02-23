<?php

namespace Fulgurio\LightCMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Paginator;

/**
 * MediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaRepository extends EntityRepository
{
    /**
     * Find media with pagination
     *
     * @param array $filters
     * @param integer $limit
     * @param integer $offset
     * @param boolean $resultInArray
     *
     * @return array
     */
    public function findAllWithPagination($filters, $limit, $offset, $resultInArray = FALSE)
    {
        $where = $this->getQueryFilter($filters);
        $query = $this->getEntityManager()->createQuery('SELECT m FROM FulgurioLightCMSBundle:Media m ' . $where . ' ORDER BY m.created_at DESC')->setMaxResults($limit)->setFirstResult($offset);
        if (!empty($filters))
        {
            foreach ($filters as $filterKey => $filterValue)
            {
                $query->setParameter($filterKey, $filterValue);
            }
        }
        if ($resultInArray)
        {
            return $query->getArrayResult();
        }
        return $query->getResult();
    }

    /**
     * Count number of result
     *
     * @param unknown $filters
     * @return Ambigous <\Doctrine\ORM\mixed, mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement>
     */
    public function count($filters)
    {
        $where = $this->getQueryFilter($filters);
        $query = $this->getEntityManager()->createQuery('SELECT COUNT(m) FROM FulgurioLightCMSBundle:Media m' . $where);
        if (!empty($filters))
        {
            foreach ($filters as $filterKey => $filterValue)
            {
                $query->setParameter($filterKey, $filterValue);
            }
        }
        return $query->getSingleScalarResult();
    }

    /**
     * Make query filter string, from given filters
     *
     * @param array $filters
     * @return string
     */
    private function getQueryFilter($filters)
    {
        $where = '';
        if (!empty($filters))
        {
            foreach ($filters as $filterKey => $filterValue)
            {
                $where .= ' AND m.' . $filterKey . ' LIKE :' . $filterKey;
            }
            $where = ' WHERE ' . substr($where, 4);
        }
        return ($where);
    }
}