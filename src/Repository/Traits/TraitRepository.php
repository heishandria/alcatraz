<?php

namespace App\Repository\Traits;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait TraitRepository
{
    /**
     * @param array|null $criteria
     * @param $limit
     * @return Array|null
     */
    public function getAll(?Array $criteria, $limit): ?Array
    {
        $qb = $this->createQueryBuilder('data');

        foreach ($criteria as $key => $value) {
            if ($key === 'active') {
                $qb->andWhere('data.' . $key . ' like :' . $key)->setParameter($key, '%' . $value . '%');
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * getLatest
     *
     * @param int $maxCount
     * @param int $offset
     *
     * @return Paginator|array|User[]
     */
    public function getLatest(int $maxCount = 20, $offset = 0)
    {
        $qb = $this->createQueryBuilder('t');

        $qb->setMaxResults($maxCount)
            ->setFirstResult($offset);

        $qb->addOrderBy('t.updated', 'desc')
            ->addOrderBy('t.created', 'desc');

        return new Paginator($qb);
    }
}