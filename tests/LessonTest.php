<?php

namespace App\Tests\Entity;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class LessonTest extends TestCase
{
    public function testSetName(): void
    {
        $lesson = new Lesson();
        $lesson->setName('Test Lesson');

        $this->assertEquals('Test Lesson', $lesson->getName());
    }

    public function testSetPrice(): void
    {
        $lesson = new Lesson();
        $lesson->setPrice(99.99);

        $this->assertEquals(99.99, $lesson->getPrice());
    }

    public function testSetCourse(): void
    {
        $lesson = new Lesson();
        $course = new Course();
        $lesson->setCourse($course);

        $this->assertSame($course, $lesson->getCourse());
    }

    public function testAddUser(): void
    {
        $lesson = new Lesson();
        $user = new User();

        $lesson->addUser($user);

        $this->assertCount(1, $lesson->getUsers());
        $this->assertTrue($lesson->getUsers()->contains($user));
    }

    public function testSetCreatedAt(): void
    {
        $lesson = new Lesson();
        $createdAt = new \DateTime('2025-02-06 12:00:00');
        $lesson->setCreatedAt($createdAt);

        $this->assertEquals($createdAt, $lesson->getCreatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $lesson = new Lesson();
        $updatedAt = new \DateTime('2025-02-06 12:00:00');
        $lesson->setUpdatedAt($updatedAt);

        $this->assertEquals($updatedAt, $lesson->getUpdatedAt());
    }

    public function testSetCreatedBy(): void
    {
        $lesson = new Lesson();
        $user = new User();
        $lesson->setCreatedBy($user);

        $this->assertSame($user, $lesson->getCreatedBy());
    }

    public function testSetUpdatedBy(): void
    {
        $lesson = new Lesson();
        $user = new User();
        $lesson->setUpdatedBy($user);

        $this->assertSame($user, $lesson->getUpdatedBy());
    }

    public function testGetType(): void
    {
        $lesson = new Lesson();

        $this->assertEquals('lesson', $lesson->getType());
    }

    public function testAddLessonToCourse(): void
    {
        $course = new Course();
        $lesson = new Lesson();

        $lesson->setCourse($course);

        $this->assertTrue($course->getLessons()->contains($lesson));
        $this->assertSame($lesson, $course->getLessons()->first());
    }

    public function testRemoveUser(): void
    {
        $lesson = new Lesson();
        $user = new User();

        $lesson->addUser($user);
        $this->assertCount(1, $lesson->getUsers());

        $lesson->removeUser($user);
        $this->assertCount(0, $lesson->getUsers());
    }
}
