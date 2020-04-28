<?php

namespace App\DataFixtures;

use App\Entity\QuestionFormat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getQuestionFormats() as $code => $name) {
            $format = new QuestionFormat();
            $format->setName($name);
            $format->setCode($code);

            $manager->persist($format);
        }

        $manager->flush();
    }

    private function getQuestionFormats(): array
    {
        return [
            "CHECKBOX" => "checkbox",
            "RADIO" => "radio",
            "TEXT" => "text"
        ];
    }
}
