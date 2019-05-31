<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TimeTravelFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        return sprintf('%s.sys_period @> %s::timestamptz', $targetTableAlias, $this->getParameter('timetravel'));
    }
}