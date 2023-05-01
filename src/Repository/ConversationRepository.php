<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Conversation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function add(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Conversation[] Returns an array of Conversation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conversation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByParticipants(array $participants, User $currentUser): ?Conversation
    {
        // chek if conversation exist between participants in ^participants array and current user 
        // if exist return conversation else return null
        $qb = $this->createQueryBuilder('c');

        $qb->select('c')
            ->innerJoin('c.users', 'u')
            // filtre les conversations auxquelles les participants passés en paramètre sont associés.
            ->where('u IN (:participants)')
            // vérifie si l'utilisateur en session est aussi dans la liste des participants
            ->andWhere(':currentUser MEMBER OF c.users')
            // ajoute une contrainte pour ne récupérer que les conversations actives (avec un status = 1):
            ->andWhere('c.status = 1')
            // groupe les résultats par l'id de la conversation:
            ->groupBy('c.id')
            // ajoute une contrainte pour s'assurer que la conversation a exactement le nombre de participants passé en paramètre
            ->having('COUNT(c.id) = :count')
            // on passe les valeurs aux :paramètres
            ->setParameter('participants', $participants)
            ->setParameter('count', count($participants))
            ->setParameter('currentUser', $currentUser);

        $result = $qb->getQuery()->getResult();

        // si il y a un résultat, on retourne la première conversation trouvée
        // théoriquement il ne peut y avoir qu'un seul résultat c-a-d une seule conversation
        if ($result) {
            return $result[0];
        }

        // sinon on retourne null pour autoriser la création d'une nouvelle conversation
        return null;
        
    }
}
