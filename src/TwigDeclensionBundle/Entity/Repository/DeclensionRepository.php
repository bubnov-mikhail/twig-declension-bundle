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
     * @param array $formData
     * @return QueryBuilder
     */
    public function getListQB($formData = [])
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->orderBy('d.infinitive', 'ASC')
        ;
        
        foreach($formData as $name => $value){
            if(empty($value)){
                continue;
            }
            if($name === 'needs_work'){
                foreach(Declension::$forms as $form){
                    if($form === 'plural'){
                        continue;
                    }
                    $qb
                        ->orWhere('d.' . $form . ' IS NULL')
                    ;
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
     * @param type $infinitive
     */
    public function findOneByInfinitive($infinitive = '')
    {
        return $this->findOneBy(['infinitive' => $infinitive]);
    }
}
