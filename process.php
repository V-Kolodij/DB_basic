<?php

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\QueryBuilder;

$route = $request->query->get('route');
$id = $request->query->get('id');
$page = $request->query->get('page', 1);
$sortName = $request->query->get('sort-name');
$searchName = $request->query->get('search-name');
$formHandler = new FormHandler($entityManager);

if ($route === 'create') {
    include 'view/create.php';
} else {
    if (empty($id)) {
        throw new EntityNotFoundException();
    }
    if ($route === 'delete') {
        $formHandler->deleteItem($id);
        renderIndex($entityManager, $page, $sortName, $searchName, $formHandler);
    } elseif ($route === 'update') {
        $product = $formHandler->findById($id);
        include 'view/update.php';
    }
}

if ($route == '') {
    $name = $request->get('name');
    $id = $request->get('id');

    if ($name && null === $id) {
        $formHandler->createItem($name);
    }
    if ($name && $id) {
        $formHandler->updateItem($name, $id);
    }
    renderIndex($entityManager, $page, $sortName, $searchName, $formHandler);
}

/**
 * @param $entityManager
 * @param $page
 * @param $sortName
 * @param $searchName
 * @param $formHandler
 */
function renderIndex($entityManager, $page, $sortName, $searchName, $formHandler)
{
    $qb = new QueryBuilder($entityManager);

    $qb->add('select', 'u')
        ->add('from', 'Product u')
        ->setFirstResult(($page - 1) * 5)
        ->setMaxResults(5);

    if (!empty($sortName)) {
        $formHandler->sortByName($qb, $sortName);
    }
    if (!empty($searchName)) {
        $formHandler->findByName($qb, $searchName);
    }

    $query = $qb->getQuery();
    $products = $query->getResult();

    $count = $formHandler->getCountProduct($searchName, $entityManager);
    include 'view/index.php';
}
