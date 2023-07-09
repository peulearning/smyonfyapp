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

    public function enviarAbate(): array
    {
        $query = $this->createQueryBuilder('b')
        ->select('b.id')
        ->orderBy('b.data_nascimento', 'ASC')
        ->addSelect('
            (CASE
                WHEN b.data_nascimento <= : minIdade OR
                b.leite < :minLeite OR
                (b.leite < :minLeite2 and (b.quantia_semanal / 7) > :minQuantiaDia) OR
                (b.peso / 15) > :minPeso
                THEN 1
                ELSE 0
            END) as conditions'
        )
            ->setParameter('minIdade', new DateTime('-5 year'))
            ->setParameter('minLeite', 40)
            ->setParameter('minLeite2', 70)
            ->setParameter('minQuantiaDia', 50)
            ->setParameter('minPeso', 18)
            ->getQuery();

            return $query->getArrayResult();
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

    public function findByDataMaximaAbate()
    {
        $query = $this->createQueryBuilder('b')
            ->select('MAX(b.data_nascimento)')
            ->where('b.data_abatimento IS NOT NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findByDataMininaAbate()
    {
        $query = $this->createQueryBuilder('b')
            ->select('MIN(b.data_nascimento)')
            ->where('b.data_abatimento IS NOT NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
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