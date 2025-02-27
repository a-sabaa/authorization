<?php
declare(strict_types = 1);
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authorization\Test\TestCase;

use Phauthentic\Authorization\AuthorizationService;
use Phauthentic\Authorization\IdentityDecorator;
use Phauthentic\Authorization\Policy\BeforePolicyInterface;
use Phauthentic\Authorization\Policy\Exception\MissingMethodException;
use Phauthentic\Authorization\Policy\MapResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TestApp\Model\Entity\Article;
use TestApp\Policy\ArticlePolicy;
use TestApp\Policy\MagicCallPolicy;

/**
 * AuthorizationServiceTest
 */
class AuthorizationServiceTest extends TestCase
{
    public function testNullUserCan(): void
    {
        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);

        $service = new AuthorizationService($resolver);

        $user = null;

        $result = $service->can($user, 'view', new Article);
        $this->assertFalse($result);

        $result = $service->can($user, 'view', new Article(['visibility' => 'public']));
        $this->assertTrue($result);
    }

    public function testCan(): void
    {
        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);

        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $result = $service->can($user, 'add', new Article);
        $this->assertTrue($result);
    }

    public function testAuthorizationCheckedWithCan(): void
    {
        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);
        $service = new AuthorizationService($resolver);
        $this->assertFalse($service->authorizationChecked());

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $service->can($user, 'add', new Article);
        $this->assertTrue($service->authorizationChecked());
    }

    public function testCallingMagicCallPolicy(): void
    {
        $resolver = new MapResolver([
            Article::class => MagicCallPolicy::class
        ]);
        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'id' => 9,
            'role' => 'admin'
        ]);

        $article = new Article();
        $this->assertTrue($service->can($user, 'doThat', $article));
        $this->assertFalse($service->can($user, 'cantDoThis', $article));
    }

    public function testAuthorizationCheckedWithApplyScope(): void
    {
        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);
        $service = new AuthorizationService($resolver);
        $this->assertFalse($service->authorizationChecked());

        $user = new IdentityDecorator($service, [
            'id' => 9,
            'role' => 'admin'
        ]);

        $service->applyScope($user, 'index', new Article);
        $this->assertTrue($service->authorizationChecked());
    }

    public function testSkipAuthorization(): void
    {
        $resolver = new MapResolver([]);
        $service = new AuthorizationService($resolver);
        $this->assertFalse($service->authorizationChecked());

        $service->skipAuthorization();
        $this->assertTrue($service->authorizationChecked());
    }

    public function testApplyScope(): void
    {
        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);
        $service = new AuthorizationService($resolver);
        $user = new IdentityDecorator($service, [
            'id' => 9,
            'role' => 'admin'
        ]);

        $article = new Article();
        $result = $service->applyScope($user, 'index', $article);
        $this->assertSame($article, $result);
        $this->assertSame($article->user_id, $user->getOriginalData()['id']);
    }

    public function testApplyScopeMethodMissing(): void
    {
        $this->expectException(MissingMethodException::class);

        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);
        $service = new AuthorizationService($resolver);
        $user = new IdentityDecorator($service, [
            'id' => 9,
            'role' => 'admin'
        ]);

        $article = new Article();
        $result = $service->applyScope($user, 'nope', $article);
    }

    public function testBeforeFalse(): void
    {
        $entity = new Article();

        $policy = $this->getMockBuilder(BeforePolicyInterface::class)
            ->setMethods(['before', 'canAdd'])
            ->getMock();

        $policy->expects($this->once())
            ->method('before')
            ->with($this->isInstanceOf(IdentityDecorator::class), $entity, 'add')
            ->willReturn(false);

        $resolver = new MapResolver([
            Article::class => $policy
        ]);

        $policy->expects($this->never())
            ->method('canAdd');

        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $result = $service->can($user, 'add', $entity);
        $this->assertFalse($result);
    }

    public function testBeforeTrue(): void
    {
        $entity = new Article();

        $policy = $this->getMockBuilder(BeforePolicyInterface::class)
            ->setMethods(['before', 'canAdd'])
            ->getMock();

        $policy->expects($this->once())
            ->method('before')
            ->with($this->isInstanceOf(IdentityDecorator::class), $entity, 'add')
            ->willReturn(true);

        $policy->expects($this->never())
            ->method('canAdd');

        $resolver = new MapResolver([
            Article::class => $policy
        ]);

        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $result = $service->can($user, 'add', $entity);
        $this->assertTrue($result);
    }

    public function testBeforeNull(): void
    {
        $entity = new Article();

        $policy = $this->getMockBuilder(BeforePolicyInterface::class)
            ->setMethods(['before', 'canAdd'])
            ->getMock();

        $policy->expects($this->once())
            ->method('before')
            ->with($this->isInstanceOf(IdentityDecorator::class), $entity, 'add')
            ->willReturn(null);

        $policy->expects($this->once())
            ->method('canAdd')
            ->with($this->isInstanceOf(IdentityDecorator::class), $entity)
            ->willReturn(true);

        $resolver = new MapResolver([
            Article::class => $policy
        ]);

        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $result = $service->can($user, 'add', $entity);
        $this->assertTrue($result);
    }

    public function testMissingMethod(): void
    {
        $entity = new Article();

        $resolver = new MapResolver([
            Article::class => ArticlePolicy::class
        ]);

        $service = new AuthorizationService($resolver);

        $user = new IdentityDecorator($service, [
            'role' => 'admin'
        ]);

        $this->expectException(MissingMethodException::class);
        $this->expectExceptionMessage('Method `canDisable` for invoking action `disable` has not been defined in `TestApp\Policy\ArticlePolicy`.');

        $service->can($user, 'disable', $entity);
    }
}
