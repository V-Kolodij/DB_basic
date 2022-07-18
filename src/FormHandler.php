<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;


class FormHandler
{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteItem($id)
    {
        $product = $this->findById($id);
        if (!empty($product)) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        }
    }

    /**
     * @param $name
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createItem($name)
    {
        $product = new Product();
        $product->setName($name);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * @param $name
     * @param $id
     * @throws ORMException
     * @throws OptimisticLockException
     */
    function updateItem($name, $id)
    {
        $product = $this->findById($id);
        $product->setName($name);
        $this->entityManager->flush();
    }

    /**
     * @param $query
     * @param $searchName
     *
     * @return mixed
     */
    protected function findByName($query, $searchName)
    {
        return $query->andWhere('u.name LIKE :name')
            ->setParameter('name', $searchName);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findById($id)
    {
        $productRepository = $this->entityManager->getRepository('Product');

        return $productRepository->find($id);
    }

    /**
     * @param $searchName
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountProduct($searchName)
    {
        $repository = $this->entityManager->getRepository('Product');

        $countQuery = $repository->createQueryBuilder('u')
            ->select('count(u.id)');
        if (!empty($searchName)) {
            $this->findByName($countQuery, $searchName);
        }

        return $countQuery->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $query
     * @param $sortName
     *
     * @return mixed
     */
    public function sortByName($query, $sortName)
    {
        if ($sortName === 'asc') {
            $query = $query->orderBy('u.name', 'ASC');
        } elseif ($sortName === 'desc') {
            $query = $query->orderBy('u.name', 'DESC');
        }

        return $query;
    }

}
