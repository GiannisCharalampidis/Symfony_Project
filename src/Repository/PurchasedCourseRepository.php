<?php
// src/Repository/PurchasedCourseRepository.php
namespace App\Repository;

use App\Entity\PurchasedCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurchasedCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasedCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasedCourse[]    findAll()
 * @method PurchasedCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasedCourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasedCourse::class);
    }
}