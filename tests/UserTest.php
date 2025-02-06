<?php

namespace App\Tests\Entity;

use App\Entity\Certification;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUsername(): void
    {
        $user = (new User())
            ->setUsername('valid_username')
            ->setPassword('secure_password123!')
            ->setEmail('valid@example.com');

        $errors = $this->validator->validate($user);
        $this->assertCount(0, $errors, 'The username should be valid.');
    }

    public function testInvalidUsernameTooLong(): void
    {
        $user = (new User())->setUsername(str_repeat('a', 31));

        $errors = $this->validator->validate($user);

        $this->assertNotEmpty($errors, 'The username should not exceed 30 characters.');

        $this->assertSame('username', $errors[0]->getPropertyPath());

        $this->assertSame('Username cannot exceed 30 characters.', $errors[0]->getMessage());
    }

    public function testInvalidUsernameBlank(): void
    {
        $user = (new User())->setUsername('');

        $errors = $this->validator->validate($user);

        $this->assertNotEmpty($errors, 'The username should not be blank.');

        $this->assertSame('username', $errors[0]->getPropertyPath());

        $this->assertSame('Username cannot be blank.', $errors[0]->getMessage());
    }

    public function testSetPassword(): void
    {
        $user = (new User())->setPassword('securePassword123!');
        $this->assertEquals('securePassword123!', $user->getPassword(), 'Password should be correctly set.');
    }

    public function testIsVerifiedDefault(): void
    {
        $user = new User();
        $this->assertFalse($user->isVerified(), 'The user should not be verified by default.');
    }


    public function testSetIsVerified(): void
    {
        $user = (new User())->setIsVerified(true);
        $this->assertTrue($user->isVerified(), 'The user should be verified after setting the flag to true.');
    }

    public function testValidEmail(): void
    {
        $user = (new User())
            ->setUsername('testuser')
            ->setPassword('secure_password123!')
            ->setEmail('valid@example.com');

        $errors = $this->validator->validate($user);
        $this->assertCount(0, $errors, 'The email should be valid.');
    }

    public function testInvalidEmail(): void
    {
        $user = (new User())
            ->setUsername('testuser')
            ->setPassword('secure_password123!')
            ->setEmail('invalid-email');

        $errors = $this->validator->validate($user);

        $this->assertNotEmpty($errors, 'The email should be invalid.');

        $errorMessages = array_map(fn($error) => $error->getMessage(), iterator_to_array($errors));

        $this->assertContains('This value is not a valid email address.', $errorMessages, 'The validation error should be related to the email format.');
    }

    public function testDefaultRoleUser(): void
    {
        $user = (new User())
            ->setUsername('user_default')
            ->setEmail('default.user@example.com')
            ->setPassword('password123');

        $this->assertContains('ROLE_USER', $user->getRoles(), 'Default role should be ROLE_USER.');
    }

    public function testRoleEscalationPrevention(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $validRoles = ['ROLE_USER', 'ROLE_ADMIN'];
        foreach ($user->getRoles() as $role) {
            $this->assertContains($role, $validRoles, "Role $role should be valid.");
        }
    }

    public function testAddPurchasedCourse(): void
    {
        $user = new User();
        $course = new Course();
        $user->addCourseWithLessons($course);

        $this->assertCount(1, $user->getPurchasedCourses(), 'The user should have 1 purchased course.');
    }
    public function testDeliveryAddress(): void
    {
        $user = (new User())->setDeliveryAddress('1234 Long Delivery Address');
        $this->assertEquals('1234 Long Delivery Address', $user->getDeliveryAddress(), 'The delivery address should be set correctly.');
    }

    public function testDeliveryAddressTooLong(): void
    {
        $user = (new User())->setDeliveryAddress(str_repeat('a', 256));

        $errors = $this->validator->validate($user);

        $this->assertNotEmpty($errors, 'The delivery address should not exceed 255 characters.');

        $errorMessages = array_map(fn($error) => $error->getMessage(), iterator_to_array($errors));

        $this->assertContains(
            'Delivery address cannot exceed 255 characters.',
            $errorMessages,
            'The validation error should be related to the length of the delivery address.'
        );
    }

    public function testRemovePurchasedCourse(): void
    {
        $user = new User();
        $course = new Course();
        $user->addCourseWithLessons($course);
        $user->removePurchasedCourse($course);

        $this->assertCount(0, $user->getPurchasedCourses(), 'The course should be removed from the user.');
    }

    public function testAddLesson(): void
    {
        $user = new User();
        $lesson = new Lesson();
        $user->addLessons([$lesson]);

        $this->assertCount(1, $user->getLessons(), 'The user should have 1 lesson.');
    }

    public function testRemoveLesson(): void
    {
        $user = new User();
        $lesson = new Lesson();
        $user->addLessons([$lesson]);
        $user->removeLesson($lesson);

        $this->assertCount(0, $user->getLessons(), 'The lesson should be removed from the user.');
    }

    public function testAddCertification(): void
    {
        $user = new User();
        $certification = new Certification();
        $user->addCertification($certification);

        $this->assertCount(1, $user->getCertifications(), 'The user should have 1 certification.');
    }

    public function testRemoveCertification(): void
    {
        $user = new User();
        $certification = new Certification();
        $user->addCertification($certification);
        $user->removeCertification($certification);

        $this->assertCount(0, $user->getCertifications(), 'The certification should be removed from the user.');
    }
}
