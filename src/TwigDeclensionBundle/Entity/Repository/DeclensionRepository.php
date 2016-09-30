<?php

namespace Bubnov\TwigDeclensionBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Bubnov\TwigDeclensionBundle\Entity\Declension;

/**
 * DeclensionRepository
 */
class DeclensionRepository extends EntityRepository
{
    /**
     * @param array $data
     * @return QueryBuilder
     */
    public function getListQB($data = [])
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->orderBy('d.infinitive', 'ASC')
        ;

        if (!is_array($data)) {
            return $qb;
        }
        
        foreach ($data as $name => $value) {
            if (empty($value)) {
                continue;
            }
            if ('needs_work' === $name) {
                foreach (Declension::$forms as $form) {
                    if ('plural' === $form) {
                        continue;
                    }
                    $qb->orWhere('d.' . $form . ' IS NULL');
                }
                continue;
            }
            $qb
                ->andWhere('d.' . $name . ' = :'. $name)
                ->setParameter(':'. $name, $value)
            ;
        }
        
        return $qb;
    }
    
    /**
     *
     * @param string $infinitive
     * @return Declension | null
     */
    public function findOneByInfinitive($infinitive = '')
    {
        return $this->findOneBy(['infinitive' => $infinitive]);
    }
}
