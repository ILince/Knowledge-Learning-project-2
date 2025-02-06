<?php

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase
{
    public function testSetName(): void
    {
        $course = new Course();
        $course->setName('PHP Basics');

        $this->assertEquals('PHP Basics', $course->getName());
    }

    public function testSetPrice(): void
    {
        $course = new Course();
        $course->setPrice(100.50);

        $this->assertEquals(100.50, $course->getPrice());
    }

    public function testSetTheme(): void
    {
        $course = new Course();
        $theme = new Theme();
        $course->setTheme($theme);

        $this->assertSame($theme, $course->getTheme());
    }

    public function testAddLesson(): void
    {
        $course = new Course();
        $lesson = new Lesson();
        $course->addLesson($lesson);

        $this->assertCount(1, $course->getLessons());
    }

    public function testRemoveLesson(): void
    {
        $course = new Course();
        $lesson = new Lesson();
        $course->addLesson($lesson);

        $course->removeLesson($lesson);

        $this->assertCount(0, $course->getLessons());
    }

    public function testGetType(): void
    {
        $course = new Course();
        $this->assertEquals('course', $course->getType());
    }

    public function testCreatedAndUpdatedAt(): void
    {
        $course = new Course();
        $createdAt = $course->getCreatedAt();
        $updatedAt = $course->getUpdatedAt();

        $this->assertInstanceOf(\DateTime::class, $createdAt);
        $this->assertInstanceOf(\DateTime::class, $updatedAt);
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $updatedAt->format('Y-m-d H:i:s'));
    }

    public function testSetCreatedBy(): void
    {
        $course = new Course();
        $user = new User();
        $course->setCreatedBy($user);

        $this->assertSame($user, $course->getCreatedBy());
    }

    public function testSetUpdatedBy(): void
    {
        $course = new Course();
        $user = new User();
        $course->setUpdatedBy($user);

        $this->assertSame($user, $course->getUpdatedBy());
    }
}
