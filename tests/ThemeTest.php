<?php

namespace App\Tests\Entity;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    public function testSetName(): void
    {
        $theme = new Theme();
        $theme->setName('Science');

        $this->assertEquals('Science', $theme->getName(), 'The theme name should be set correctly.');
    }

    public function testAddCourse(): void
    {
        $theme = new Theme();
        $course = new Course();
        $theme->addCourse($course);

        $this->assertCount(1, $theme->getCourses(), 'The theme should have one course.');
        $this->assertSame($theme, $course->getTheme(), 'The course should be associated with the theme.');
    }

    public function testRemoveCourse(): void
    {
        $theme = new Theme();
        $course = new Course();
        $theme->addCourse($course);

        $theme->removeCourse($course);

        $this->assertCount(0, $theme->getCourses(), 'The theme should have no courses.');
        $this->assertNull($course->getTheme(), 'The course should no longer be associated with the theme.');
    }

    public function testCreatedAndUpdatedAt(): void
    {
        $theme = new Theme();
        $createdAt = $theme->getCreatedAt();
        $updatedAt = $theme->getUpdatedAt();

        $this->assertInstanceOf(\DateTime::class, $createdAt, 'Created at should be an instance of DateTime.');
        $this->assertInstanceOf(\DateTime::class, $updatedAt, 'Updated at should be an instance of DateTime.');

        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $updatedAt->format('Y-m-d H:i:s'), 'Created at and Updated at should initially be the same.');
    }


    public function testSetCreatedBy(): void
    {
        $theme = new Theme();
        $user = new User();
        $theme->setCreatedBy($user);

        $this->assertSame($user, $theme->getCreatedBy(), 'The createdBy user should be set correctly.');
    }

    public function testSetUpdatedBy(): void
    {
        $theme = new Theme();
        $user = new User();
        $theme->setUpdatedBy($user);

        $this->assertSame($user, $theme->getUpdatedBy(), 'The updatedBy user should be set correctly.');
    }


    public function testGetCoursesInitiallyEmpty(): void
    {
        $theme = new Theme();

        $this->assertCount(0, $theme->getCourses(), 'The theme should have no courses initially.');
    }

    public function testId(): void
    {
        $theme = new Theme();
        $this->assertNull($theme->getId(), 'The theme should not have an ID until persisted.');
    }
}
