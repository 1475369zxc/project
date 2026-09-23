<?php

namespace App\Service\Admin;

use App\Entity\Vacancy;

class VacancyCloner
{
    public function clone(Vacancy $vacancy): Vacancy
    {
        $new = new Vacancy();
        $new->setTitle($vacancy->getTitle() . ' (clone)');
        $new->setDescription($vacancy->getDescription());
        $new->setIsPublic(false);
        $new->setMaxProjects($vacancy->getMaxProjects());
        $new->setPhoto($vacancy->getPhoto());

        foreach ($vacancy->getAttributes() as $attribute) {
            $new->addAttribute($attribute);
        }

        return $new;
    }

    public function cloneMany(array $vacancies): array
    {
        $clones = [];
        foreach ($vacancies as $vacancy) {
            $clones[] = $this->clone($vacancy);
        }
        return $clones;
    }
}
