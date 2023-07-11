<?php

namespace App\Repository;

use App\Entity\Bovinos;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Bovinos>
 *
 * @method Bovinos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bovinos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bovinos[]    findAll()
 * @method Bovinos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BovinosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bovinos::class);
    }

    public function save(Bovinos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bovinos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTodosOrdenadosPeloAniversario()
    {
        $query = $this->createQueryBuilder('b')
        ->orderBy('b.data_nascimento', 'ASC')
        ->getQuery();

        return $query->getResult();

    }

    // Leite Produzido Semanal

    public function somaLeite(): float
    {
        $query = $this->createQueryBuilder('b')
        ->select('SUM(b.leite)')
        ->where('b.data_abatimento IS NULL')
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    public function mediaLeite() : float
    {
        $query = $this->createQueryBuilder('b')
        ->select('AVG(b.leite)')
        ->where('b.data_abatimento IS NULL')
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    // Ração Consumida Semanal

    public function somaRacao(): float
    {
        $query = $this->createQueryBuilder('b')
        ->select('SUM(b.racao)')
        ->where('b.data_abatimento IS NULL')
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    public function mediaRacao() : float
    {
        $query = $this->createQueryBuilder('b')
        ->select('AVG(b.racao)')
        ->where('b.data_abatimento IS NULL')
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    // Regras de Negócio dos Bovinos

    public function countBovinoQuantia(): int
    {
        $query = $this->createQueryBuilder('b')
        ->select('COUNT(b.id)')
        ->where('b.data_nascimento IS NOT NULL')
       // ->setParameter('minIdade', new DateTime('-1 year'))
        ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function avgMediaQuantiaLeite(): float
    {
        $query = $this ->createQueryBuilder('b')
        ->select('AVG(b.leite)')
        ->where('b.data_nascimento IS NOT NULL')
        //->setParameter('minIdade', new DateTime('-1 year'))
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    public function avgMediaQuantiaRacao(): float
    {
        $query = $this->createQueryBuilder('b')
        ->select('AVG(b.racao)')
        ->where('b.data_nascimento IS NOT NULL')
        //->setParameter('minIdade', new DateTime('-1 year'))
        ->getQuery();

        return (float) $query->getSingleScalarResult();
    }


    public function findPossibilidadedeAbate()
    {
        return $this->createQueryBuilder('b')
            ->where('b.data_nascimento IS NOT NULL ')
            ->andWhere('b.data_abatimento IS  NULL ')
            ->andWhere('b.leite < 40 or b.data_nascimento <= :data or (b.peso/15) > 18 or (b.leite < 70 and b.racao > (50/7))')
            ->setParameter('data', date('Y-m-d', strtotime('-5 year')))
            ->orderBy('b.id')
            ->getQuery()
            ->getResult();
    }

    // Abatidos
    public function countBovinosAbatidos(): int
    {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.data_abatimento IS NOT NULL')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findByDataAbate()
    {
        $query = $this->createQueryBuilder('b')
            ->where('b.data_abatimento IS NOT NULL')
            ->orderBy('b.data_abatimento', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function sumLeiteBovinosAbatidos(): float
    {
        $query = $this->createQueryBuilder('b')
            ->select('SUM(b.leite)')
            ->where('b.data_abatimento IS NOT NULL')
            ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

    public function sumRacaoBovinosAbatidos(): float
    {
        $query = $this->createQueryBuilder('b')
            ->select('SUM(b.racao)')
            ->where('b.data_abatimento IS NOT NULL')
            ->getQuery();

        return (float) $query->getSingleScalarResult();
    }

}
//    /**
//     * @return Bovinos[] Returns an array of Bovinos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bovinos
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }